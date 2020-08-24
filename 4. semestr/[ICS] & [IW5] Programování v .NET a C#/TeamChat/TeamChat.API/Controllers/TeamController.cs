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
    public class TeamController : ControllerBase
    {
        readonly TeamRepository _repository = new TeamRepository(new DbContextFactory());
        readonly TeamMapper _mapper = new TeamMapper();     

        [HttpGet]
        public ActionResult<IEnumerable<TeamListModel>> GetAllTeams()
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
        public ActionResult<TeamDetailModel> GetTeamById(int id)
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
        public ActionResult<TeamListModel> GetLessDetailTeamById(int id)
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

        [HttpGet("user/{id}")]
        public ActionResult<IEnumerable<TeamListModel>> GetTeamsForUserId(int id)
        {
            try
            {
                return _repository.GetAllTeamsForUser(id);
            }
            catch (Exception e)
            {
                return null;
            }
            
        }

        [HttpPost]
        public void CreateTeam([FromBody] TeamDetailModel value)
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
        public void UpdateTeam([FromBody] TeamDetailModel value)
        {
            try
            {
                _repository.Update(value);
            }
            catch
            {
                try
                {
                    _repository.Update(value);
                }
                catch
                {

                }
            }
            
        }

        [HttpDelete("{id}")]
        public void DeleteTeam(int id)
        {
            try
            {
                _repository.Delete(id);
            }
            catch
            {
                try
                {
                    _repository.Delete(id);
                }
                catch
                {

                }
            }
            
        }
    }
}
