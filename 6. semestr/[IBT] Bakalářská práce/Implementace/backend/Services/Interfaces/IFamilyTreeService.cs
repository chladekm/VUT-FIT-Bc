/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IFamilyTreeService.cs                                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Collections.Generic;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IFamilyTreeService
    {
        // GET methods
        public Task<IEnumerable<FamilyTree>> GetFamilyTreesByUserIdAsync(int userId);
        public Task<FamilyTree> GetFamilyTreeByIdAsync(int id);

        // CRUD methods
        public Task<FamilyTree> CreateFamilyTreeAsync(FamilyTree treeToCreate);
        public Task<FamilyTree> UpdateFamilyTreeAsync(FamilyTree treeToCreate);
        public Task<FamilyTree> DeleteFamilyTreeAsync(int id);

        // Other methods
        public Task<IEnumerable<FamilyTree>> FamilyTreesThanCanBeConcatenatedForUserAsync(int sourceTreeId, int userId);
        public Task<FamilyTree> ConcatenateFamilyTreesAsync(int sourceTreeId, int destinationTreeId);
    }
}
