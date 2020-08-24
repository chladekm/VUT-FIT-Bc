using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using Microsoft.EntityFrameworkCore;
using System.Linq;
using Newtonsoft.Json;
using TeamChat.DAL.Entities;
using TeamChat.DAL.DBContexts;
using Xunit;

namespace TeamChat.DAL.Tests
{
    public class TeamChatDbContextTests
    {
        private IDbContextFactory dbContextFactory;

        public TeamChatDbContextTests()
        {
            dbContextFactory = new InMemoryDbContextFactory();
        }

        [Fact]
        public void CreateCommentTest()
        {
            //Arrange
            var comment = new CommentEntity()
            {
                Id = 1,
                Author = 42,
                Date = new DateTime(2019, 1, 1),
                Text = "Testovaci komentar"
            };

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Comments.Add(comment);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedComment = dbContext.Comments.First(t => t.Id == comment.Id);
                Assert.NotNull(retrievedComment);
            }
        }

        [Fact]
        public void UpdateCommentTest()
        {
            //Arrange
            var comment = new CommentEntity()
            {
                Author = 42,
                Date = new DateTime(2019, 1, 1),
                Id = 2,
                Text = "TestovacY komentar"
            };


            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Comments.Add(comment);
                dbContext.SaveChanges();
            }

            //Act
            comment.Text = "Testovaci komentar";
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Comments.Update(comment);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedComment = dbContext.Comments.First(t => t.Id == comment.Id);
                Assert.NotNull(retrievedComment);
                Assert.Equal("Testovaci komentar", retrievedComment.Text);
            }
        }

        [Fact]
        public void RemoveCommentTest()
        {
            //Arrange
            var comment = new CommentEntity()
            {
                Author = 42,
                Date = new DateTime(2019, 1, 20),
                Id = 3,
                Text = "Testovaci komentar"
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Comments.Add(comment);
                dbContext.SaveChanges();
            }

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Comments.Remove(comment);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedComment = dbContext.Comments.FirstOrDefault(t => t.Id == comment.Id);
                Assert.Null(retrievedComment);
            }
        }

        [Fact]
        public void CreatePostTest()
        {
            //Arrange
            var post = new PostEntity()
            {
                Author = 42,
                Date = new DateTime(2019, 1, 1),
                Id = 10,
                Text = "Lorem Ipsum...",
                Title = "Toto je nadpis!"
            };

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Posts.Add(post);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedPost = dbContext.Posts.First(t => t.Id == post.Id);
                Assert.NotNull(retrievedPost);
            }
        }

        [Fact]
        public void UpdatePostTest()
        {
            //Arrange
            var post = new PostEntity()
            {
                Author = 42,
                Date = new DateTime(2019, 1, 1),
                Id = 12,
                Text = "Lorem Ipsum...",
                Title = "Toto je nadpYYYYs!",
                Comments = null,
                Team = 42
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Posts.Add(post);
                dbContext.SaveChanges();
            }

            //Act
            post.Title = "Toto je nadpis!";
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Posts.Update(post);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedPost = dbContext.Posts.First(t => t.Id == post.Id);
                Assert.NotNull(retrievedPost);
                Assert.Equal("Toto je nadpis!", retrievedPost.Title);
            }
        }

        [Fact]
        public void RemovePostTest()
        {
            //Arrange
            var post = new PostEntity()
            {
                Author = 42,
                Date = new DateTime(2019, 1, 16),
                Id = 15,
                Text = "Lorem Ipsum...",
                Title = "Toto je nadpis!",
                Comments = null,
                Team = 42
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Posts.Add(post);
                dbContext.SaveChanges();
            }

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Posts.Remove(post);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedPost = dbContext.Posts.FirstOrDefault(t => t.Id == post.Id);
                Assert.Null(retrievedPost);
            }
        }

        [Fact]
        public void CreateTeamTest()
        {
            //Arrange
            var team = new TeamEntity()
            {
                Id = 20,
                Name = "Python development group",
                Members = null,
                Leader = 42
            };
 
            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Teams.Add(team);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedTeam = dbContext.Teams.First(t => t.Id == team.Id);
                Assert.NotNull(retrievedTeam);
            }
        }

        [Fact]
        public void UpdateTeamTest()
        {
            //Arrange
            var team = new TeamEntity()
            {
                Id = 22,
                Name = "Pascal development group",
                Members = null,
                Leader = 42
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Teams.Add(team);
                dbContext.SaveChanges();
            }

            //Act
            team.Name = "Pascal is dead!";
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Teams.Update(team);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedTeam = dbContext.Teams.First(t => t.Id == team.Id);
                Assert.NotNull(retrievedTeam);
                Assert.Equal("Pascal is dead!", retrievedTeam.Name);
            }
        }

        [Fact]
        public void RemoveTeamTest()
        {
            //Arrange
            var team = new TeamEntity()
            {
                Id = 24,
                Name = "PHP development group",
                Members = null,
                Leader = 42,
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Teams.Add(team);
                dbContext.SaveChanges();
            }

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Teams.Remove(team);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedTeam = dbContext.Teams.FirstOrDefault(t => t.Id == team.Id);
                Assert.Null(retrievedTeam);
            }
        }

        [Fact]
        public void CreateUserTest()
        {
            //Arrange
            var user = new UserEntity()
            {
                Id = 30,
                Name = "Jan Fialovy",
                Password = "6729aabbcc",
                Email = "fialovy1020@seznam.cz",
                Posts = null,
                Comments = null
            };

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Users.Add(user);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedUser = dbContext.Users.First(t => t.Id == user.Id);
                Assert.NotNull(retrievedUser);
            }
        }

        [Fact]
        public void UpdateUserTest()
        {
            //Arrange
            var user = new UserEntity()
            {
                Id = 31,
                Name = "Evzen Hnedy",
                Password = "ss66ss",
                Email = "hnedy60@seznam.cz",
                Posts = null,
                Comments = null
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Users.Add(user);
                dbContext.SaveChanges();
            }

            //Act
            user.Password = "gg44gg";
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Users.Update(user);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedUser = dbContext.Users.First(t => t.Id == user.Id);
                Assert.NotNull(retrievedUser);
                Assert.Equal("gg44gg", retrievedUser.Password);
            }
        }

        [Fact]
        public void RemoveUserTest()
        {
            //Arrange
            var user = new UserEntity()
            {
                Id = 32,
                Name = "Bronislav Sedy",
                Password = "jason280",
                Email = "sedy42@seznam.cz",
                Posts = null,
                Comments = null
            };

            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Users.Add(user);
                dbContext.SaveChanges();
            }

            //Act
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                dbContext.Users.Remove(user);
                dbContext.SaveChanges();
            }

            //Assert
            using (var dbContext = dbContextFactory.CreateDbContext())
            {
                var retrievedUser = dbContext.Users.FirstOrDefault(t => t.Id == user.Id);
                Assert.Null(retrievedUser);
            }
        }
    }
}
