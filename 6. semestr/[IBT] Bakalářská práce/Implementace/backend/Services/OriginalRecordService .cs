/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    OriginalRecordService.cs                              */
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
    public class OriginalRecordService : BaseService<OriginalRecord>, IOriginalRecordService
    {
        public OriginalRecordService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region CRUD operations

        public async Task<IEnumerable<OriginalRecord>> GetOriginalRecordsByPersonIdAsync(int id)
        {
            if (!(ExistsInDb<Person>(id)))
                throw new ObjectNotFoundException(nameof(Person), id);

            var records = await _serviceDependencies._context.OriginalRecords.Where(record => record.PersonId == id).ToListAsync();

            return records;
        }

        public async Task<OriginalRecord> CreateOriginalRecordAsync(OriginalRecord recordToCreate)
        {
            if (!(ExistsInDb<Person>(recordToCreate.PersonId)))
                throw new ObjectNotFoundException(nameof(Person), recordToCreate.PersonId);

            return await Create(recordToCreate);
        }

        public async Task DeleteAllOriginalRecordsForPersonAsync(int personId)
        {
            var records = await GetOriginalRecordsByPersonIdAsync(personId);

            foreach(var record in records)
                _serviceDependencies._context.OriginalRecords.Remove(record);

            return;
        }

        #endregion
    }
}
