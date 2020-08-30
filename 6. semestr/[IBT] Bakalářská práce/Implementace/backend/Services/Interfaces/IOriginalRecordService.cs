/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IOriginalRecordService.cs                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IOriginalRecordService
    {
        // GET methods
        public Task<IEnumerable<OriginalRecord>> GetOriginalRecordsByPersonIdAsync(int id);

        // CRUD methods
        public Task<OriginalRecord> CreateOriginalRecordAsync(OriginalRecord recordToCreate);
        public Task DeleteAllOriginalRecordsForPersonAsync(int id);
    }
}
