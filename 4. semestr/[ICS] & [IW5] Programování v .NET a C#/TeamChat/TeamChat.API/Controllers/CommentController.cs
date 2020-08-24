using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.BL.Repositories;
using TeamChat.DAL.DBContexts;
using TeamChat.DAL.Entities;

namespace TeamChat.API.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class CommentController : ControllerBase
    {

        readonly CommentRepository _repository = new CommentRepository(new DbContextFactory());

        [HttpGet]
        public ActionResult<IEnumerable<CommentModel>> GetAllComments()
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
        public ActionResult<CommentModel> GetCommentById(int id)
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

        [HttpGet("user/{id}")]
        public ActionResult<CommentModel> GetLastCommentByUserId(int id)
        {
            try
            {
                return _repository.GetAll().Where(comment => comment.Author == id).OrderByDescending(e => e.Date).FirstOrDefault();
            }
            catch (Exception e)
            {
                return null;
            }
        }

        [HttpGet("post/{id}")]
        public ActionResult<IEnumerable<CommentModel>> GetCommentsByPostId(int id)
        {
            try
            {
                return _repository.GetAll().Where(comment => comment.Post == id).ToList();
            }
            catch (Exception e)
            {
                return null;
            }
        }

        [HttpPost]
        public void CreateComment([FromBody] CommentModel value)
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
        public void UpdateComment([FromBody] CommentModel value)
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
        public void DeleteComment(int id)
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
