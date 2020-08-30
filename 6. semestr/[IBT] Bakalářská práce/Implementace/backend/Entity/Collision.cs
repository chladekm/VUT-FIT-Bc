/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    Collision.cs                                          */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using Entity.Enums;
using Entity.Relationships;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations.Schema;

namespace Entity
{
    [Table("Collisions")]
    public class Collision : BaseEntity
    {
        public CollisionTypesEnum Type { get; set; }

        // References

        // Many to many - with Relationship
        public ICollection<CollisionRelationship> CollisionRelationship { get; set; }

        // Many to many - with FamilyTree
        public ICollection<FamilyTreeCollision> FamilyTreeCollision { get; set; }
    }
}
