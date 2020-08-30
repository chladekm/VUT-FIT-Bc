/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ModelBuilderRelationships.cs                          */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Enums;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using System;

namespace Service
{
    public static class ModelBuilderRelationships
    {
        public static void Relationships(this ModelBuilder modelBuilder)
        {
            /**************** 1 to many relationships ***************/

            // [User - FamilyTree]
            modelBuilder.Entity<FamilyTree>()
                .HasOne<User>(t => t.User)
                .WithMany(u => u.FamilyTrees)
                .HasForeignKey(t => t.UserId);

            // [Person - PersonName]
            modelBuilder.Entity<PersonName>()
                .HasOne<Person>(p => p.Person)
                .WithMany(u => u.PersonNames)
                .HasForeignKey(p => p.PersonId)
                .OnDelete(DeleteBehavior.Cascade);

            // [Person - OriginalRecord]
            modelBuilder.Entity<OriginalRecord>()
                .HasOne<Person>(p => p.Person)
                .WithMany(u => u.OriginalRecords)
                .HasForeignKey(p => p.PersonId)
                .OnDelete(DeleteBehavior.Cascade);

            // [Relationship - Marriage]
            modelBuilder.Entity<Marriage>()
                .HasOne<Relationship>(m => m.Relationship)
                .WithMany(r => r.Marriages)
                .HasForeignKey(m => m.RelationshipId)
                .OnDelete(DeleteBehavior.Cascade);

            // [Person - Relationship (Ancestor or Husband)]
            modelBuilder.Entity<Relationship>()
                .HasOne<Person>(m => m.AncestorOrHusbandPerson)
                .WithMany(r => r.AncestorOrHusbandRelationship)
                .HasForeignKey(m => m.AncestorOrHusbandPersonId);

            // [Person - Relationship (Descendant or Wife)]
            modelBuilder.Entity<Relationship>()
                .HasOne<Person>(m => m.DescendantOrWifePerson)
                .WithMany(r => r.DescendantOrWifeRelationship)
                .HasForeignKey(m => m.DescendantOrWifePersonId);

            // [Person - FamilyTree]
            modelBuilder.Entity<FamilyTree>()
                .HasOne<Person>(m => m.StartPerson)
                .WithMany(r => r.MainInFamilyTrees)
                .HasForeignKey(m => m.StartPersonId);


            /**************** Many to many relationships ***************/

            // [FamilyTree - Collision]
            modelBuilder.Entity<FamilyTreeCollision>()
                .HasKey(fc => new { fc.FamilyTreeId, fc.CollisionId });

            // [Collision - Relationship]
            modelBuilder.Entity<CollisionRelationship>()
                .HasKey(cr => new { cr.CollisionId, cr.RelationshipId });

            // [FamilyTree - Person]
            modelBuilder.Entity<FamilyTreePerson>()
                .HasKey(fp => new { fp.FamilyTreeId, fp.PersonId });

            // [FamilyTree - Relationship]
            modelBuilder.Entity<FamilyTreeRelationship>()
                .HasKey(fr => new { fr.FamilyTreeId, fr.RelationshipId });

            /********************** Other *********************/

            // Collision type enum treatment
            modelBuilder.Entity<Collision>().Property(e => e.Type)
                .HasConversion(v => v.ToString(),
                v => (CollisionTypesEnum)Enum.Parse(typeof(CollisionTypesEnum), v));

            // Relationship type enum treatment
            modelBuilder.Entity<Relationship>().Property(e => e.Type)
                .HasConversion(v => v.ToString(),
                v => (RelationshipTypesEnum)Enum.Parse(typeof(RelationshipTypesEnum), v));

            // FamilyTree type enum treatment
            modelBuilder.Entity<FamilyTree>().Property(e => e.Type)
                .HasConversion(v => v.ToString(),
                v => (FamilyTreeTypesEnum)Enum.Parse(typeof(FamilyTreeTypesEnum), v));

        }

    }
}
