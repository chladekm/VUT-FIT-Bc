/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IMarriageService.cs                                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IMarriageService
    {
        // GET methods
        public Task<IEnumerable<Marriage>> GetMarriagesByRelationshipIdAsync(int Id);

        // CRUD methods
        public Task<Marriage> CreateMarriageAsync(Marriage item);

        // Other methods
        public Task<Marriage> AddMarriageToRelationshipAsync(Marriage item, int familytreeId);
    }
}
