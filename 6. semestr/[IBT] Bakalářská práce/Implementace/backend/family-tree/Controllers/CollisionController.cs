/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CollisionController.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using Mapper;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using PresentationModels;
using Services.Interfaces;
using System.Threading.Tasks;

namespace FamilyTree.Controllers
{
    [Authorize]
    [ApiController]
    [Route("[controller]")]
    public class CollisionController : ControllerBase
    {

        private readonly ICollisionService _service;
        protected readonly IAutoMappingService _mapper;

        public CollisionController(ICollisionService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // GET api/collision/count/{id}
        [HttpGet("count/{id}")]
        public async Task<ActionResult<int>> GetUnsolvedCollisionsCountByFamilyTreeId(int Id)
        {
            var result = await _service.GetCountOfNotSolvedCollisionsByFamilyTreeIdAsync(Id);

            return new ObjectResult(new
            {
                CollisionsCountGet = result
            });

        }

        // GET api/collision/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<CollisionDto>> GetCollisionsByFamilyTreeId(int Id)
        {
            var result = await _service.GetCollisionsByFamilyTreeIdAsync(Id);

            return new ObjectResult(new
            {
                CollisionsGet = _mapper.MapToDto<Collision, CollisionDto>(result)
            });

        }

        // PUT api/collision/solved/
        [HttpPut("solved")]
        public async Task<ActionResult<FamilyTreeCollision>> ToggleCollisionSolution(FamilyTreeCollision item)
        {
            var result = await _service.ToggleCollisionSolutionAsync(item);

            return new ObjectResult(new
            {
                CollisionSolved = result
            });

        }
    }
}