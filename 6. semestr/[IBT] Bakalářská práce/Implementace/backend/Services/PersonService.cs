/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    PersonService.cs                                      */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Services.Base;
using Services.Exceptions;
using Services.Interfaces;
using Services.Interfaces.RelationshipInterfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services
{
    public class PersonService : BaseService<Person>, IPersonService
    {
        public PersonService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region Methods for getting specific person(s)

        public async Task<Person> GetPersonByIdAsync(int Id)
        {
            var result = await _serviceDependencies._context.Persons
                                                            .AsNoTracking()
                                                            .Include(person => person.PersonNames)
                                                            .Include(person => person.OriginalRecords)
                                                            .Include(person => person.AncestorOrHusbandRelationship)
                                                            .ThenInclude(rel => rel.FamilyTreeRelationship)
                                                            .ThenInclude(fr => fr.FamilyTree)
                                                            .Include(person => person.DescendantOrWifeRelationship)
                                                            .ThenInclude(rel => rel.FamilyTreeRelationship)
                                                            .ThenInclude(fr => fr.FamilyTree)
                                                            .FirstOrDefaultAsync(person => person.Id == Id);
            if (result == null)
                throw new ObjectNotFoundException(nameof(Entity.Person), Id);

            return result;
        }

        public async Task<IEnumerable<Person>> GetSimiliarPersonsAsync(Person personToSearch)
        {
            List<PersonName> nameList = new List<PersonName>();
            List<int> personIdList = new List<int>();

            // Check if firstname & lastname is present
            if (!(personToSearch.PersonNames.Any(name => name.isFirstName) && personToSearch.PersonNames.Any(name => !name.isFirstName)))
            {
                throw new InvalidObjectException("Cannot get similiar person. Lastname or Firstname is empty and is required", nameof(Entity.Person), personToSearch.Id);
            }

            // Get all names that fulfill requirements
            foreach (var personName in personToSearch.PersonNames)
            {
                var nameResult = await _serviceDependencies._context.PersonNames
                                                                    .Where(personname => (personname.Name == personName.Name)
                                                                       && (personname.isFirstName == personName.isFirstName))
                                                                    .ToListAsync();

                nameList.AddRange(nameResult);
            }

            // Get only Firstname & Lastname valid combinations
            foreach (var firstName in nameList)
            {
                if (firstName.isFirstName)
                {
                    foreach (var lastName in nameList)
                    {
                        if (lastName.isFirstName == false && (firstName.PersonId == lastName.PersonId))
                        {
                            personIdList.Add(lastName.PersonId);
                        }
                    }
                }
            }

            // If found the same person as the passed parameter, remove it
            if (personIdList.Contains(personToSearch.Id))
                personIdList.Remove(personToSearch.Id);

            // Persons who has the same firstname & lastname as personToSearch
            var validPersons = await _serviceDependencies._context.Persons.Where(person => (personIdList.Contains(person.Id))
                                                                             && (person.IsFemale == personToSearch.IsFemale)
                                                                             && (person.IsPrivate == false))
                                                                           .Include(person => person.FamilyTreePerson)
                                                                           .Include(person => person.PersonNames)
                                                                           .Include(person => person.OriginalRecords)
                                                                           .AsNoTracking()
                                                                           .ToListAsync();


            foreach (var person in validPersons.ToList())
            {
                // Person already exists in selected FamilyTree
                if ((personToSearch.FamilyTreePerson.Count() != 0) && person.FamilyTreePerson.FirstOrDefault(fp => fp.FamilyTreeId == personToSearch.FamilyTreePerson.First().FamilyTreeId) != null)
                {
                    validPersons.Remove(person);
                    continue;
                }

                // Compare dates of birth
                if (personToSearch.BirthDate != null && person.BirthDate != null)
                {
                    if (!AreDatesSubtractLessThanYear((DateTime)personToSearch.BirthDate, (DateTime)person.BirthDate))
                    {
                        validPersons.Remove(person);
                        continue;
                    }
                }
                // Compare dates of baptism
                if (personToSearch.BaptismDate != null && person.BaptismDate != null)
                {
                    if (!AreDatesSubtractLessThanYear((DateTime)personToSearch.BaptismDate, (DateTime)person.BaptismDate))
                    {
                        validPersons.Remove(person);
                        continue;
                    }
                }
                // Compare dates of death
                if (personToSearch.DeathDate != null && person.DeathDate != null)
                {
                    if (!AreDatesSubtractLessThanYear((DateTime)personToSearch.DeathDate, (DateTime)person.DeathDate))
                    {
                        validPersons.Remove(person);
                        continue;
                    }
                }
                // Compare places of birth
                if (personToSearch.BirthPlace != null && person.BirthPlace != null)
                {
                    if (personToSearch.BirthPlace != person.BirthPlace)
                    {
                        validPersons.Remove(person);
                        continue;
                    }
                }

                // Extract only references on public familytrees
                var publicTreeReferences = await GetPublicFamilyTreesByPersonIdAsync(person.Id, 0);
                person.FamilyTreePerson = person.FamilyTreePerson.Where(fp => publicTreeReferences.Contains(fp.FamilyTreeId)).ToList();
            }

            return validPersons;
        }

        public async Task<IEnumerable<int>> GetPublicFamilyTreesByPersonIdAsync(int personId, int familytreeId)
        {
            var familyTreePersonService = ResolveService<IFamilyTreePersonService>();
            var ids = await familyTreePersonService.GetIdsOfPublicFamilyTreesByPersonIdAsync(personId);

            // Exclude current familytree
            return ids.Where(id => id != familytreeId).ToList();
        }

        #endregion

        #region CRUD operations

        public async Task<Person> CreatePersonAsync(Person personToCreate, int familytreeId = 0)
        {
            var relationshipService = ResolveService<IRelationshipService>();

            // All relationships needs to be added separatly because they need to be checked for collisions
            var ancestorOrHusbandRelationships = personToCreate.AncestorOrHusbandRelationship;
            var descendantOrWifeRelationships = personToCreate.DescendantOrWifeRelationship;

            personToCreate.AncestorOrHusbandRelationship = null;
            personToCreate.DescendantOrWifeRelationship = null;

            var result = await Create(personToCreate);

            // Get FamilyTree id where we are adding the new person
            if (familytreeId == 0)
            {
                if (ancestorOrHusbandRelationships.Any())
                    familytreeId = ancestorOrHusbandRelationships.First().FamilyTreeRelationship.First().FamilyTreeId;
                else if (descendantOrWifeRelationships.Any())
                    familytreeId = descendantOrWifeRelationships.First().FamilyTreeRelationship.First().FamilyTreeId;
                else
                    throw new InvalidObjectException("Id of the FamilyTree is not present", nameof(Person), personToCreate.Id);
            }

            // Create relationships one by one
            try
            {
                // Get added relationship (AncestorOrHusbandRelationship)
                foreach (var rel in ancestorOrHusbandRelationships)
                {
                    rel.AncestorOrHusbandPersonId = result.Id;
                    await relationshipService.CreateNewRelationshipInFamilyTreeAsync(rel, familytreeId);
                }

                // Get added relationship (DescendantOrWifeRelationship))
                foreach (var rel in descendantOrWifeRelationships)
                {
                    rel.DescendantOrWifePersonId = result.Id;
                    await relationshipService.CreateNewRelationshipInFamilyTreeAsync(rel, familytreeId);
                }
            }
            // If any error happened, person will be deleted with all relationships and collisions
            catch (Exception exception)
            {
                await RemovePersonFromFamilyTreeAsync(result.Id, familytreeId);

                throw exception;
            }

            result.AncestorOrHusbandRelationship = ancestorOrHusbandRelationships;
            result.DescendantOrWifeRelationship = descendantOrWifeRelationships;

            return result;
        }

        /// <summary>
        /// Updating Person when credentials has changed
        /// </summary>
        public async Task<Person> UpdatePersonCredentialsAsync(Person personToUpdate, int familytreeId)
        {

            var references = await _serviceDependencies._context.FamilyTreePerson.AsNoTracking().Where(fp => fp.PersonId == personToUpdate.Id).ToListAsync();

            // Person exists only in one FamilyTree -> can change whatever we want
            if (references.Count() == 1 && references.First().FamilyTreeId == familytreeId)
            {
                var personNameService = ResolveService<IPersonNameService>();
                var originalRecordService = ResolveService<IOriginalRecordService>();

                var currentNames = await personNameService.GetPersonNamesByPersonIdAsync(personToUpdate.Id);
                _serviceDependencies._context.DetachAllEntities();

                // Create new names
                foreach (var name in personToUpdate.PersonNames.Where(name => name.Id == 0).ToList())
                {
                    await personNameService.CreatePersonNameAsync(name);
                }

                // Update already existing names and delete removed names
                foreach (var name in currentNames)
                {
                    var updatedName = personToUpdate.PersonNames.FirstOrDefault(pn => pn.Id == name.Id);

                    if (updatedName != null)
                        await personNameService.UpdatePersonNameAsync(updatedName);
                    else
                        await personNameService.DeletePersonNameAsync(name.Id);
                }

                _serviceDependencies._context.DetachAllEntities();

                // Delete old references
                await originalRecordService.DeleteAllOriginalRecordsForPersonAsync(personToUpdate.Id);

                // Create new references
                foreach (var record in personToUpdate.OriginalRecords.Where(record => record.Id == 0).ToList())
                {
                    await originalRecordService.CreateOriginalRecordAsync(record);
                }

                currentNames = await personNameService.GetPersonNamesByPersonIdAsync(personToUpdate.Id);
                personToUpdate.PersonNames = currentNames.ToList();

                return await Update(personToUpdate);
            }
            // Person exists in more than one FamilyTree -> create new Person with required credentials
            else
            {
                // Store id of the previous person
                var oldPersonId = personToUpdate.Id;

                // Null all ids so they can be created again
                personToUpdate.Id = 0;
                foreach (var name in personToUpdate.PersonNames) { name.Id = 0; }

                // Create person and replace the old one
                var person = await CreatePersonAsync(personToUpdate, familytreeId);
                await ReplacePersonInFamilyTreeAsync(oldPersonId, person.Id, familytreeId);

                // Check if old person was start person -> replace
                await CheckForStartPersonChange(oldPersonId, person.Id, familytreeId);

                return person;
            }
        }

        /// <summary>
        /// Updating Person when adding to FamilyTree
        /// </summary>
        public async Task<Person> UpdatePersonAsync(Person personToUpdate)
        {
            var familytreeId = personToUpdate.FamilyTreePerson.Last().FamilyTreeId;

            var relationshipService = ResolveService<IRelationshipService>();

            // Get latest added relationship (AncestorOrHusbandRelationship) -- has id 0
            var addedAncestorRelationships = personToUpdate.AncestorOrHusbandRelationship.Where(rel => rel.Id == 0);

            // Get latest added relationship (DescendantOrWifeRelationship) -- has id 0
            var addedDescendantRelationships = personToUpdate.DescendantOrWifeRelationship.Where(rel => rel.Id == 0);

            foreach (var rel in addedAncestorRelationships.Concat(addedDescendantRelationships))
            {
                await relationshipService.CreateNewRelationshipInFamilyTreeAsync(rel, familytreeId);
            }

            personToUpdate.AncestorOrHusbandRelationship = null;
            personToUpdate.DescendantOrWifeRelationship = null;

            _serviceDependencies._context.DetachAllEntities();
            _serviceDependencies._context.Entry(personToUpdate).CurrentValues.SetValues(personToUpdate);
            _serviceDependencies._context.Attach(personToUpdate).State = EntityState.Modified;

            foreach (var fp in personToUpdate.FamilyTreePerson)
            {
                _serviceDependencies._context.FamilyTreePerson.Attach(fp);

                if (fp.FamilyTreeId == familytreeId)
                    _serviceDependencies._context.Entry(fp).State = EntityState.Added;
                else
                    _serviceDependencies._context.Entry(fp).State = EntityState.Modified;
            }

            await _serviceDependencies._context.SaveChangesAsync();

            return personToUpdate;
        }

        public async Task<Person> DeletePersonAsync(int id)
        {
            return await Delete(id);
        }

        #endregion

        /// <summary>
        /// Method coppies all valid relationships from old person to the new one
        /// Useful when solving collision or replacing undefined person
        /// </summary>
        public async Task<Person> ReplacePersonInFamilyTreeAsync(int oldPersonId, int newPersonId, int familytreeId)
        {
            var familyTreePersonService = ResolveService<IFamilyTreePersonService>();
            var relationshipService = ResolveService<IRelationshipService>();

            // Check if assumed new person does not already exists in the familytree
            bool alreadyExistsInFamilyTree = _serviceDependencies._context.FamilyTreePerson.AsNoTracking().Any(fp => fp.FamilyTreeId == familytreeId && fp.PersonId == newPersonId);
            if (alreadyExistsInFamilyTree)
                throw new InvalidObjectException("New person already exists in this familytree", nameof(FamilyTreePerson), newPersonId);

            var personRelationships = await relationshipService.GetRelationshipsInFamilyTreeForSpecificPersonAsync(familytreeId, oldPersonId);
            var personRelationshipsIds = personRelationships.Select(rel => rel.Id);

            // Check if old person was start person of the familytree
            await CheckForStartPersonChange(oldPersonId, newPersonId, familytreeId);

            //Remove old Person from FamilyTree
            await RemovePersonFromFamilyTreeAsync(oldPersonId, familytreeId);

            foreach (var relationship in personRelationships)
            {
                // Duplicate relationship and create it for the "new" (already existing) Person
                relationship.Id = 0;

                if (relationship.AncestorOrHusbandPersonId == oldPersonId)
                    relationship.AncestorOrHusbandPersonId = newPersonId;
                else
                    relationship.DescendantOrWifePersonId = newPersonId;

                // Push reference
                relationship.FamilyTreeRelationship = new List<FamilyTreeRelationship>();
                relationship.FamilyTreeRelationship.Add(new FamilyTreeRelationship { FamilyTreeId = familytreeId });

                relationship.AncestorOrHusbandPerson = null;
                relationship.DescendantOrWifePerson = null;

                // Set all marriage ids to null, so they will be created again
                foreach (var marriage in relationship.Marriages)
                {
                    marriage.Id = 0;
                    marriage.RelationshipId = 0;
                }

                _serviceDependencies._context.DetachAllEntities();
                await relationshipService.CreateNewRelationshipInFamilyTreeAsync(relationship, familytreeId);
            }

            // Put new person officially to the FamilyTree
            await familyTreePersonService.PutPersonToFamilyTreeAsync(new FamilyTreePerson { FamilyTreeId = familytreeId, PersonId = newPersonId });

            return await GetPersonByIdAsync(newPersonId);
        }

        /// <summary>
        /// Method updates undefined person to full-fledged person
        /// </summary>
        public async Task<Person> ReplaceUndefinedPersonInFamilyTreeAsync(Person replacedPerson)
        {
            await Update(replacedPerson);

            var collisionService = ResolveService<ICollisionService>();
            var personNameService = ResolveService<IPersonNameService>();

            // Create person's names
            foreach (var personName in replacedPerson.PersonNames)
                await personNameService.CreatePersonNameAsync(personName);

            // Check for collisions (relationships were never tested, because undefined person is marked as private) 
            foreach (var relationship in replacedPerson.AncestorOrHusbandRelationship)
            {
                relationship.AncestorOrHusbandPerson = replacedPerson;
                await collisionService.CollisionDetectionAsync(relationship);
            }

            foreach (var relationship in replacedPerson.DescendantOrWifeRelationship)
            {
                relationship.DescendantOrWifePerson = replacedPerson;
                await collisionService.CollisionDetectionAsync(relationship);
            }

            return replacedPerson;
        }

        /// <summary>
        /// Method provides removing person from familytree (person may be deleted)
        /// </summary>
        public async Task<Person> RemovePersonFromFamilyTreeAsync(int personId, int familytreeId)
        {
            var relationshipService = ResolveService<IRelationshipService>();

            var personToRemove = await _serviceDependencies._context.Persons
                                                                   .AsNoTracking()
                                                                   .Include(person => person.AncestorOrHusbandRelationship)
                                                                   .ThenInclude(rel => rel.FamilyTreeRelationship)
                                                                   .Include(person => person.DescendantOrWifeRelationship)
                                                                   .ThenInclude(rel => rel.FamilyTreeRelationship)
                                                                   .Include(person => person.FamilyTreePerson)
                                                                   .FirstOrDefaultAsync(person => person.Id == personId);


            if (personToRemove == null)
                throw new ObjectNotFoundException(nameof(Entity.Person), personId);

            // Person exists only in one familytree
            if (personToRemove.FamilyTreePerson.Count() == 1 && personToRemove.FamilyTreePerson.First().FamilyTreeId == familytreeId)
            {
                var collisionService = ResolveService<ICollisionService>();

                // All relationships will be deleted, need to check if it will somehow affect collisions
                foreach (var relationship in personToRemove.AncestorOrHusbandRelationship)
                    await collisionService.CheckForCollisionDeleteAsync(relationship.Id);

                foreach (var relationship in personToRemove.DescendantOrWifeRelationship)
                    await collisionService.CheckForCollisionDeleteAsync(relationship.Id);

                return await DeletePersonAsync(personId);
            }
            else
            {
                var familytreePersonService = ResolveService<IFamilyTreePersonService>();

                var familytreePersonReference = personToRemove.FamilyTreePerson.FirstOrDefault(x => x.FamilyTreeId == familytreeId);

                // Remove FamilyTreePerson reference
                if (familytreePersonReference != null)
                    await familytreePersonService.DeletePersonfromFamilyTreeAsync(familytreePersonReference);


                foreach (var relationship in personToRemove.AncestorOrHusbandRelationship)
                {
                    await relationshipService.RemoveReferenceOrDeleteRelationship(relationship, familytreeId);
                }

                foreach (var relationship in personToRemove.DescendantOrWifeRelationship)
                {
                    await relationshipService.RemoveReferenceOrDeleteRelationship(relationship, familytreeId);
                }
            }

            return personToRemove;
        }

        /// <summary>
        /// Method compares if two dates are +- 1 year far from each others
        /// </summary>
        private bool AreDatesSubtractLessThanYear(DateTime date1, DateTime date2)
        {
            var span = date1.Subtract(date2).Days;

            if ((span < 365) && (span > -365))
                return true;
            else
                return false;
        }

        /// <summary>
        /// Method inspects if passed id is startperson's id and eventualy change it to the new person
        /// </summary>
        private async Task CheckForStartPersonChange(int oldPersonId, int newPersonId, int familytreeId)
        {
            var familyTreeService = ResolveService<IFamilyTreeService>();
            var familyTree = await familyTreeService.GetFamilyTreeByIdAsync(familytreeId);
            _serviceDependencies._context.DetachAllEntities();

            // Actualize startPerson
            if (familyTree.StartPersonId == oldPersonId)
            {
                familyTree.StartPersonId = newPersonId;
                await familyTreeService.UpdateFamilyTreeAsync(familyTree);
            }
        }

    }
}
