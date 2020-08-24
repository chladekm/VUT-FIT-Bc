using System;
using TeamChat.BL.Models;

namespace TeamChat.BL.Messages
{
    public class CommentSelectedMessage : IMessage
    {
        public CommentModel CommentModel { get; set; }
    }
}