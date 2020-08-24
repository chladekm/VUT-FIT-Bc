using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Text;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.DAL.Entities;
using Xunit;

namespace TeamChat.BL.Tests
{
    public class MapperTests
    {
        [Fact]
        public void CommentEntityModelTest()
        {
            var commentMapper = new CommentMapper();

            Assert.Null(commentMapper.EntityToModel(null));
            Assert.Null(commentMapper.ModelToEntity(null));

            var UserEntityObject = new UserEntity()
            {
                Id = 1,
                Name = "Jan",
                Email = "janko123@fmail.kom",
                Password = "testujeme123",
            };

            var CommentEntityObject = new CommentEntity()
            {
                Id = 1,
                Author = UserEntityObject.Id,
                AuthorName = UserEntityObject.Name,
                Text = "NONI?"
            };

            var UserModelObject = new UserDetailModel()
            {
                Id = 1,
                Name = "Jan",
                Email = "janko123@fmail.kom",
                Password = "testujeme123",
            };

            var CommentModelObject = new CommentModel()
            {
                Id = 1,
                Author = UserModelObject.Id,
                AuthorName = UserModelObject.Name,
                Text = "NONI?"
            };

            Assert.Equal(CommentModelObject, commentMapper.EntityToModel(CommentEntityObject));
            Assert.Equal(CommentEntityObject, commentMapper.ModelToEntity(CommentModelObject));
        }

