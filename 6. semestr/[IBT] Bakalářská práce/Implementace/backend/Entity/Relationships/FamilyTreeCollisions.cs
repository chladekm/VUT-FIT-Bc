/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeCollisions.cs                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System;
using System.ComponentModel.DataAnnotations.Schema;

namespace Entity.Relationships
{
    [Table("FamilyTreeCollision")]
    public class FamilyTreeCollision
    {
        public int FamilyTreeId { get; set; }
        public FamilyTree FamilyTree { get; set; }

        public int CollisionId { get; set; }
        public Collision Collision { get; set; }

        public bool IsSolved { get; set; }
        public DateTime? SolutionDate { get; set; }
    }
}
