using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.DAL.DBContexts
{
    public interface IDbContextFactory
    {
        TeamChatDbContext CreateDbContext();
    }
}
