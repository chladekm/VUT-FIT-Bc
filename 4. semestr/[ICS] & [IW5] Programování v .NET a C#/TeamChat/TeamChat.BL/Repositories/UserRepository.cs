using System;
using System.Collections.Generic;
using System.Linq;
using Microsoft.EntityFrameworkCore;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.DAL.DBContexts;

namespace TeamChat.BL.Repositories
{
    public class UserRepository : IGenericRepository<UserDetailModel, UserListModel>
    {
        private readonly IDbContextFactory dbContextFactory;

        public UserRepository(IDbContextFactory dbContextFactory)
        {
            this.dbContextFactory = dbContextFactory;
        }

        public UserDetailModel GetById(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Users.Include("Posts").Include("Comments").First(t => t.Id == id);
                var userMapper = new UserMapper();
                return userMapper.EntityToDetailModel(entity);
            }
        }

        public UserLoginModel GetByEmail(string email)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Users.Include("Posts").Include("Comments").First(t => t.Email == email);
                var userMapper = new UserMapper();
                return userMapper.EntityToLoginModel(entity);
            }
        }

        public UserDetailModel Create(UserDetailModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var userMapper = new UserMapper();
                var entity = userMapper.DetailModelToEntity(model);
                dbContext.Users.Add(entity);
                dbContext.SaveChanges();
                return userMapper.EntityToDetailModel(entity);
            }
        }

        public void Update(UserDetailModel model)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var userMapper = new UserMapper();
                var entity = userMapper.DetailModelToEntity(model);
                dbContext.Users.Update(entity);
                dbContext.SaveChanges();
            }
        }

        public void Delete(int id)
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var entity = dbContext.Users.First(t => t.Id == id);
                dbContext.Remove(entity);
                dbContext.SaveChanges();
            }
        }
        public List<UserListModel> GetAll()
        {
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var userMapper = new UserMapper();
                return dbContext.Users.Include("Posts").Include("Comments").Select(e => userMapper.EntityToListModel(e)).ToList();
            }
        }
    }
}