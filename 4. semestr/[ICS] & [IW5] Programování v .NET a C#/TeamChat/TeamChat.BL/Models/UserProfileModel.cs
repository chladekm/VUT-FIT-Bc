using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Text;
using TeamChat.Utilities;

namespace TeamChat.BL.Models
{
    public class UserProfileModel
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Email { get; set; }
        public PostModel LastPost { get; set; }
        public CommentModel LastComment { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is UserProfileModel user))
            {
                return false;
            }

            return Id == user.Id &&
                   Name.Equals(user.Name) &&
                   Email.Equals(user.Email);
        }

        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Name.GetHashCode();
            hash = (hash * 7) + Email.GetHashCode();
            hash = (hash * 7) + LastPost.GetHashCode();
            hash = (hash * 7) + LastComment.GetHashCode();

            return hash;
        }
    }
}
