/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IAutoMappingService.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.Collections.Generic;

namespace Mapper
{
    public interface IAutoMappingService
    { 
        public TDto MapToDto<TSource, TDto>(TSource item);
        public List<TDto> MapToDto<TSource, TDto>(IEnumerable<TSource> items);
        public TEntity MapFromDto<TDto, TEntity>(TDto item);
        public List<TEntity> MapFromDto<TDto, TEntity>(IEnumerable<TDto> items);

    }
}
