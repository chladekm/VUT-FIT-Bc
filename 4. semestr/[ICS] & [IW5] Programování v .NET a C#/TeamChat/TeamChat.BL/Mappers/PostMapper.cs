using System.Collections.ObjectModel;
using TeamChat.BL.Models;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Mappers
{
    public class PostMapper
    {
        public PostModel EntityToModel(PostEntity postEntity)
        {
            if (postEntity == null)
            {
                return null;
            }

            var postModel = new PostModel
            {
                Id = postEntity.Id,
                Author = postEntity.Author,
                AuthorName = postEntity.AuthorName,
                Text = postEntity.Text,
                Date = postEntity.Date,
                Title = postEntity.Title,
                Team = postEntity.Team,
                Comments = new Collection<CommentModel>()
            };
            if (postEntity.Comments == null)
            {
                postEntity.Comments = new Collection<CommentEntity>();
            }
            else
            {
                var commentMapper= new CommentMapper();
                foreach (var comment in postEntity.Comments)
                {
                    postModel.Comments.Add(commentMapper.EntityToModel(comment));
                }
            }
            return postModel;
        }

        public PostEntity ModelToEntity(PostModel postModel)
        {
            if (postModel == null)
            {
                return null;
            }

            var postEntity = new PostEntity
            {
                Author = postModel.Author,
                AuthorName = postModel.AuthorName,
                Date = postModel.Date,
                Id = postModel.Id,
                Team = postModel.Team,
                Text = postModel.Text,
                Title = postModel.Title,
                Comments = new Collection<CommentEntity>()
            };

            if (postModel.Comments == null)
            {
                postModel.Comments = new Collection<CommentModel>();
            }
            else
            {
                var commentMapper = new CommentMapper();
                foreach (var comment in postModel.Comments)
                {
                    postEntity.Comments.Add(commentMapper.ModelToEntity(comment));
                }
            }
            return postEntity;
        }
    }
}
