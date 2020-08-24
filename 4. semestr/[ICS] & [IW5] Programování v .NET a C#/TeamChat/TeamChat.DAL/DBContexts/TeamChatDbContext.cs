using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime.InteropServices.ComTypes;
using System.Text;
using Microsoft.EntityFrameworkCore;
using TeamChat.DAL.Entities;

namespace TeamChat.DAL.DBContexts
{
    public class TeamChatDbContext : DbContext
    {

        public DbSet<CommentEntity> Comments { get; set; }
        public DbSet<MessageEntity> Messages { get; set; }
        public DbSet<PostEntity> Posts { get; set; }
        public DbSet<TeamEntity> Teams { get; set; }
        public DbSet<UserEntity> Users { get; set; }

        public TeamChatDbContext()
        {

        }

        public TeamChatDbContext(DbContextOptions dbContextOptions) : base(dbContextOptions)
        {

        }

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            if (!optionsBuilder.IsConfigured)
            {
                optionsBuilder.UseSqlServer("Data Source=(localdb)\\MSSQLLocalDB;Initial Catalog= TeamChatDB;Integrated Security=True;Connect Timeout=30;Encrypt=False;TrustServerCertificate=False;ApplicationIntent=ReadWrite;MultiSubnetFailover=False");
                
            }
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            modelBuilder.Entity<PostEntity>()
                .HasMany(e => e.Comments)
                .WithOne();

            modelBuilder.Entity<TeamEntity>()
                .HasMany(e => e.Members)
                .WithOne();

            modelBuilder.Entity<UserEntity>()
                .HasMany(e => e.Comments)
                .WithOne();

            modelBuilder.Entity<UserEntity>()
                .HasMany(e => e.Posts)
                .WithOne();
        }
    }
}
