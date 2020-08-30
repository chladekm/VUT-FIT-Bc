/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeController.cs                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.Collections.Generic;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Services.Interfaces;
using PresentationModels;
using Mapper;
using Microsoft.AspNetCore.Authorization;

namespace FamilyTree.Controllers
{
    [Route("[controller]")]
    [ApiController]
    [Authorize]
    public class FamilyTreeController : ControllerBase
    {
        private readonly IFamilyTreeService _service;
        protected readonly IAutoMappingService _mapper;

        public FamilyTreeController(IFamilyTreeService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // GET: api/FamilyTree/user/userId
        [HttpGet("user/{id}")]
        public async Task<ActionResult<IEnumerable<FamilyTreeDto>>> GetFamilyTreesByUserId(int id)
        {
            var result = await _service.GetFamilyTreesByUserIdAsync(id);

            return new ObjectResult(new
            {
                FamilyTreeGetByUserId = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
            });
        }

        // GET: api/FamilyTree/Id
        [HttpGet("{id}")]
        public async Task<ActionResult<FamilyTreeDto>> GetFamilyTreeById(int id)
        {
            var result = await _service.GetFamilyTreeByIdAsync(id);

            return new ObjectResult(new
            {
                FamilyTreeGet = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
            });
        }

        // POST: api/FamilyTree
        [HttpPost]
        public async Task<ActionResult<FamilyTreeDto>> PostFamilyTree(FamilyTreeDto dataDto)
        {
            var data = _mapper.MapFromDto<FamilyTreeDto, Entity.FamilyTree>(dataDto);
            var result = await _service.CreateFamilyTreeAsync(data);

            return new ObjectResult(new
            {
                FamilyTreeCreate = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
            });
        }

        // PUT: api/FamilyTree
        [HttpPut]
        public async Task<ActionResult<FamilyTreeDto>> PutFamilyTree(FamilyTreeDto dataDto)
        {
            var data = _mapper.MapFromDto<FamilyTreeDto, Entity.FamilyTree>(dataDto);
            var result = await _service.UpdateFamilyTreeAsync(data);

            return new ObjectResult(new
            {
                FamilyTreeUpdate = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
            });
        }

        // DELETE: api/FamilyTree/5
        [HttpDelete("{id}")]
        public async Task<ActionResult<FamilyTreeDto>> DeleteFamilyTree(int id)
        {
            var result = await _service.DeleteFamilyTreeAsync(id);

            return new ObjectResult(new
            {
                FamilyTreeDelete = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
            });
        }

        // GET: api/FamilyTree/concat-valid
        [HttpGet("concat-valid/{srcId}-{userId}")]
        public async Task<ActionResult<IEnumerable<FamilyTreeDto>>> GetValidTreesForContatenation(int srcId, int userId)
        {
            var result = await _service.FamilyTreesThanCanBeConcatenatedForUserAsync(srcId, userId);

            return new ObjectResult(new
            {
                FamilyTreeConcatValid = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
        });
        }

        // POST: api/FamilyTree/concatenate
        [HttpPut("concatenate/{srcId}-{dstId}")]
        public async Task<ActionResult<FamilyTreeDto>> ConcatenateFamilyTrees(int srcId, int dstId)
        {
            var result = await _service.ConcatenateFamilyTreesAsync(srcId, dstId);

            return new ObjectResult(new
            {
                FamilyTreeConcatenate = _mapper.MapToDto<Entity.FamilyTree, FamilyTreeDto>(result)
            });
        }
    }
}
