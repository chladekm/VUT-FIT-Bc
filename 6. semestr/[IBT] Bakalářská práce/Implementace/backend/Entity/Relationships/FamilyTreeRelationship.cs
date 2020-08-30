/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeRelationship.cs                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.ComponentModel.DataAnnotations.Schema;

namespace Entity.Relationships
{
    [Table("FamilyTreeRelationship")]
    public class FamilyTreeRelationship
    {
        public int FamilyTreeId { get; set; }
        public FamilyTree FamilyTree { get; set; }

        public int RelationshipId { get; set; }
        public Relationship Relationship { get; set; }
    }
}
