/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IPersonService.cs                                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IPersonService
    {
        // GET methods
        public Task<Person> GetPersonByIdAsync(int Id);
        public Task<IEnumerable<Person>> GetSimiliarPersonsAsync(Person personToSearch);
        public Task<IEnumerable<int>> GetPublicFamilyTreesByPersonIdAsync(int personId, int familytreeId);
        
        // CRUD methods
        public Task<Person> CreatePersonAsync(Person data, int familyTreeId = 0);
        public Task<Person> UpdatePersonCredentialsAsync(Person personToUpdate, int familyTreeId);
        public Task<Person> UpdatePersonAsync(Person personToUpdate);
        public Task<Person> DeletePersonAsync(int Id);

        // Other methods
        public Task<Person> ReplacePersonInFamilyTreeAsync(int oldPersonId, int newPersonId, int familytreeId);
        public Task<Person> ReplaceUndefinedPersonInFamilyTreeAsync(Person replacedPerson);
        public Task<Person> RemovePersonFromFamilyTreeAsync(int personId, int familytreeId);
    }
}
