/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    AutoMappingService.cs                                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using AutoMapper;
using System.Collections.Generic;
using System.Linq;

namespace Mapper
{
    public class AutoMappingService : IAutoMappingService
    {
        private readonly IMapper _mapper;

        public AutoMappingService(IMapper mapper)
        {
            _mapper = mapper;
        }

        // Map Entity to Dto Model
        public TDto MapToDto<TSource, TDto>(TSource item)
        { 
            return _mapper.Map<TSource, TDto>(item);
        }

        // Map List of Entities to List of Dto models
        public List<TDto> MapToDto<TSource, TDto>(IEnumerable<TSource> items)
        {
            return items.Select(MapToDto<TSource, TDto>).ToList();
        }

        // Map from Dto Model to Entity
        public TEntity MapFromDto<TDto, TEntity>(TDto item)
        {
            return _mapper.Map<TDto, TEntity>(item);
        }

        // Map List of Dto models to List of Entities
        public List<TEntity> MapFromDto<TDto, TEntity>(IEnumerable<TDto> items)
        {
            return items.Select(MapToDto<TDto, TEntity>).ToList();
        }
    }
}
