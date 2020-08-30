/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTree.cs                                         */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using Entity.Base;
using Entity.Relationships;
using System.ComponentModel.DataAnnotations.Schema;
using Entity.Enums;

namespace Entity
{
    [Table("FamilyTrees")]
    public class FamilyTree : BaseEntity
    {   
        [Required]
        public FamilyTreeTypesEnum Type { get; set; }
        public string Title { get; set; }

        //References

        // 1 to Many - with User
        public int UserId { get; set; }
        public User User { get; set; }

        // 1 to Many - with Person
        public int StartPersonId { get; set; }
        public Person StartPerson { get; set; }

        // Many to many - with Collision
        public ICollection<FamilyTreeCollision> FamilyTreeCollisions { get; set; }

        // Many to many - with Person
        public ICollection<FamilyTreePerson> FamilyTreePerson { get; set; }
        
        // Many to many - with Relationship
        public ICollection<FamilyTreeRelationship> FamilyTreeRelationship { get; set; }
    }
}
