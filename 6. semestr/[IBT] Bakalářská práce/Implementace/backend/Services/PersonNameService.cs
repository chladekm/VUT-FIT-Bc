/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    PersonNameService.cs                                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Microsoft.EntityFrameworkCore;
using Services.Base;
using Services.Exceptions;
using Services.Interfaces;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services
{
    public class PersonNameService : BaseService<PersonName>, IPersonNameService
    {
        public PersonNameService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region CRUD operations

        public async Task<IEnumerable<PersonName>> GetPersonNamesByPersonIdAsync(int Id)
        {
            if (!(ExistsInDb<Person>(Id)))
                throw new ObjectNotFoundException(nameof(Person), Id);

            var names = await _serviceDependencies._context.PersonNames.Where(personname => personname.PersonId == Id).OrderBy(personname => personname.isFirstName).ToListAsync();

            return names;
        }

        public async Task<PersonName> CreatePersonNameAsync(PersonName nameToCreate)
        {
            if (!(ExistsInDb<Person>(nameToCreate.PersonId)))
                throw new ObjectNotFoundException(nameof(Person), nameToCreate.PersonId);

            return await Create(nameToCreate);
        }

        public async Task<PersonName> UpdatePersonNameAsync(PersonName nameToUpdate)
        {
            return await Update(nameToUpdate);
        }

        public async Task<PersonName> DeletePersonNameAsync(int id)
        {
            return await Delete(id);
        }

        #endregion
    }
}
