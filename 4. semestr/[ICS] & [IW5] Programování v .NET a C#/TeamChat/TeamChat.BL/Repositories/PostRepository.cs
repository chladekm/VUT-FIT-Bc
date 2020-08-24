using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.DAL.DBContexts;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Repositories
{
    public class PostRepository : IGenericRepository<PostModel, PostModel>
    {
        private readonly IDbContextFactory dbContextFactory;

        public PostRepository(IDbContextFactory dbContextFactory)
        {
            this.dbContextFactory = dbContextFactory;
        }


        public PostModel GetById(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Posts.Include("Comments").First(t => t.Id == id);
                var postMapper = new PostMapper();
                return postMapper.EntityToModel(entity);
            }
        }


        public PostModel Create(PostModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var postMapper = new PostMapper();
                var entity = postMapper.ModelToEntity(model);
                dbContext.Posts.Add(entity);                
                dbContext.SaveChanges();
                return postMapper.EntityToModel(entity);
            }
        }


        public void Update(PostModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var postMapper = new PostMapper();
                var entity = postMapper.ModelToEntity(model);
                dbContext.Posts.Update(entity);
                dbContext.SaveChanges();
            }
        }


        public void Delete(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Posts.First(t => t.Id == id);
                dbContext.Remove(entity);
                dbContext.SaveChanges();
            }
        }
        public List<PostModel> GetAll()
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var postMapper = new PostMapper();
                return dbContext.Posts.Include("Comments").Select(e => postMapper.EntityToModel(e)).ToList();
            }
        }
    }
}


