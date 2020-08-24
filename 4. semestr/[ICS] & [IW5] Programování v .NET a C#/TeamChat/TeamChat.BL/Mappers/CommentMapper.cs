using TeamChat.BL.Models;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Mappers
{
    public class CommentMapper
    {
        public CommentModel EntityToModel(CommentEntity commentEntity)
        {
            if (commentEntity == null)
            {
                return null;
            }

            var commentModel = new CommentModel
            {
                Id = commentEntity.Id,
                Author = commentEntity.Author,
                AuthorName = commentEntity.AuthorName,
                Text = commentEntity.Text,
                Date = commentEntity.Date,
                Post = commentEntity.Post
            };

            return commentModel;
        }

        public CommentEntity ModelToEntity(CommentModel commentModel)
        {
            if (commentModel == null)
            {
                return null;
            }

            var commentEntity = new CommentEntity
            {
                Author = commentModel.Author,
                AuthorName = commentModel.AuthorName,
                Date = commentModel.Date,
                Id = commentModel.Id,
                Text = commentModel.Text,
                Post = commentModel.Post
            };

            return commentEntity;
        }
    }
}
