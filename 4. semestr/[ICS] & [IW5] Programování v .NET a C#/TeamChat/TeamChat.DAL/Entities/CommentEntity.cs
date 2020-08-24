using System;
using System.Runtime;
using System.Text;

namespace TeamChat.DAL.Entities
{
    public class CommentEntity : MessageEntity
    {
        public int Post { get; set; }
        public override bool Equals(object obj)
        {
            if (!(obj is CommentEntity Comment))
            {
                return false;
            }

            return Id == Comment.Id &&
                   Author.Equals(Comment.Author) &&
                   AuthorName.Equals(Comment.AuthorName) &&
                   Text.Equals(Comment.Text) &&
                   Date.Equals(Comment.Date);
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

            return hash;
        }

    }

    
}