        [Fact]
        public void PostEntityModelTest()
        {
            var postMapper = new PostMapper();
            var userMapper = new UserMapper();

            Assert.Null(postMapper.EntityToModel(null));
            Assert.Null(postMapper.ModelToEntity(null));

            var UserEntityObject = new UserEntity()
            {
                Id = 4,
                Name = "Anton",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk",
            };

            var TeamEntityObject = new TeamEntity()
            {
                Id = 2,
                Leader = UserEntityObject.Id,
                Members = new Collection<TeamUserEntity>(),
                Name = "Team1"
            };

            var PostEntityObject = new PostEntity()
            {
                Id = 4,
                Author = UserEntityObject.Id,
                AuthorName = UserEntityObject.Name,
                Date = new DateTime(2019, 4, 1),
                Team = TeamEntityObject.Id,
                Text = "Toto je testovaci prispevok",
                Title = "Titulok"
            };

            var CommentEntity1 = new CommentEntity()
            {
                Author = UserEntityObject.Id,
                AuthorName = UserEntityObject.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var CommentEntity2 = new CommentEntity()
            {
                Author = UserEntityObject.Id,
                AuthorName = UserEntityObject.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment cislo 2"
            };

            

            var UserModelObject = new UserDetailModel()
            {
                Id = 4,
                Name = "Anton",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk",
            };

            var TeamModelObject = new TeamDetailModel()
            {
                Id = 2,
                Leader = UserModelObject.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };

            var PostModelObject = new PostModel()
            {
                Id = 4,
                Author = UserModelObject.Id,
                AuthorName = UserModelObject.Name,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 4, 1),
                Team = TeamModelObject.Id,
                Text = "Toto je testovaci prispevok",
                Title = "Titulok"
            };

            Assert.Equal(PostModelObject, postMapper.EntityToModel(PostEntityObject));

            PostEntityObject.Comments = new Collection<CommentEntity>();

            var Comment1 = new CommentModel()
            {
                Author = UserModelObject.Id,
                AuthorName = UserModelObject.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var Comment2 = new CommentModel()
            {
                Author = UserModelObject.Id,
                AuthorName = UserModelObject.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment cislo 2"
            };

            PostModelObject.Comments = null;

            Assert.Equal(PostEntityObject, postMapper.ModelToEntity(PostModelObject));

            PostModelObject.Comments = new Collection<CommentModel>();

            TeamEntityObject.Members.Add(new TeamUserEntity(){UserId = UserEntityObject.Id, UserName = UserEntityObject.Name});
            PostEntityObject.Comments.Add(CommentEntity1);
            PostEntityObject.Comments.Add(CommentEntity2);
            TeamModelObject.Members.Add(userMapper.DetailModelToListModel(UserModelObject));
            PostModelObject.Comments.Add(Comment1);
            PostModelObject.Comments.Add(Comment2);

            Assert.Equal(PostModelObject, postMapper.EntityToModel(PostEntityObject));
            Assert.Equal(PostEntityObject, postMapper.ModelToEntity(PostModelObject));

        }

        [Fact]
        public void UserEntityModelTest()
        {
            var userMapper = new UserMapper();

            Assert.Null(userMapper.EntityToDetailModel(null));
            Assert.Null(userMapper.DetailModelToEntity(null));
            Assert.Null(userMapper.EntityToListModel(null));

            var UserEntityObject = new UserEntity()
            {
                Id = 1,
                Name = "Jan",
                Email = "janko123@fmail.kom",
                Password = "testujeme123",
            };

            var UserModelObject = new UserDetailModel()
            {
                Id = 1,
                Name = "Jan",
                Email = "janko123@fmail.kom",
                Password = "testujeme123",
            };

            var Comment1 = new CommentEntity()
            {
                Author = 12,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var Comment2 = new CommentEntity()
            {
                Author = 12,
                Date = new DateTime(2019, 5, 4),
                Id = 2,
                Text = "Testovaci koment cislo 2"
            };

            var CommentModel = new CommentModel()
            {
                Author = 12,
                Date = new DateTime(2019, 5, 4),
                Id = 2,
                Text = "Testovaci koment cislo 2"
            };
            var CommentModel1 = new CommentModel()
            {
                Author = 12,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };

            var PostEntity = new PostEntity()
            {
                Id = 6,
                Author = 12,
                Comments = new Collection<CommentEntity>(),
                Date = new DateTime(2019, 5, 4),
                Team = 2,
                Text = "Post",
                Title = "Titulok"
            };

            PostEntity.Comments.Add(Comment1);
            PostEntity.Comments.Add(Comment2);

            var PostModel = new PostModel()
            {
                Id = 6,
                Author = 12,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 5, 4),
                Team = 2,
                Text = "Post",
                Title = "Titulok"
            };

            PostModel.Comments.Add(CommentModel1);
            PostModel.Comments.Add(CommentModel);

            Assert.Equal(UserModelObject, userMapper.EntityToDetailModel(UserEntityObject));
            Assert.Equal(UserEntityObject, userMapper.DetailModelToEntity(UserModelObject));

            UserEntityObject.Comments = new Collection<CommentEntity>();
            UserModelObject.Comments = new Collection<CommentModel>();

            UserEntityObject.Comments.Add(Comment1);
            UserEntityObject.Comments.Add(Comment2);
            UserModelObject.Comments.Add(CommentModel1);
            UserModelObject.Comments.Add(CommentModel);

            Assert.Equal(UserModelObject, userMapper.EntityToDetailModel(UserEntityObject));
            Assert.Equal(UserEntityObject, userMapper.DetailModelToEntity(UserModelObject));

            UserModelObject.Posts = new Collection<PostModel>();
            UserEntityObject.Posts = new Collection<PostEntity>();
            UserEntityObject.Posts.Add(PostEntity);    
            UserModelObject.Posts.Add(PostModel);

            Assert.Equal(UserModelObject, userMapper.EntityToDetailModel(UserEntityObject));
            Assert.Equal(UserEntityObject, userMapper.DetailModelToEntity(UserModelObject));
        }

        [Fact]
        public void TeamEntityModelTest()
        {
            var teamMapper = new TeamMapper();
            var userMapper = new UserMapper();

            Assert.Null(teamMapper.EntityToDetailModel(null));
            Assert.Null(teamMapper.DetailModelToEntity(null));

            var UserEntityObject = new UserEntity()
            {
                Id = 4,
                Name = "Anton",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk",
                Comments = new Collection<CommentEntity>(),
                Posts = new Collection<PostEntity>()
            };

            var TeamEntityObject = new TeamEntity()
            {
                Id = 2,
                Leader = UserEntityObject.Id,
                Name = "Team1"
            };


            var UserModelObject = new UserDetailModel()
            {
                Id = 4,
                Name = "Anton",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk",
                Comments = new Collection<CommentModel>(),
                Posts = new Collection<PostModel>()
            };

            var TeamModelObject = new TeamDetailModel()
            {
                Id = 2,
                Leader = UserModelObject.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };

            Assert.Equal(TeamModelObject, teamMapper.EntityToDetailModel(TeamEntityObject));

            TeamEntityObject.Members = new Collection<TeamUserEntity>();

            TeamModelObject.Members = null;

            Assert.Equal(TeamEntityObject, teamMapper.DetailModelToEntity(TeamModelObject));

            TeamModelObject.Members = new Collection<UserListModel>();

            TeamEntityObject.Members.Add(new TeamUserEntity(){UserId = UserEntityObject.Id, UserName = UserEntityObject.Name});
            TeamModelObject.Members.Add(userMapper.DetailModelToListModel(UserModelObject));

            Assert.Equal(TeamModelObject, teamMapper.EntityToDetailModel(TeamEntityObject));
            Assert.Equal(TeamEntityObject, teamMapper.DetailModelToEntity(TeamModelObject));
        }

        [Fact]
        public void UserProfileModel()
        {
            var userMapper = new UserMapper();

            Assert.Null(userMapper.EntityToProfileModel(null));
            var UserEntity = new UserEntity()
            {
                Id = 12,
                Comments = new Collection<CommentEntity>(),
                Name = "Milos Hlava",
                Email = "milos@hlava.ks",
                Password = "velmisofistikovanehesloktoreniknezlomi",
                Posts = new Collection<PostEntity>()
            };

            var Comment1 = new CommentEntity()
            {
                Author = 12,
                AuthorName = UserEntity.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var Comment2 = new CommentEntity()
            {
                Author = 12,
                AuthorName = UserEntity.Name,
                Date = new DateTime(2019, 5, 4),
                Id = 2,
                Text = "Testovaci koment cislo 2"
            };

            var CommentModel = new CommentModel()
            {
                Author = 12,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 5, 4),
                Id = 2,
                Text = "Testovaci koment cislo 2"
            };
            var CommentModel1 = new CommentModel()
            {
                Author = 12,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };

            var PostEntity = new PostEntity()
            {
                Id = 6,
                Author = 12,
                AuthorName = "Milos Hlava",
                Comments = new Collection<CommentEntity>(),
                Date = new DateTime(2019, 5, 4),
                Team = 2,
                Text = "Post",
                Title = "Titulok"
            };

            PostEntity.Comments.Add(Comment1);
            PostEntity.Comments.Add(Comment2);

            var PostModel = new PostModel()
            {
                Id = 6,
                Author = 12,
                AuthorName = "Milos Hlava",
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 5, 4),
                Team = 2,
                Text = "Post",
                Title = "Titulok"
            };

            PostModel.Comments.Add(CommentModel1);
            PostModel.Comments.Add(CommentModel);

            UserEntity.Comments.Add(Comment1);
            UserEntity.Comments.Add(Comment2);
            UserEntity.Posts.Add(PostEntity);

            var UserProfile = new UserProfileModel()
            {
                Id = 12,
                Name = "Milos Hlava",
                Email = "milos@hlava.ks",
                LastComment = CommentModel,
                LastPost = PostModel
            };

            Assert.Equal(UserProfile, userMapper.EntityToProfileModel(UserEntity));
            Assert.True(CommentModel.Equals(userMapper.EntityToProfileModel(UserEntity).LastComment));
            Assert.True(PostModel.Equals(userMapper.EntityToProfileModel(UserEntity).LastPost));
        }

