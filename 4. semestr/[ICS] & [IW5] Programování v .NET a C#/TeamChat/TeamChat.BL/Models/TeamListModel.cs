using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.BL.Models
{
    public class TeamListModel
    {
        public int Id { get; set; }
        public string Name { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is TeamListModel team))
            {
                return false;
            }

            return Id == team.Id &&
                   Name.Equals(team.Name);
        }

        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Name.GetHashCode();
            return hash;
        }
    }
}
