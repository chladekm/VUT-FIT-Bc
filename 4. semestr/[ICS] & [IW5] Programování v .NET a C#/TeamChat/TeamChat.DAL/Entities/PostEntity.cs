using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Text;
using TeamChat.Utilities;

namespace TeamChat.DAL.Entities
{
    public class PostEntity : MessageEntity
    {
        public string Title { get; set; }
        public Collection<CommentEntity> Comments { get; set; }
        public int Team { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is PostEntity Post))
            {
                return false;
            }

            var Comparer = new CollectionComparer<CommentEntity>();

            return Id == Post.Id &&
                   Author.Equals(Post.Author) &&
                   AuthorName.Equals(Post.AuthorName) &&
                   Text.Equals(Post.Text) &&
                   Date.Equals(Post.Date) &&
                   Title.Equals(Post.Title) &&
                   Team.Equals(Post.Team) &&
                   Comparer.Equals(Comments, Post.Comments);
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
