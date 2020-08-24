using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using TeamChat.BL.Models;
using TeamChat.BL.Repositories;
using TeamChat.DAL.DBContexts;
using TeamChat.DAL.Entities;

namespace TeamChat.API.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class PostController : ControllerBase
    {
        readonly PostRepository _repository = new PostRepository(new DbContextFactory());

        [HttpGet]
        public ActionResult<IEnumerable<PostModel>> GetAllPosts()
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
        public ActionResult<PostModel> GetPostById(int id)
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

        [HttpGet("team/{id}")]
        public ActionResult<IEnumerable<PostModel>> GetAllPostsByTeamId(int id)
        {
            try
            {
                return _repository.GetAll().Where(post => post.Team == id).ToList();
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpGet("user/{id}")]
        public ActionResult<PostModel> GetLastPostByUserId(int id)
        {
            try
            {
                return _repository.GetAll().Where(post => post.Author == id).OrderByDescending(e => e.Date).FirstOrDefault();
            }
            catch (Exception e)
            {
                return null;
            }
        }

        [HttpPost]
        public void CreatePost([FromBody] PostModel value)
        {
            try
            {
                _repository.Create(value);
            }
            catch (Exception e)
            {
                
            }
            
        }

        [HttpPut]
        public void UpdatePost([FromBody] PostModel value)
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
        public void DeletePost(int id)
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
