/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeDbContext.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Entity.Relationships;
using Microsoft.EntityFrameworkCore;
using System.Linq;

namespace Service
{
    public class FamilyTreeDbContext : DbContext
    {
        public FamilyTreeDbContext(DbContextOptions<FamilyTreeDbContext> options) : base(options) 
        {
        }

        public DbSet<Collision> Collisions { get; set; }
        public DbSet<FamilyTree> FamilyTrees { get; set; }
        public DbSet<Marriage> Marriages { get; set; }
        public DbSet<Person> Persons { get; set; }
        public DbSet<PersonName> PersonNames { get; set; }
        public DbSet<OriginalRecord> OriginalRecords { get; set; }
        public DbSet<Relationship> Relationships { get; set; }
        public DbSet<User> Users { get; set; }

        public DbSet<CollisionRelationship> CollisionRelationship { get; set; }
        public DbSet<FamilyTreeCollision> FamilyTreeCollision { get; set; }
        public DbSet<FamilyTreePerson> FamilyTreePerson { get; set; }
        public DbSet<FamilyTreeRelationship> FamilyTreeRelationship { get; set; }


        // ModelBuilder
        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            // Declare relationships between tables
            modelBuilder.Relationships();
        }

        // Detach tracking for all entities
        public void DetachAllEntities()
        {
            var changedEntries = this.ChangeTracker.Entries()
                .Where(e => e.State == EntityState.Added ||
                            e.State == EntityState.Modified ||
                            e.State == EntityState.Deleted ||
                            e.State == EntityState.Unchanged)
                .ToList();
            
            foreach (var entry in changedEntries)
                entry.State = EntityState.Detached;
        }


    }
}
