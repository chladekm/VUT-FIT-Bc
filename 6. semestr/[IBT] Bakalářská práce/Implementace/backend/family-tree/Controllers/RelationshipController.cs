/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    RelationshipController.cs                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.Collections.Generic;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Entity;
using Services.Interfaces;
using Mapper;
using PresentationModels;
using Microsoft.AspNetCore.Authorization;

namespace FamilyTree.Controllers
{
    [Route("[controller]")]
    [ApiController]
    [Authorize]
    public class RelationshipController : ControllerBase
    {
        private readonly IRelationshipService _service;
        protected readonly IAutoMappingService _mapper;

        public RelationshipController(IRelationshipService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // GET: api/relationship/tree{id}
        [HttpGet("tree/{id}")]
        public async Task<ActionResult<IEnumerable<RelationshipDto>>> GetRelationshipsByFamilyTreeId(int id)
        {
            var result = await _service.GetRelationshipsByFamilyTreeIdAsync(id);

            return new ObjectResult(new
            {
                RelationshipsByFamilyTreeGet = _mapper.MapToDto<Relationship, RelationshipDto>(result)
            });
        }
    }
}
