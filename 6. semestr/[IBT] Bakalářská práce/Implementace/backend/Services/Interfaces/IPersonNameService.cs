/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IPersonNameService.cs                                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IPersonNameService
    {
        // GET methods
        public Task<IEnumerable<PersonName>> GetPersonNamesByPersonIdAsync(int Id);

        // CRUD methods
        public Task<PersonName> CreatePersonNameAsync(PersonName nameToCreate);
        public Task<PersonName> UpdatePersonNameAsync(PersonName nameToUpdate);
        public Task<PersonName> DeletePersonNameAsync(int Id);
    }
}
