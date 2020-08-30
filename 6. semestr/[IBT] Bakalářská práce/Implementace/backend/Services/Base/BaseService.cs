/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    BaseService.cs                                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;
using Services.Exceptions;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Services.Base
{
    public abstract class BaseService<TEntity> where TEntity : class, IEntity
    {
        protected readonly IServiceDependencies _serviceDependencies;

        protected BaseService(IServiceDependencies serviceDependencies)
        {
            _serviceDependencies = serviceDependencies;
        }

        protected T ResolveService<T>()
        {
            return _serviceDependencies.ServiceProvider.GetRequiredService<T>();
        }
        
        public DbSet<TEntity> DbSet => _serviceDependencies._context.Set<TEntity>();

        private IQueryable<TEntity> GetEntityQuery()
        {
            return DbSet.AsQueryable().AsNoTracking();
        }

        /// <summary>
        /// Get record by id
        /// </summary
        protected async Task<TEntity> GetById(int id)
        {
            var result = await GetEntityQuery().FirstOrDefaultAsync(item => item.Id == id);
            
            if (result == null)
                throw new ObjectNotFoundException(typeof(TEntity).FullName, id);
            else
                return result;
        }

        /// <summary>
        /// Get all records
        /// </summary>
        protected async Task<IEnumerable<TEntity>> GetAll()
        {
            return await GetEntityQuery().ToListAsync();
        }

        /// <summary>
        /// Create record in database
        /// </summary>
        protected async Task<TEntity> Create(TEntity item)
        {
            await _serviceDependencies._context.Set<TEntity>().AddAsync(item);
            await _serviceDependencies._context.SaveChangesAsync();

            return item;
        }

        /// <summary>
        /// Delete record from database
        /// </summary>
        protected async Task<TEntity> Delete(int id)
        {
            var result = await GetEntityQuery().FirstOrDefaultAsync(item => item.Id == id);

            if (result != null)
            {
                _serviceDependencies._context.Entry(result).State = EntityState.Deleted;
                await _serviceDependencies._context.SaveChangesAsync();
            }
            else
            {
                throw new ObjectNotFoundException(typeof(TEntity).FullName, id);
            }

            return result;
        }

        /// <summary>
        /// Update record in database
        /// </summary>
        protected async Task<TEntity> Update(TEntity item)
        {
            var result = await GetEntityQuery().FirstOrDefaultAsync(i => i.Id == item.Id);

            // Record found
            if (result != null)
            {
                _serviceDependencies._context.Entry(result).CurrentValues.SetValues(item);
                _serviceDependencies._context.Entry(result).State = EntityState.Modified;

                await _serviceDependencies._context.SaveChangesAsync();
            }
            else
            {
                throw new ObjectNotFoundException(typeof(TEntity).FullName, item.Id);
            }

            return item;
        }

        /// <summary>
        /// Check if selected entity has record in database (by id)
        /// </summary>
        protected bool ExistsInDb<TEntityCheck>(int Id) where TEntityCheck : class, IEntity
        {
            var result = _serviceDependencies._context.Set<TEntityCheck>().AsNoTracking().FirstOrDefault(entity => entity.Id == Id);

            return result == null ? false : true;

        }
    }
}
