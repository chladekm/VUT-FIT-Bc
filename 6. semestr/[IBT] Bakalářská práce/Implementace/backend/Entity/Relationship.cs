/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    Relationship.cs                                       */
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
    [Table("Relationships")]
    public class Relationship : BaseEntity
    {
        public RelationshipTypesEnum Type { get; set; }

        // References

        // 1 to Many - with Marriages
        public ICollection<Marriage> Marriages { get; set; }

        // 1 to Many - with Person (Ancestor or Husband)
        public int AncestorOrHusbandPersonId { get; set; }
        public Person AncestorOrHusbandPerson { get; set; }

        // 1 to Many - with Person (Descendant or Wife)
        public int DescendantOrWifePersonId { get; set; }
        public Person DescendantOrWifePerson { get; set; }

        // Many to Many - with Collision
        public ICollection<CollisionRelationship> CollisionRelationship { get; set; }

        // Many to many - with FamilyTree
        public ICollection<FamilyTreeRelationship> FamilyTreeRelationship { get; set; }
    }
}
