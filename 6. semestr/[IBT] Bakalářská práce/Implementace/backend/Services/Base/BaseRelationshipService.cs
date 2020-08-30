/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    BaseRelationshipService.cs                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using Microsoft.EntityFrameworkCore;
using Service;
using Services.Exceptions;
using System;
using System.Linq;
using System.Threading.Tasks;

namespace Services.Base
{
    public abstract class BaseRelationshipService<TEntityRel> where TEntityRel : class
    {
        protected readonly FamilyTreeDbContext _context;

        protected BaseRelationshipService(FamilyTreeDbContext context)
        {
            _context = context;
        }

        /// <summary>
        /// Gets record for selected relationship and returns Ids that are referencing to the entities
        /// </summary>
        protected abstract (TEntityRel, int, int) GetRecordAndIdsForRelationship(TEntityRel item);

        /// <summary>
        /// Create record of relationship between entities in database
        /// </summary>
        protected async Task<TEntityRel> Create<TReference1, TReference2>(TEntityRel item) where TReference1 : class, IEntity
                                                                                           where TReference2 : class, IEntity 
        {
            var (result, idRef1Value, idRef2Value) = GetRecordAndIdsForRelationship(item);

            if (!ExistsInDb<TReference1>(idRef1Value))
                throw new ObjectNotFoundException(typeof(TReference1).FullName, idRef1Value);
            else if (!ExistsInDb<TReference2>(idRef2Value))
                throw new ObjectNotFoundException(typeof(TReference2).FullName, idRef2Value);

            if (result != null)
                throw new InvalidObjectRelationshipException("This reference already exists", typeof(TEntityRel).FullName, idRef1Value, idRef2Value);

            await _context.Set<TEntityRel>().AddAsync(item);
            await _context.SaveChangesAsync();

            return item;
        }

        /// <summary>
        /// Deletes record of relationship between entities from database
        /// </summary>
        protected async Task<TEntityRel> Delete<TReference1, TReference2>(TEntityRel item) where TReference1 : class, IEntity
                                                                                           where TReference2 : class, IEntity
        {
            var (result, idRef1Value, idRef2Value) = GetRecordAndIdsForRelationship(item);

            if (result == null)
                throw new ObjectRelationshipNotFoundException(typeof(TEntityRel).FullName, idRef1Value, idRef2Value);
            else
            {
                try 
                {
                    _context.DetachAllEntities();
                    _context.Entry(result).State = EntityState.Deleted;
                    await _context.SaveChangesAsync();
                }
                catch (Exception) { }
            }
            
            return item;
        }

        /// <summary>
        /// Checks if exists selected record
        /// </summary>
        protected bool ExistsInDb<TEntity>(int Id) where TEntity : class, IEntity
        {
            var result = _context.Set<TEntity>().AsNoTracking().FirstOrDefault(entity => entity.Id == Id);

            if (result != null)
                return true;
            else
                return false;
        }
    }
       
}
