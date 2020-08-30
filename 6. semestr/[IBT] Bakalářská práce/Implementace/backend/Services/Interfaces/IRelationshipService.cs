/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IRelationshipService.cs                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IRelationshipService
    {
        // GET methods
        public Task<Relationship> GetRelationshipByIdAsync(int id);
        public Task<IEnumerable<Relationship>> GetRelationshipsListByPersonIdAsync(int Id);
        public  Task<Relationship> GetRelationshipByParticipatedPersonsAsync(Relationship item);
        public Task<IEnumerable<Relationship>> GetRelationshipsListByParticipatedPersonsAsync(Relationship item);
        public Task<IEnumerable<Relationship>> GetRelationshipsByFamilyTreeIdAsync(int id);
        public Task<IEnumerable<Relationship>> GetRelationshipsInFamilyTreeForSpecificPersonAsync(int FamilyTreeId, int PersonId);
        
        // CRUD methods
        public Task<Relationship> CreateNewRelationshipInFamilyTreeAsync(Relationship item, int FamilyTreeId);
        public Task<Relationship> UpdateRelationshipAsync(Relationship item);
        public Task<Relationship> DeleteRelationshipAsync(int id);

        // Other methods
        public Task AddExistingRelationshipToFamilyTreeAsync(int relationshipId, int familyTreeId);
        public Task<Relationship> RemoveReferenceOrDeleteRelationship(Relationship relationship, int familytreeId);
        public ICollection<Relationship> NullIdsOfRelationshipsAndMarriages(ICollection<Relationship> relationships);
    }
}
