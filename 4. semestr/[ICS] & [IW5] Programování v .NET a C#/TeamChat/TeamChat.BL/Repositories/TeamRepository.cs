using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.ComponentModel;
using System.Linq;
using Microsoft.EntityFrameworkCore;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.DAL.DBContexts;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Repositories
{
    public class TeamRepository : IGenericRepository<TeamDetailModel, TeamListModel>
    {
        private readonly IDbContextFactory dbContextFactory;

        public TeamRepository(IDbContextFactory dbContextFactory)
        {
            this.dbContextFactory = dbContextFactory;
        }

        public TeamDetailModel GetById(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Teams.Include("Members").First(t => t.Id == id);
                var teamMapper = new TeamMapper();
                return teamMapper.EntityToDetailModel(entity);
            }
        }

        public TeamDetailModel Create(TeamDetailModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var teamMapper = new TeamMapper();              
                var entity = teamMapper.DetailModelToEntity(model);
                dbContext.Teams.Add(entity); 
                dbContext.SaveChanges();
                return teamMapper.EntityToDetailModel(entity);
            }
        }

        public void Update(TeamDetailModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                /*var teamMapper = new TeamMapper();
                var entity = teamMapper.DetailModelToEntity(model);
                dbContext.Teams.Update(entity);
                dbContext.SaveChanges();*/
                if (dbContext.Teams.Any(team => team.Id == model.Id))
                {
                    Delete(model.Id);
                    dbContext.SaveChanges();
                    var posts = dbContext.Posts.Where(post => post.Team == model.Id).ToList();
                    model.Id = 0;
                    Create(model);
                    dbContext.SaveChanges();
                    var newId = dbContext.Teams.Last().Id;
                    foreach (var post in posts)
                    {
                        post.Team = newId;
                    }
                }
                else
                {
                    Create(model);
                }

                

                dbContext.SaveChanges();
            }
        }

        public void Delete(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Teams.Include("Members").FirstOrDefault(t => t.Id == id);
                while(entity.Members.Count>0)
                {

                    dbContext.Remove(entity.Members.ToArray()[0]);
                    dbContext.SaveChanges();
                }

                dbContext.Remove(entity);
                dbContext.SaveChanges();
            }
        }
        public List<TeamListModel> GetAll()
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var teamMapper = new TeamMapper();
                return dbContext.Teams.Include("Members").Select(e => teamMapper.EntityToListModel(e)).ToList();
            }
        }

        public List<TeamDetailModel> GetAllDetail()
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var teamMapper = new TeamMapper();
                return dbContext.Teams.Include("Members").Select(e => teamMapper.EntityToDetailModel(e)).ToList();
            }
        }

        public List<TeamListModel> GetAllTeamsForUser(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var userMapper = new UserMapper();
                var teamMapper = new TeamMapper();
                var teamEntityList = GetAllDetail();
                var userTeamList = new List<TeamListModel>();
                var repository = new UserRepository(dbContextFactory);
                var user = userMapper.DetailModelToListModel(repository.GetById(id));
                foreach (var team in teamEntityList)
                {
                    if (team.Members.Contains(user)) 
                    {
                        userTeamList.Add(teamMapper.DetailModelToListModel(team));
                    }
                }
                return userTeamList;
            }
        }
    }
}