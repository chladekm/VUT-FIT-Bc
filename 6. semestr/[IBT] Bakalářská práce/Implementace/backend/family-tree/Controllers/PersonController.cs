/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    PersonController.cs                                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.Collections.Generic;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Entity;
using Services.Interfaces;
using PresentationModels;
using Mapper;
using Microsoft.AspNetCore.Authorization;

namespace FamilyTree.Controllers
{
    [Route("[controller]")]
    [ApiController]
    [Authorize]
    public class PersonController : ControllerBase
    {
        private readonly IPersonService _service;
        protected readonly IAutoMappingService _mapper;

        public PersonController(IPersonService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // GET: api/Person/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<PersonDto>> GetPerson(int Id)
        {
            var result = await _service.GetPersonByIdAsync(Id);
            
            return new ObjectResult(new
            {
                PersonGet = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // POST: api/Person/similiar
        [HttpPost("similiar/")]
        public async Task<ActionResult<IEnumerable<PersonDto>>> GetSimiliarPersons([FromBody] PersonDto dataDto)
        {
            var data = _mapper.MapFromDto<PersonDto, Person>(dataDto);

            var result = await _service.GetSimiliarPersonsAsync(data);

            return new ObjectResult(new
            {
                PersonsGetSimiliar = _mapper.MapToDto<Person, PersonDto>(result)
            });    
        }

        // POST: api/Person
        [HttpPost]
        public async Task<ActionResult<PersonDto>> PostPerson(PersonDto dataDto)
        {
            var data = _mapper.MapFromDto<PersonDto, Person>(dataDto);

            var result = await _service.CreatePersonAsync(data);

            return new ObjectResult(new
            {
                PersonCreate = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // PUT: api/Person
        [HttpPut("{familyTreeId}")]
        public async Task<ActionResult<PersonDto>> UpdatePersonCredentials(PersonDto dataDto, int familyTreeId)
        {
            var data = _mapper.MapFromDto<PersonDto, Person>(dataDto);

            var result = await _service.UpdatePersonCredentialsAsync(data, familyTreeId);

            return new ObjectResult(new
            {
                PersonUpdate = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // PUT: api/Person/familytree
        [HttpPut("familytree/")]
        public async Task<ActionResult<PersonDto>> UpdatePerson(PersonDto dataDto)
        {
            var data = _mapper.MapFromDto<PersonDto, Person>(dataDto);

            var result = await _service.UpdatePersonAsync(data);

            return new ObjectResult(new
            {
                PersonUpdate = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // DELETE: api/Person
        [HttpDelete("{pid}-{fid}")]
        public async Task<ActionResult<PersonDto>> RemovePerson(int pid, int fid)
        {
            var result = await _service.RemovePersonFromFamilyTreeAsync(pid, fid);

            return new ObjectResult(new
            {
                PersonRemove = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // PUT: api/Person/replace-undefined
        [HttpPut("replace-undefined/")]
        public async Task<ActionResult<PersonDto>> ReplaceUndefinedPerson(PersonDto dataDto)
        {
            var data = _mapper.MapFromDto<PersonDto, Person>(dataDto);

            var result = await _service.ReplaceUndefinedPersonInFamilyTreeAsync(data);
   
            return new ObjectResult(new
            {
                PersonReplaceUndefined = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // PUT: api/Person/replace
        [HttpPut("replace/{oldPersonId}-{newPersonId}-{familyTreeId}")]
        public async Task<ActionResult<PersonDto>> ReplacePerson(int oldPersonId, int newPersonId, int familyTreeId)
        {
            var result = await _service.ReplacePersonInFamilyTreeAsync(oldPersonId, newPersonId, familyTreeId);

            return new ObjectResult(new
            {
                PersonReplace = _mapper.MapToDto<Person, PersonDto>(result)
            });
        }

        // GET: api/Person/{id}
        [HttpGet("publicTrees/{pid}-{fid}")]
        public async Task<ActionResult<IEnumerable<int>>> GetPublicTreesForPerson(int pid, int fid)
        {
            var result = await _service.GetPublicFamilyTreesByPersonIdAsync(pid, fid);

            return new ObjectResult(new
            {
                PersonGetPublicTrees = result
            });
        }
    }
}
