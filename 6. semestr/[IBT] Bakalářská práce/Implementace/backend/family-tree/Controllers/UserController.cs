/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    UserController.cs                                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Mapper;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using PresentationModels;
using PresentationModels.Authentication;
using Services.Interfaces;
using System.Threading.Tasks;

namespace FamilyTree.Controllers
{
    [Authorize]
    [ApiController]
    [Route("[controller]")]
    public class UserController : ControllerBase
    {
        
        private readonly IUserService _service;
        protected readonly IAutoMappingService _mapper;

        public UserController(IUserService service, IAutoMappingService mapper)
        {
            _service = service;
            _mapper = mapper;
        }

        // POST api/user/login
        [AllowAnonymous]
        [HttpPost("login")]
        public async Task<ActionResult<UserCompleteDto>> LoginUser([FromBody] LoginCredentialsDto dataDto)
        {
            var data = _mapper.MapFromDto<LoginCredentialsDto, User>(dataDto);

            var result = await _service.LoginUserAsync(data);

            if (result == null)
                return BadRequest(new { message = "Username or password is incorrect" });

            return new ObjectResult(new
            {
                Login = _mapper.MapToDto<User, LoginCredentialsDto>(result)
            });
        }

        // POST api/user/check
        [AllowAnonymous]
        [HttpPost("check")]
        public async Task<ActionResult<bool>> CheckUniqueCredentials(LoginCredentialsDto Credentials)
        {
            var Nickname = Credentials.Nickname;
            var result =  await _service.AreCredentialsUnique(Nickname);

            return Ok(result);
        }

        // GET api/user/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<UserDto>> GetUser(int Id)
        {
            var result = await _service.GetUserAsync(Id);

            return new ObjectResult(new
            {
                UserGet = _mapper.MapToDto<User, UserDto>(result)
            });

        }

        // POST api/user
        [AllowAnonymous]
        [HttpPost]
        public async Task<ActionResult<UserCompleteDto>> PostUser([FromBody] UserCompleteDto dataDto)
        {
            var data = _mapper.MapFromDto<UserCompleteDto, User>(dataDto);

            var result = await _service.CreateUserAsync(data);

            return new ObjectResult(new
            {
                UserCreate = _mapper.MapToDto<User, UserCompleteDto>(result)
            }); 
        }

        // PUT api/user
        [HttpPut]
        public async Task<ActionResult<UserDto>> PutUser([FromBody] UserDto dataDto)
        {
            var data = _mapper.MapFromDto<UserDto, User>(dataDto);

            var result = await _service.UpdateUserAsync(data);

            return new ObjectResult(new
            {
                UserUpdate = _mapper.MapToDto<User, UserDto>(result)
            });
        }

        // Delete api/user/{id}
        [HttpDelete("{id}")]
        public async Task<ActionResult<UserDto>> DeleteUser(int id)
        {
            var result = await _service.DeleteUserAsync(id);

            return new ObjectResult(new
            {
                UserDelete = _mapper.MapToDto<User, UserDto>(result)
            });
        }
    }
}
