using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text;
using TeamChat.Utilities;

namespace TeamChat.BL.Models
{
    public class PostListModel
    {
        public int Id { get; set; }
        public Collection<PostModel> Posts { get; set; }
        public int Team { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is PostListModel posts))
            {
                return false;
            }

            var comparer = new CollectionComparer<PostModel>();

            return Id == posts.Id &&
                   Team.Equals(posts.Team) &&
                   comparer.Equals(Posts, posts.Posts);
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Team.GetHashCode();

            return hash;
        }
    }
}
