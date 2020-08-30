/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    PersonDto.cs                                          */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using PresentationModels.Relationships;
using System;
using System.Collections.Generic;

namespace PresentationModels
{
    public class PersonDto : BaseDto
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
        public ICollection<PersonNameDto> PersonNames { get; set; }

        // 1 to Many - with OriginalRecord
        public ICollection<OriginalRecordDto> OriginalRecords { get; set; }

        // 1 to Many - with FamilyTree
        public ICollection<FamilyTreeDto> MainInFamilyTrees { get; set; }

        // 1 to Many - with Relationship (Ancestor or Husband)
        public ICollection<RelationshipDto> AncestorOrHusbandRelationship { get; set; }

        // 1 to Many - with Relationship (Descendant or Wife)
        public ICollection<RelationshipDto> DescendantOrWifeRelationship { get; set; }
        
        // Many to many - with FamilyTree
        public ICollection<FamilyTreePersonDto> FamilyTreePerson { get; set; }

    }
}
