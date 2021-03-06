﻿// <auto-generated />
using System;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Infrastructure;
using Microsoft.EntityFrameworkCore.Migrations;
using Microsoft.EntityFrameworkCore.Storage.ValueConversion;
using Service;

namespace FamilyTree.Migrations
{
    [DbContext(typeof(FamilyTreeDbContext))]
    [Migration("20200521103606_InitialCreate")]
    partial class InitialCreate
    {
        protected override void BuildTargetModel(ModelBuilder modelBuilder)
        {
#pragma warning disable 612, 618
            modelBuilder
                .HasAnnotation("ProductVersion", "3.1.0")
                .HasAnnotation("Relational:MaxIdentifierLength", 64);

            modelBuilder.Entity("Entity.Collision", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<string>("Type")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.HasKey("Id");

                    b.ToTable("Collisions");
                });

            modelBuilder.Entity("Entity.FamilyTree", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<int>("StartPersonId")
                        .HasColumnType("int");

                    b.Property<string>("Title")
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<string>("Type")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<int>("UserId")
                        .HasColumnType("int");

                    b.HasKey("Id");

                    b.HasIndex("StartPersonId");

                    b.HasIndex("UserId");

                    b.ToTable("FamilyTrees");
                });

            modelBuilder.Entity("Entity.Marriage", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<string>("MarriageAddress")
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<DateTime>("MarriageDate")
                        .HasColumnType("datetime(6)");

                    b.Property<int>("RelationshipId")
                        .HasColumnType("int");

                    b.HasKey("Id");

                    b.HasIndex("RelationshipId");

                    b.ToTable("Marriages");
                });

            modelBuilder.Entity("Entity.OriginalRecord", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<int>("PersonId")
                        .HasColumnType("int");

                    b.Property<int>("RecordId")
                        .HasColumnType("int");

                    b.HasKey("Id");

                    b.HasIndex("PersonId");

                    b.ToTable("OriginalRecords");
                });

            modelBuilder.Entity("Entity.Person", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<DateTime?>("BaptismDate")
                        .HasColumnType("datetime(6)");

                    b.Property<DateTime?>("BirthDate")
                        .HasColumnType("datetime(6)");

                    b.Property<string>("BirthPlace")
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<DateTime?>("DeathDate")
                        .HasColumnType("datetime(6)");

                    b.Property<bool>("IsFemale")
                        .HasColumnType("tinyint(1)");

                    b.Property<bool>("IsPrivate")
                        .HasColumnType("tinyint(1)");

                    b.Property<bool>("IsUndefined")
                        .HasColumnType("tinyint(1)");

                    b.HasKey("Id");

                    b.ToTable("Persons");
                });

            modelBuilder.Entity("Entity.PersonName", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<string>("Name")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<int>("PersonId")
                        .HasColumnType("int");

                    b.Property<bool>("isFirstName")
                        .HasColumnType("tinyint(1)");

                    b.HasKey("Id");

                    b.HasIndex("PersonId");

                    b.ToTable("PersonNames");
                });

            modelBuilder.Entity("Entity.Relationship", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<int>("AncestorOrHusbandPersonId")
                        .HasColumnType("int");

                    b.Property<int>("DescendantOrWifePersonId")
                        .HasColumnType("int");

                    b.Property<string>("Type")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.HasKey("Id");

                    b.HasIndex("AncestorOrHusbandPersonId");

                    b.HasIndex("DescendantOrWifePersonId");

                    b.ToTable("Relationships");
                });

            modelBuilder.Entity("Entity.Relationships.CollisionRelationship", b =>
                {
                    b.Property<int>("CollisionId")
                        .HasColumnType("int");

                    b.Property<int>("RelationshipId")
                        .HasColumnType("int");

                    b.HasKey("CollisionId", "RelationshipId");

                    b.HasIndex("RelationshipId");

                    b.ToTable("CollisionRelationship");
                });

            modelBuilder.Entity("Entity.Relationships.FamilyTreeCollision", b =>
                {
                    b.Property<int>("FamilyTreeId")
                        .HasColumnType("int");

                    b.Property<int>("CollisionId")
                        .HasColumnType("int");

                    b.Property<bool>("IsSolved")
                        .HasColumnType("tinyint(1)");

                    b.Property<DateTime?>("SolutionDate")
                        .HasColumnType("datetime(6)");

                    b.HasKey("FamilyTreeId", "CollisionId");

                    b.HasIndex("CollisionId");

                    b.ToTable("FamilyTreeCollision");
                });

            modelBuilder.Entity("Entity.Relationships.FamilyTreePerson", b =>
                {
                    b.Property<int>("FamilyTreeId")
                        .HasColumnType("int");

                    b.Property<int>("PersonId")
                        .HasColumnType("int");

                    b.HasKey("FamilyTreeId", "PersonId");

                    b.HasIndex("PersonId");

                    b.ToTable("FamilyTreePerson");
                });

            modelBuilder.Entity("Entity.Relationships.FamilyTreeRelationship", b =>
                {
                    b.Property<int>("FamilyTreeId")
                        .HasColumnType("int");

                    b.Property<int>("RelationshipId")
                        .HasColumnType("int");

                    b.HasKey("FamilyTreeId", "RelationshipId");

                    b.HasIndex("RelationshipId");

                    b.ToTable("FamilyTreeRelationship");
                });

            modelBuilder.Entity("Entity.User", b =>
                {
                    b.Property<int>("Id")
                        .ValueGeneratedOnAdd()
                        .HasColumnType("int");

                    b.Property<string>("Email")
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<string>("Name")
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<string>("Nickname")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<string>("Password")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<DateTime>("RegisterDate")
                        .HasColumnType("datetime(6)");

                    b.Property<string>("Salt")
                        .IsRequired()
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.Property<string>("Surname")
                        .HasColumnType("longtext CHARACTER SET utf8mb4");

                    b.HasKey("Id");

                    b.ToTable("Users");
                });

            modelBuilder.Entity("Entity.FamilyTree", b =>
                {
                    b.HasOne("Entity.Person", "StartPerson")
                        .WithMany("MainInFamilyTrees")
                        .HasForeignKey("StartPersonId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.HasOne("Entity.User", "User")
                        .WithMany("FamilyTrees")
                        .HasForeignKey("UserId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.Marriage", b =>
                {
                    b.HasOne("Entity.Relationship", "Relationship")
                        .WithMany("Marriages")
                        .HasForeignKey("RelationshipId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.OriginalRecord", b =>
                {
                    b.HasOne("Entity.Person", "Person")
                        .WithMany("OriginalRecords")
                        .HasForeignKey("PersonId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.PersonName", b =>
                {
                    b.HasOne("Entity.Person", "Person")
                        .WithMany("PersonNames")
                        .HasForeignKey("PersonId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.Relationship", b =>
                {
                    b.HasOne("Entity.Person", "AncestorOrHusbandPerson")
                        .WithMany("AncestorOrHusbandRelationship")
                        .HasForeignKey("AncestorOrHusbandPersonId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.HasOne("Entity.Person", "DescendantOrWifePerson")
                        .WithMany("DescendantOrWifeRelationship")
                        .HasForeignKey("DescendantOrWifePersonId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.Relationships.CollisionRelationship", b =>
                {
                    b.HasOne("Entity.Collision", "Collision")
                        .WithMany("CollisionRelationship")
                        .HasForeignKey("CollisionId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.HasOne("Entity.Relationship", "Relationship")
                        .WithMany("CollisionRelationship")
                        .HasForeignKey("RelationshipId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.Relationships.FamilyTreeCollision", b =>
                {
                    b.HasOne("Entity.Collision", "Collision")
                        .WithMany("FamilyTreeCollision")
                        .HasForeignKey("CollisionId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.HasOne("Entity.FamilyTree", "FamilyTree")
                        .WithMany("FamilyTreeCollisions")
                        .HasForeignKey("FamilyTreeId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.Relationships.FamilyTreePerson", b =>
                {
                    b.HasOne("Entity.FamilyTree", "FamilyTree")
                        .WithMany("FamilyTreePerson")
                        .HasForeignKey("FamilyTreeId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.HasOne("Entity.Person", "Person")
                        .WithMany("FamilyTreePerson")
                        .HasForeignKey("PersonId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });

            modelBuilder.Entity("Entity.Relationships.FamilyTreeRelationship", b =>
                {
                    b.HasOne("Entity.FamilyTree", "FamilyTree")
                        .WithMany("FamilyTreeRelationship")
                        .HasForeignKey("FamilyTreeId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();

                    b.HasOne("Entity.Relationship", "Relationship")
                        .WithMany("FamilyTreeRelationship")
                        .HasForeignKey("RelationshipId")
                        .OnDelete(DeleteBehavior.Cascade)
                        .IsRequired();
                });
#pragma warning restore 612, 618
        }
    }
}
