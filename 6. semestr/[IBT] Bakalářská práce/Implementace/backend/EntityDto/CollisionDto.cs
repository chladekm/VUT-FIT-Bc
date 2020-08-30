/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CollisionDto.cs                                       */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using PresentationModels.Enums;
using PresentationModels.Relationships;
using System.Collections.Generic;

namespace PresentationModels
{
    public class CollisionDto : BaseDto
    {
        public CollisionDtoTypesEnum Type { get; set; }

        // References

        // Many to many - with Relationship
        public ICollection<CollisionRelationshipDto> CollisionRelationship { get; set; }

        // Many to many - with FamilyTree
        public ICollection<FamilyTreeCollisionDto> FamilyTreeCollision { get; set; }
    }
}