        [Fact]
        public void UserLoginModel()
        {
            var userMapper = new UserMapper();

            var UserEntity = new UserEntity()
            {
                Id = 12,
                Comments = new Collection<CommentEntity>(),
                Name = "Milos Hlava",
                Email = "milos@hlava.ks",
                Password = "velmisofistikovanehesloktoreniknezlomi",
                Posts = new Collection<PostEntity>()
            };


            var UserLoginModel = new UserLoginModel()
            {
                Id = 12,
                Email = "milos@hlava.ks",
                Password = "velmisofistikovanehesloktoreniknezlomi"
            };

            Assert.Equal(UserLoginModel, userMapper.EntityToLoginModel(UserEntity));
            Assert.Null(userMapper.EntityToLoginModel(null));
        }

        [Fact]
        public void UserRegisterModel()
        {
            var userMapper = new UserMapper();

            var UserEntity = new UserEntity()
            {
                Id = 12,
                Comments = new Collection<CommentEntity>(),
                Name = "Milos Hlava",
                Email = "milos@hlava.ks",
                Password = "velmisofistikovanehesloktoreniknezlomi",
                Posts = new Collection<PostEntity>()
            };


            var UserRegisterModel = new UserRegistrationModel()
            {
                Id = 12,
                Email = "milos@hlava.ks",
                Password = "velmisofistikovanehesloktoreniknezlomi",
                RepeatedPassword = "velmisofistikovanehesloktoreniknezlomi",
                Name = "Milos Hlava"
            };

            Assert.Equal(UserEntity, userMapper.RegistrationModelToEntity(UserRegisterModel));
            Assert.Null(userMapper.EntityToLoginModel(null));
        }
    }
}
