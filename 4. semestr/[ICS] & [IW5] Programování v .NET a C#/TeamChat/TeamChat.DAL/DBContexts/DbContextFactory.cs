using System;
using System.Collections.Generic;
using System.Text;
using Microsoft.EntityFrameworkCore;

namespace TeamChat.DAL.DBContexts
{
    public class DbContextFactory : IDbContextFactory
    {
        public TeamChatDbContext CreateDbContext()
        {
            var optionsBuilder = new DbContextOptionsBuilder<TeamChatDbContext>();

            optionsBuilder.UseSqlServer("Data Source=(localdb)\\MSSQLLocalDB;Initial Catalog= TeamChatDB;Integrated Security=True;Connect Timeout=30;Encrypt=False;TrustServerCertificate=False;ApplicationIntent=ReadWrite;MultiSubnetFailover=False");

            return new TeamChatDbContext(optionsBuilder.Options);
        }
    }
}
