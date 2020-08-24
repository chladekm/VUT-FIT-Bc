using System;
using System.Collections.Generic;
using System.Text;
using TeamChat.DAL.Entities;

namespace TeamChat.DAL
{
    public abstract class MessageEntity : EntityBase
    {
        public int Author { get; set; }
        public string AuthorName { get; set; }
        public string Text { get; set; }
        public DateTime Date { get; set; }
    }
}
