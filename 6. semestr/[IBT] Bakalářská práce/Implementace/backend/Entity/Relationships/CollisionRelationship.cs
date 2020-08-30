/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CollisionRelationship.cs                              */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.ComponentModel.DataAnnotations.Schema;

namespace Entity.Relationships
{
    [Table("CollisionRelationship")]
    public class CollisionRelationship
    {
        public int CollisionId { get; set; }
        public Collision Collision { get; set; }

        public int RelationshipId { get; set; }
        public Relationship Relationship { get; set; }
    }
}
