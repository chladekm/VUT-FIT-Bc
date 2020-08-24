using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.BL.Models
{
    public class CommentModel
    {
        public int Id { get; set; }
        public int Author { get; set; }
        public string AuthorName { get; set; }
        public string Text { get; set; }
        public DateTime Date { get; set; }
        public int Post { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is CommentModel comment))
            {
                return false;
            }

            return Id == comment.Id &&
                   Author.Equals(comment.Author) &&
                   AuthorName.Equals(comment.AuthorName) &&
                   Text.Equals(comment.Text) &&
                   Date.Equals(comment.Date);
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Author.GetHashCode();
            hash = (hash * 7) + Text.GetHashCode();
            hash = (hash * 7) + Date.GetHashCode();

            return hash;
        }
    }
}
