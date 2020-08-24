using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text;
using TeamChat.Utilities;

namespace TeamChat.BL.Models
{
    public class PostModel
    {
        public int Id { get; set; }
        public int Author { get; set; }
        public string AuthorName { get; set; }
        public string Text { get; set; }
        public DateTime Date { get; set; }
        public string Title { get; set; }
        public Collection<CommentModel> Comments { get; set; }
        public int Team { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is PostModel post))
            {
                return false;
            }

            var comparer = new CollectionComparer<CommentModel>();

            return Id == post.Id &&
                   Author.Equals(post.Author) &&
                   AuthorName.Equals(post.AuthorName) &&
                   Text.Equals(post.Text) &&
                   Date.Equals(post.Date) &&
                   Title.Equals(post.Title) &&
                   Team.Equals(post.Team) &&
                   comparer.Equals(Comments, post.Comments);
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Author.GetHashCode();
            hash = (hash * 7) + AuthorName.GetHashCode();
            hash = (hash * 7) + Text.GetHashCode();
            hash = (hash * 7) + Date.GetHashCode();
            hash = (hash * 7) + Title.GetHashCode();
            hash = (hash * 7) + Team.GetHashCode();

            return hash;
        }
    }
}
