/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreePersonService.cs                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Enums;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using Service;
using Services.Base;
using Services.Interfaces.RelationshipInterfaces;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services.RelationshipServices
{
    public class FamilyTreePersonService : BaseRelationshipService<FamilyTreePerson>, IFamilyTreePersonService
    {

        public FamilyTreePersonService(FamilyTreeDbContext _context) : base(_context)
        {
        }

        public async Task<FamilyTreePerson> PutPersonToFamilyTreeAsync(FamilyTreePerson item)
        {
            return await Create<FamilyTree, Person>(item);
        }

        public async Task<FamilyTreePerson> DeletePersonfromFamilyTreeAsync(FamilyTreePerson item)
        {
            return await Delete<FamilyTree, Person>(item);
        }

        /// <summary>
        /// Method returns ids of public familytrees where person exists
        /// </summary>
        public async Task<IEnumerable<int>> GetIdsOfPublicFamilyTreesByPersonIdAsync(int personId)
        {
            var references = await _context.FamilyTreePerson.AsNoTracking().Where(fp => fp.PersonId == personId).Include(fp => fp.FamilyTree).ToListAsync();
            return references.Where(fp => fp.FamilyTree.Type == FamilyTreeTypesEnum._public).Select(fp => fp.FamilyTreeId).ToList();
        }

        protected override (FamilyTreePerson, int, int) GetRecordAndIdsForRelationship(FamilyTreePerson item)
        {
            var result = _context.FamilyTreePerson.AsNoTracking().FirstOrDefault(fp => (fp.FamilyTreeId == item.FamilyTreeId) && (fp.PersonId == item.PersonId));

            return (result, item.FamilyTreeId, item.PersonId);
        }

    }
}
