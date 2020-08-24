using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using Microsoft.AspNetCore.Mvc;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.BL.Repositories;
using TeamChat.DAL.DBContexts;

namespace TeamChat.API.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class UserController : ControllerBase
    {
        readonly UserRepository _repository = new UserRepository(new DbContextFactory());
        readonly UserMapper _mapper = new UserMapper();

        [HttpGet]
        public ActionResult<IEnumerable<UserListModel>> GetAllUsers()
        {
            try
            {
                return _repository.GetAll();
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpGet("{id}")]
        public ActionResult<UserDetailModel> GetUserById(int id)
        {
            try
            {
                return _repository.GetById(id);
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpGet("lessdetails/{id}")]
        public ActionResult<UserListModel> GetLessDetailUserById(int id)
        {
            try
            {
                return _mapper.DetailModelToListModel(_repository.GetById(id));
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpGet("loginmodel/{id}")]
        public ActionResult<UserLoginModel> GetUserLoginModelById(int id)
        {
            try
            {
                return _mapper.DetailModelToLoginModel(_repository.GetById(id));
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpGet("loginmodel/email/{email}")]
        public ActionResult<UserLoginModel> GetUserLoginModelByEmail(string email)
        {
            try
            {
                return _repository.GetByEmail(email);
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpGet("profilemodel/{id}")]
        public ActionResult<UserProfileModel> GetUserProfileModelById(int id)
        {
            try
            {
                return _mapper.DetailModelToProfileModel(_repository.GetById(id));
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpPost]
        public void CreateUser([FromBody] UserRegistrationModel value)
        {
            try
            {
                var NewUser = new UserDetailModel()
                {
                    Name = value.Name,
                    Email = value.Email,
                    Password = value.Password,
                    Comments = new Collection<CommentModel>(),
                    Posts = new Collection<PostModel>()
                };
                _repository.Create(NewUser);
            }
            catch (Exception e)
            {

            }
            
        }

        [HttpPut]
        public void UpdateUser([FromBody] UserDetailModel value)
        {
            try
            {
                _repository.Update(value);
            }
            catch (Exception e)
            {

            }
            
        }

        [HttpDelete("{id}")]
        public void DeleteUser(int id)
        {
            try
            {
                _repository.Delete(id);
            }
            catch (Exception e)
            {
                
            }
            
        }
    }
}
