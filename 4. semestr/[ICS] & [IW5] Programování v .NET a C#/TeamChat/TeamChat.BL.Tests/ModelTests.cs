using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text;
using TeamChat.BL.Models;
using Xunit;

namespace TeamChat.BL.Tests
{
    public class ModelTests
    {
        [Fact]
        public void CommentsEquals()
        {
            var Comment = new CommentModel()
            {
                Author = 1,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 1,4),
                Id = 1,
                Text = "Testovaci koment"
            };

            var TheSameComment = new CommentModel()
            {
                Author = 1,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };

            Assert.True(Comment.Equals(TheSameComment));
            Assert.Equal(Comment.GetHashCode(), Comment.GetHashCode());
        }

        [Fact]
        public void CommentsNotEquals()
        {
            var Comment = new CommentModel()
            {
                Author = 1,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };

            var NotTheSameComment = new CommentModel()
            {
                Author = 1,
                AuthorName = "Milos Hlava",
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment je toto"
            };

            Assert.False(Comment.Equals(NotTheSameComment));
        }

        [Fact]
        public void UserEquals()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Jozef"
            };

            var Team = new TeamDetailModel()
            {
                Id = 1,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Testeri"
            };

            var TheSameUser = new UserListModel()
            {
                Id = 4,
                Name = "Jozef"
            };

            var TheSameTeam = new TeamDetailModel()
            {
                Id = 1,
                Leader = TheSameUser.Id,
                Members = new Collection<UserListModel>(),
                Name = "Testeri"
            };

            Assert.True(User.Equals(TheSameUser));

            Team.Members.Add(User);
            TheSameTeam.Members.Add(TheSameUser);

            Assert.True(User.Equals(TheSameUser));

            Assert.Equal(User.GetHashCode(), User.GetHashCode());
        }

        [Fact]
        public void UserNotEquals()
        {
            var User = new UserDetailModel()
            {
                Id = 4,
                Name = "Anton",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk"
            };


            var NotTheSameUser = new UserDetailModel()
            {
                Id = 4,
                Name = "Jozef",
                Password = "fezoJ123",
                Email = "jozo@pokec.sk"
            };

            Assert.False(User.Equals(NotTheSameUser));
        }

        [Fact]
        public void UserNotEqualsTeam()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };

            var Team = new TeamDetailModel()
            {
                Id = 2,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };

            var UserListModel = new UserListModel()
            {
                Id = User.Id,
                Name = User.Name
            };
            var TeamListModel = new TeamListModel()
            {
                Id = Team.Id,
                Name = Team.Name
            };

            Team.Members.Add(User);

            Assert.False(User.Equals(Team));
            Assert.False(Team.Equals(User));
        }

        [Fact]
        public void TeamsEquals()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };
            var Team = new TeamDetailModel()
            {
                Id = 2,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };
           

            var TheSameUser = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };
            var TheSameTeam = new TeamDetailModel()
            {
                Id = 2,
                Leader = TheSameUser.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };

            Assert.True(Team.Equals(TheSameTeam));

            TheSameTeam.Members.Add(TheSameUser);
            Team.Members.Add(User);

            Assert.True(Team.Equals(TheSameTeam));
            Assert.Equal(Team.GetHashCode(), Team.GetHashCode());
        }

        [Fact]
        public void TeamsNotEquals()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };
            var Team = new TeamDetailModel()
            {
                Id = 2,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };

            Team.Members.Add(User);

            var TheSameUser = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };
            var NotTheSameTeam = new TeamDetailModel()
            {
                Id = 2,
                Leader = TheSameUser.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team12"
            };
  
            Assert.False(Team.Equals(NotTheSameTeam));
        }

        [Fact]
        public void PostEquals()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };

            var Team = new TeamDetailModel()
            {
                Id = 2,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };
            var CommentAuthor = new UserListModel()
            {
                Id = 1,
                Name = "Igor",
            };

            var Post = new PostModel()
            {
                Id = 4,
                Author = User.Id,
                AuthorName = User.Name,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 4,1),
                Team = Team.Id,
                Text = "Toto je testovaci prispevok",
                Title = "Titulok"
            };

            var Comment1 = new CommentModel()
            {
                Author = CommentAuthor.Id,
                AuthorName = CommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var Comment2 = new CommentModel()
            {
                Author = CommentAuthor.Id,
                AuthorName = CommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment cislo 2"
            };

            Team.Members.Add(User);
            Team.Members.Add(CommentAuthor);
            Post.Comments.Add(Comment1);
            Post.Comments.Add(Comment2);

            var TheSameUser = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };

            var TheSameTeam = new TeamDetailModel()
            {
                Id = 2,
                Leader = TheSameUser.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };
            var TheSameCommentAuthor = new UserListModel()
            {
                Id = 1,
                Name = "Igor",
            };

            var TheSamePost = new PostModel()
            {
                Id = 4,
                Author = TheSameUser.Id,
                AuthorName = TheSameUser.Name,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 4, 1),
                Team = TheSameTeam.Id,
                Text = "Toto je testovaci prispevok",
                Title = "Titulok"
            };

            var TheSameComment1 = new CommentModel()
            {
                Author = TheSameCommentAuthor.Id,
                AuthorName = TheSameCommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var TheSameComment2 = new CommentModel()
            {
                Author = TheSameCommentAuthor.Id,
                AuthorName = TheSameCommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment cislo 2"
            };

            TheSameTeam.Members.Add(TheSameUser);
            TheSameTeam.Members.Add(TheSameCommentAuthor);
            TheSamePost.Comments.Add(TheSameComment1);
            TheSamePost.Comments.Add(TheSameComment2);

            Assert.True(Post.Equals(TheSamePost));
            Assert.Equal(Post.GetHashCode(), Post.GetHashCode());
        }

        [Fact]
        public void NotPostEquals()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };

            var Team = new TeamDetailModel()
            {
                Id = 2,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };
            var CommentAuthor = new UserListModel()
            {
                Id = 1,
                Name = "Igor",
            };

            var Post = new PostModel()
            {
                Id = 4,
                Author = User.Id,
                AuthorName = User.Name,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 4, 1),
                Team = Team.Id,
                Text = "Toto je testovaci prispevok",
                Title = "Titulok"
            };

            var Comment1 = new CommentModel()
            {
                Author = CommentAuthor.Id,
                AuthorName = CommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var Comment2 = new CommentModel()
            {
                Author = CommentAuthor.Id,
                AuthorName = CommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment cislo 2"
            };

            var TeamListModel = new TeamListModel()
            {
                Id = Team.Id,
                Name = Team.Name
            };
            var UserListModel = new UserListModel()
            {
                Id = User.Id,
                Name = User.Name
            };
            var CommentAuthorListModel = new UserListModel()
            {
                Id = CommentAuthor.Id,
                Name = CommentAuthor.Name
            };

            Team.Members.Add(User);
            Team.Members.Add(CommentAuthor);
            Post.Comments.Add(Comment1);
            Post.Comments.Add(Comment2);

            var TheSameUser = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };

            var TheSameTeam = new TeamDetailModel()
            {
                Id = 2,
                Leader = TheSameUser.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };
            var TheSameCommentAuthor = new UserListModel()
            {
                Id = 1,
                Name = "Igor",
            };

            var NotTheSamePost = new PostModel()
            {
                Id = 4,
                Author = TheSameUser.Id,
                AuthorName = TheSameUser.Name,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 4, 1),
                Team = TheSameTeam.Id,
                Text = "Toto je testovaci prispevok ..........",
                Title = "Titulok"
            };

            var TheSameComment1 = new CommentModel()
            {
                Author = TheSameCommentAuthor.Id,
                AuthorName = TheSameCommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };
            var TheSameComment2 = new CommentModel()
            {
                Author = TheSameCommentAuthor.Id,
                AuthorName = TheSameCommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment cislo 2"
            };

            TheSameTeam.Members.Add(TheSameUser);
            TheSameTeam.Members.Add(TheSameCommentAuthor);
            NotTheSamePost.Comments.Add(TheSameComment1);
            NotTheSamePost.Comments.Add(TheSameComment2);

            Assert.False(Post.Equals(NotTheSamePost));
        }

        [Fact]
        public void PostNotEqualsComment()
        {
            var User = new UserListModel()
            {
                Id = 4,
                Name = "Anton"
            };

            var Team = new TeamDetailModel()
            {
                Id = 2,
                Leader = User.Id,
                Members = new Collection<UserListModel>(),
                Name = "Team1"
            };
            var CommentAuthor = new UserListModel()
            {
                Id = 1,
                Name = "Igor",
            };

            var Post = new PostModel()
            {
                Id = 4,
                Author = User.Id,
                AuthorName = User.Name,
                Comments = new Collection<CommentModel>(),
                Date = new DateTime(2019, 4, 1),
                Team = Team.Id,
                Text = "Toto je testovaci prispevok",
                Title = "Titulok"
            };

            var Comment = new CommentModel()
            {
                Author = CommentAuthor.Id,
                AuthorName = CommentAuthor.Name,
                Date = new DateTime(2019, 1, 4),
                Id = 1,
                Text = "Testovaci koment"
            };

            var TeamListModel = new TeamListModel()
            {
                Id = Team.Id,
                Name = Team.Name
            };
            var UserListModel = new UserListModel()
            {
                Id = User.Id,
                Name = User.Name
            };
            var CommentAuthorListModel = new UserListModel()
            {
                Id = CommentAuthor.Id,
                Name = CommentAuthor.Name
            };

            Team.Members.Add(User);
            Team.Members.Add(CommentAuthor);
            Post.Comments.Add(Comment);

            Assert.False(Comment.Equals(Post));
            Assert.False(Post.Equals(Comment));

        }

    }
}
