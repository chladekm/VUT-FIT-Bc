using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.BL.Models
{
    public class UserListModel
    {
        public int Id { get; set; }
        public string Name { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is UserListModel user))
            {
                return false;
            }

            return Id == user.Id &&
                   Name.Equals(user.Name);
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
