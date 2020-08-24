using System;
using System.Collections.Generic;
using System.Text;
using Microsoft.EntityFrameworkCore;

namespace TeamChat.DAL.DBContexts
{
    public class InMemoryDbContextFactory : IDbContextFactory
    {
        public TeamChatDbContext CreateDbContext()
        {
            var optionsBuilder = new DbContextOptionsBuilder<TeamChatDbContext>();

            optionsBuilder.UseInMemoryDatabase("InMemoryTeamChatDb");

            return new TeamChatDbContext(optionsBuilder.Options);
        }
    }
}
