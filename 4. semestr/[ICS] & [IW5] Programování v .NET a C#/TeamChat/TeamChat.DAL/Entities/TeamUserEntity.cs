using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.DAL.Entities
{
    public class TeamUserEntity : EntityBase
    {
        public int UserId { get; set; }
        public string UserName { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is TeamUserEntity teamUser))
            {
                return false;
            }

            return UserId == teamUser.UserId &&
                   UserName.Equals(teamUser.UserName);
        }

        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + UserId.GetHashCode();
            hash = (hash * 7) + UserName.GetHashCode();

            return hash;
        }
    }

    
}
