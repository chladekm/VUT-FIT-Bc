using System.Collections.ObjectModel;
using System.Linq;
using TeamChat.BL.Models;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Mappers
{
    public class UserMapper
    { 
        public UserDetailModel EntityToDetailModel(UserEntity userEntity)
        {
            if (userEntity == null)
            {
                return null;
            }

            var userModel = new UserDetailModel
            {
                Email = userEntity.Email,
                Id = userEntity.Id,
                Name = userEntity.Name,
                Password = userEntity.Password,
                Comments = new Collection<CommentModel>(),
                Posts = new Collection<PostModel>()
            };
            if (userEntity.Comments == null)
            {
                userEntity.Comments = new Collection<CommentEntity>();
            }
            else
            {
                var commentMapper = new CommentMapper();
                foreach (var comment in userEntity.Comments)
                {
                    userModel.Comments.Add(commentMapper.EntityToModel(comment));
                }
            }

            if (userEntity.Posts == null)
            {
                userEntity.Posts = new Collection<PostEntity>();
            }
            else
            {
                foreach (var post in userEntity.Posts)
                {
                    var postMapper = new PostMapper();
                    userModel.Posts.Add(postMapper.EntityToModel(post));
                }
            }

            return userModel;
        }

        public UserLoginModel EntityToLoginModel(UserEntity userEntity)
        {
            if (userEntity == null)
            {
                return null;
            }

            var userLoginModel = new UserLoginModel()
            {
                Id = userEntity.Id,
                Email = userEntity.Email,
                Password = userEntity.Password
            };

            return userLoginModel;
        }

        public UserProfileModel EntityToProfileModel(UserEntity userEntity)
        {
            if (userEntity == null)
            {
                return null;
            }

            var lastComment = userEntity.Comments.OrderByDescending(e => e.Date).FirstOrDefault();
            var lastPost = userEntity.Posts.OrderByDescending(e => e.Date).FirstOrDefault();

            var userProfileModel = new UserProfileModel()
            {
                Id = userEntity.Id,
                Email = userEntity.Email,
                Name = userEntity.Name,
                LastComment = lastComment == null ? null : new CommentMapper().EntityToModel(lastComment),
                LastPost = lastPost == null ? null : new PostMapper().EntityToModel(lastPost)
            };
            return userProfileModel;
        }

        public UserListModel EntityToListModel(UserEntity userEntity)
        {
            if (userEntity == null)
            {
                return null;
            }

            var userModel = new UserListModel()
            {
                Id = userEntity.Id,
                Name = userEntity.Name
            };
            return userModel;
        }

        public UserEntity DetailModelToEntity(UserDetailModel userDetailModel)
        {
            if (userDetailModel == null)
            {
                return null;
            }

            var userEntity = new UserEntity
            {
                Email = userDetailModel.Email,
                Id = userDetailModel.Id,
                Name = userDetailModel.Name,
                Password = userDetailModel.Password,
                Comments = new Collection<CommentEntity>(),
                Posts = new Collection<PostEntity>()
            };

            if (userDetailModel.Comments == null)
            {
                userDetailModel.Comments = new Collection<CommentModel>();
            }
            else
            {
                var commentMapper = new CommentMapper();
                foreach (var comment in userDetailModel.Comments)
                {
                    userEntity.Comments.Add(commentMapper.ModelToEntity(comment));
                }
            }

            if (userDetailModel.Posts == null)
            {
                userDetailModel.Posts = new Collection<PostModel>();
            }
            else
            {
                foreach (var post in userDetailModel.Posts)
                {
                    var postMapper = new PostMapper();
                    userEntity.Posts.Add(postMapper.ModelToEntity(post));
                }
            }

            return userEntity;
        }

        public UserEntity RegistrationModelToEntity(UserRegistrationModel userRegistrationModel)
        {
            if (userRegistrationModel == null)
            {
                return null;
            }

            var userEntity = new UserEntity()
            {
                Id = userRegistrationModel.Id,
                Email = userRegistrationModel.Email,
                Name = userRegistrationModel.Name,
                Password = userRegistrationModel.Password,
                Comments = new Collection<CommentEntity>(),
                Posts = new Collection<PostEntity>()
            };

            return userEntity;
        }

        public UserListModel DetailModelToListModel(UserDetailModel userDetailModel)
        {
            if (userDetailModel == null)
            {
                return null;
            }

            var userListModel = new UserListModel()
            {
                Id = userDetailModel.Id,
                Name = userDetailModel.Name
            };

            return userListModel;
        }

        public UserLoginModel DetailModelToLoginModel(UserDetailModel userDetailModel)
        {
            if (userDetailModel == null)
            {
                return null;
            }

            var userLoginModel = new UserLoginModel()
            {
                Id = userDetailModel.Id,
                Email = userDetailModel.Email,
                Password = userDetailModel.Password
            };

            return userLoginModel;
        }

        public UserProfileModel DetailModelToProfileModel(UserDetailModel userDetailModel)
        {
            if (userDetailModel == null)
            {
                return null;
            }

            var userProfileModel = new UserProfileModel()
            {
                Email = userDetailModel.Email,
                Id = userDetailModel.Id,
                Name = userDetailModel.Name,
                LastComment = userDetailModel.Comments.OrderByDescending(e => e.Date).FirstOrDefault(),
                LastPost = userDetailModel.Posts.OrderByDescending(e => e.Date).FirstOrDefault(),
            };

            return userProfileModel;
        }
    }
}
