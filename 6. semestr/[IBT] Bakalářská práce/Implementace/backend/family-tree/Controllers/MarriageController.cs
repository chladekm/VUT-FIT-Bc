/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    MarriageController.cs                                 */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

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
    public class MarriageController : ControllerBase
    {
        private readonly IMarriageService _service;
        protected readonly IAutoMappingService _mapper;

        public MarriageController(IMarriageService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // GET api/marriagerelationship/{id}
        [HttpGet("relationship/{id}")]
        public async Task<ActionResult<MarriageDto>> GetMarriagesByRelationshipId(int id)
        {
            var result = await _service.GetMarriagesByRelationshipIdAsync(id);

            return new ObjectResult(new
            {
                MarriagesGet = _mapper.MapToDto<Marriage, MarriageDto>(result)
            });
        }

        // POST api/marriage
        [HttpPost("{familytreeId}")]
        public async Task<ActionResult<MarriageDto>> CreateMarriage(MarriageDto dataDto, int familytreeId)
        {
            var data = _mapper.MapFromDto<MarriageDto, Marriage>(dataDto);

            var result = await _service.AddMarriageToRelationshipAsync(data, familytreeId);

            return new ObjectResult(new
            {
                MarriageCreate = _mapper.MapToDto<Marriage, MarriageDto>(result)
            });
        }
    }
}
