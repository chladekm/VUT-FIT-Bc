using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text;
using TeamChat.BL.Mappers;
using TeamChat.BL.Models;
using TeamChat.BL.Repositories;
using TeamChat.DAL.DBContexts;
using TeamChat.DAL.Entities;
using TeamChat.Utilities;
using Xunit;

namespace TeamChat.BL.Tests
{
    public class RepozitoryTests
    {
        [Fact]
        public void PostRepozitoryTest()
        {
            var Repository = new PostRepository(new InMemoryDbContextFactory());

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(1));

            var Post = new PostModel()
            {
                Id = 1,
                Author = 3,
                AuthorName = "Milos Hlava",
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 1, 4),
                Team = 6,
                Text = "No pozrite sa na tie komenty",
                Title = "Lol"
            };
            var Post2 = new PostModel()
            {
                Id = 2,
                Author = 3,
                AuthorName = "Milos Hlava",
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 2, 4),
                Team = 6,
                Text = "Druhy post",
                Title = "Lol"
            };
            var Comment1 = new CommentModel()
            {
                Author = 10,
                AuthorName = "Jozej=f Hlava",
                Date = new DateTime(2019, 1, 4),
                Id = 11,
                Text = "Testovaci koment",
                Post = 1
            };

            var Comment2 = new CommentModel()
            {
                Author = 10,
                AuthorName = "Jozef Hlava",
                Date = new DateTime(2019, 1, 5),
                Id = 20,
                Text = "Testovaci koment cislo 2",
                Post = 1
            };

            Post.Comments.Add(Comment1);
            Post.Comments.Add(Comment2);
            Repository.Create(Post);
            var ReceivedPost = Repository.GetById(1);

            Assert.Equal(Post, ReceivedPost);

            Post.Text = "Zmeneny text";
            Repository.Update(Post);
            ReceivedPost = Repository.GetById(1);

            Assert.Equal(Post, ReceivedPost);

            Repository.Create(Post2);
            List<PostModel> AllPosts = new List<PostModel>();
            AllPosts.Add(Post);
            AllPosts.Add(Post2);
            List<PostModel> ReceivedAllPosts = Repository.GetAll();

            CollectionComparer<PostModel> comparer = new CollectionComparer<PostModel>();
            Assert.True(comparer.Equals(AllPosts, ReceivedAllPosts));

            Repository.Delete(1);

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(1));
        }

        [Fact]
        public void CommentRepozitoryTest()
        {
            var Repository = new CommentRepository(new InMemoryDbContextFactory());

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(0));

            var Comment1 = new CommentModel()
            {
                Author = 1,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 1,4),
                Id = 1,
                Text = "Testovaci koment"
            };

            var Comment2 = new CommentModel()
            {
                Author = 2,
                AuthorName = "Jozef Hlava",
                Date = new DateTime(2019, 1, 5),
                Id = 2,
                Text = "Testovaci koment cislo 2"
            };

            Repository.Create(Comment1);
            Repository.Create(Comment2);
            var ReceivedComment1 = Repository.GetById(1);
            var ReceivedComment2 = Repository.GetById(2);

            Assert.Equal(Comment1, ReceivedComment1);
            Assert.Equal(Comment2, ReceivedComment2);

            Comment1.Text = "Updatovany text";
            Repository.Update(Comment1);
            ReceivedComment1 = Repository.GetById(1);

            Assert.Equal(Comment1, ReceivedComment1);

            List<CommentModel> ReceivedAllComments = Repository.GetAll();

            var AllComments = new List<CommentModel>();
            AllComments.Add(Comment1);
            AllComments.Add(Comment2);

            var Comparer = new CollectionComparer<CommentModel>();

            Assert.True(Comparer.Equals(AllComments, ReceivedAllComments));

            Repository.Delete(1);

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(1));

            Repository.Delete(2);

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(2));
        }

        [Fact]
        public void UserRepozitoryTest()
        {
            var factory = new InMemoryDbContextFactory();
            var Repository = new UserRepository(factory);

            

            using(var dbContext = factory.CreateDbContext())
            {
                dbContext.Database.EnsureDeleted();
            }

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(1));

            var User1 = new UserDetailModel()

            {
                Id = 4,
                Name = "Anton",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk",
            };

            var User2 = new UserDetailModel()
            {
                Id = 2,
                Name = "Anton",
                Password = "lotr123456789",
                Email = "gandalf@email.sk",
            };

            Repository.Create(User1);
            Repository.Create(User2);

            var ReceivedUser = Repository.GetById(4);

            Assert.Equal(User1, ReceivedUser);

            var userMapper = new UserMapper();

            var AllUsers = new List<UserListModel>();
            AllUsers.Add(userMapper.DetailModelToListModel(User1));
            AllUsers.Add(userMapper.DetailModelToListModel(User2));
            var ReceivedAllUsers = Repository.GetAll();

            var Comparer = new CollectionComparer<UserListModel>();

            Assert.True(Comparer.Equals(AllUsers, ReceivedAllUsers));

            User1.Name = "Antonko";

            Repository.Update(User1);
            ReceivedUser = Repository.GetById(4);

            Assert.Equal(User1, ReceivedUser);

            Repository.Delete(4);

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(4));
        }

        [Fact]
        public void TeamRepozitoryTest()
        {
            var dbContextFactory = new InMemoryDbContextFactory();
            var Repository = new TeamRepository(dbContextFactory);
            var UserRepository = new UserRepository(dbContextFactory);

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(0));

            var Post = new PostModel()
            {
                Id = 1,
                Author = 4,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 1, 4),
                Team = 2,
                Text = "No pozrite sa na tie komenty",
                Title = "Lol"
            };
            var Comment1 = new CommentModel()
            {
                Author = 2,
                Date = new DateTime(2019, 1, 4),
                Id = 11,
                Text = "Testovaci koment",
                Post = 1
            };

            var Comment2 = new CommentModel()
            {
                Author = 1,
                Date = new DateTime(2019, 1, 5),
                Id = 20,
                Text = "Testovaci koment cislo 2",
                Post = 1
            };

            Post.Comments.Add(Comment1);
            Post.Comments.Add(Comment2);

            var User1 = new UserDetailModel()
            {
                Id = 4,
                Name = "Anton",
                Comments = new Collection<CommentModel>(),
                Email = "tono@jeboh.je",
                Password = "123",
                Posts = new Collection<PostModel>()
            };

            var User2 = new UserDetailModel()
            {
                Id = 2,
                Name = "Tomas",
                Email = "tomas@jedno.org",
                Password = "admin",
                Comments = new Collection<CommentModel>(),
                Posts = new Collection<PostModel>()
            };
            var User3 = new UserDetailModel()
            {
                Id = 3,
                Name = "Sergej",
                Email = "sergej@rusko.sk",
                Password = "putin",
                Comments = new Collection<CommentModel>(),
                Posts = new Collection<PostModel>()
            };

            var Team1 = new TeamDetailModel()
            {
                Id = 2,
                Leader = User2.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1",
                
            };
            var Team2 = new TeamDetailModel()
            {
                Id = 3,
                Leader = User3.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team2",

            };

            UserRepository.Create(User1);
            UserRepository.Create(User2);
            UserRepository.Create(User3);

            var userMapper = new UserMapper();

            Team1.Members.Add(userMapper.DetailModelToListModel(User1));
            Team1.Members.Add(userMapper.DetailModelToListModel(User2));

            Team2.Members.Add(userMapper.DetailModelToListModel(User2));
            Team2.Members.Add(userMapper.DetailModelToListModel(User3));

            Repository.Create(Team1);
            Repository.Create(Team2);

            var ReceivedTeam = Repository.GetById(2);

            Assert.Equal(Team1, ReceivedTeam);

            ReceivedTeam = Repository.GetById(3);

            Assert.Equal(Team2, ReceivedTeam);

            var AllTeams = new List<TeamListModel>();
            var AllTeamsDetail = new List<TeamDetailModel>();
            var ReceivedAllTeams = Repository.GetAll();
            var ReceivedAllTeamsDetail = Repository.GetAllDetail();

            var teamMapper = new TeamMapper();
            
            AllTeams.Add(teamMapper.DetailModelToListModel(Team1));
            AllTeams.Add(teamMapper.DetailModelToListModel(Team2));
            AllTeamsDetail.Add(Team1);
            AllTeamsDetail.Add(Team2);

            var Comparer = new CollectionComparer<TeamListModel>();

            Assert.True(Comparer.Equals(AllTeams, ReceivedAllTeams));

            var DetailComparer = new CollectionComparer<TeamDetailModel>();

            Assert.True(DetailComparer.Equals(AllTeamsDetail, ReceivedAllTeamsDetail));

            var AllTeamsForUser1 = new List<TeamListModel>();
            AllTeamsForUser1.Add(teamMapper.DetailModelToListModel(Team1));

            var ReceivedAllTeamsForUser1 = Repository.GetAllTeamsForUser(User1.Id);

            Assert.True(Comparer.Equals(AllTeamsForUser1, ReceivedAllTeamsForUser1));

            Team1.Name = "Zmenene meno";

            Repository.Update(Team1);
            Assert.Equal(Team1, Repository.GetById(Team1.Id));

            Repository.Delete(3);

            Assert.Throws<System.InvalidOperationException>(() => Repository.GetById(0));

            ReceivedTeam = Repository.GetById(2);

            Assert.Equal(Team1, ReceivedTeam);
        }
    }
}
