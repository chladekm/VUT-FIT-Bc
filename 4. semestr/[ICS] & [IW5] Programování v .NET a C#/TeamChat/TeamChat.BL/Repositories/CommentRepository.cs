using System;
using System.Collections.Generic;
using System.Linq;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.DAL.DBContexts;

namespace TeamChat.BL.Repositories
{
    public class CommentRepository : IGenericRepository<CommentModel, CommentModel>
    {
        private readonly IDbContextFactory dbContextFactory;

        public CommentRepository(IDbContextFactory dbContextFactory)
        {
            this.dbContextFactory = dbContextFactory;
        }


        public CommentModel GetById(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Comments.First(t => t.Id == id);
                var mapper = new CommentMapper();
                return mapper.EntityToModel(entity);
            }
        }


        public CommentModel Create(CommentModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var commentMapper = new CommentMapper();
                var entity = commentMapper.ModelToEntity(model);
                dbContext.Comments.Add(entity);
                dbContext.SaveChanges();
                return commentMapper.EntityToModel(entity);
            }
        }


        public void Update(CommentModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var commentMapper = new CommentMapper();
                var entity = commentMapper.ModelToEntity(model);
                dbContext.Comments.Update(entity);
                dbContext.SaveChanges();
            }
        }


        public void Delete(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Comments.First(t => t.Id == id);
                dbContext.Remove(entity);
                dbContext.SaveChanges();
            }
        }
        public List<CommentModel> GetAll()
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var commentMapper = new CommentMapper();
                return dbContext.Comments.Select(e => commentMapper.EntityToModel(e)).ToList();
            }
        }
    }
}
