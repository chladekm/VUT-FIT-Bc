/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    RelationshipDto.cs                                    */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using PresentationModels.Enums;
using PresentationModels.Relationships;
using System.Collections.Generic;

namespace PresentationModels
{
    public class RelationshipDto : BaseDto
    {
        public RelationshipDtoTypesEnum Type { get; set; }

        // Relationships

        // 1 to Many - with Relationship
        public ICollection<MarriageDto> Marriages { get; set; }

        // 1 to Many - with Person (Ancestor or Husband)
        public int AncestorOrHusbandPersonId { get; set; }
        public PersonDto AncestorOrHusbandPerson { get; set; }

        // 1 to Many - with Person (Descendant or Wife)
        public int DescendantOrWifePersonId { get; set; }
        public PersonDto DescendantOrWifePerson { get; set; }

        // Many to Many - with Collision
        public ICollection<CollisionRelationshipDto> CollisionRelationship { get; set; }

        // Many to many - with FamilyTree
        public ICollection<FamilyTreeRelationshipDto> FamilyTreeRelationship { get; set; }



    }
}
