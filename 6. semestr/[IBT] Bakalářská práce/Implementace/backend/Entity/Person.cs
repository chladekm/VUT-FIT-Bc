/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    Person.cs                                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using Entity.Relationships;
using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations.Schema;

namespace Entity
{
    [Table("Persons")]
    public class Person : BaseEntity
    {
        public bool IsFemale { get; set; }
        public DateTime? BirthDate{ get; set; }
        public DateTime? BaptismDate { get; set; }
        public DateTime? DeathDate { get; set; }
        public string BirthPlace { get; set; }
        public bool IsPrivate { get; set; }
        public bool IsUndefined { get; set; }

        //References

        // 1 to Many - with PersonName
        public ICollection<PersonName> PersonNames { get; set; }

        // 1 to Many - with OriginalRecord
        public ICollection<OriginalRecord> OriginalRecords { get; set; }

        // 1 to Many - with FamilyTree
        public ICollection<FamilyTree> MainInFamilyTrees { get; set; }

        // 1 to Many - with Relationship (Ancestor or Husband)
        public ICollection<Relationship> AncestorOrHusbandRelationship { get; set; }

        // 1 to Many - with Relationship (Descendant or Wife)
        public ICollection<Relationship> DescendantOrWifeRelationship { get; set; }
        
        // Many to many - with FamilyTree
        public ICollection<FamilyTreePerson> FamilyTreePerson { get; set; }

    }
}
