/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    OriginalRecordController.cs                           */
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
    public class OriginalRecordController : ControllerBase
    {
        private readonly IOriginalRecordService _service;
        protected readonly IAutoMappingService _mapper;

        public OriginalRecordController(IOriginalRecordService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // GET api/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<OriginalRecordDto>> GetOriginalRecordsByRelationshipId(int id)
        {
            var result = await _service.GetOriginalRecordsByPersonIdAsync(id);

            return new ObjectResult(new
            {
                RecordsGet = _mapper.MapToDto<OriginalRecord, OriginalRecordDto>(result)
            });
        }
    }
}
