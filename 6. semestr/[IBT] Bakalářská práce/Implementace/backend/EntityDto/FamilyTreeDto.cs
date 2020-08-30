/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeDto.cs                                      */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using System.Collections.Generic;
using PresentationModels.Relationships;
using PresentationModels.Enums;

namespace PresentationModels
{
    public class FamilyTreeDto : BaseDto
    {
        public FamilyTreeDtoTypesEnum Type { get; set; }
        public string Title { get; set; }

        //References

        // 1 to Many - with User
        public int UserId { get; set; }
        public UserDto User { get; set; }

        // 1 to Many - with Person
        public int StartPersonId { get; set; }
        public PersonDto StartPerson { get; set; }

        // Many to many - with Collision
        public ICollection<FamilyTreeCollisionDto> FamilyTreeCollisions { get; set; }

        // Many to many - with Person
        public ICollection<FamilyTreePersonDto> FamilyTreePerson { get; set; }

        // Many to many - with Relationship
        public ICollection<FamilyTreeRelationshipDto> FamilyTreeRelationship { get; set; }
    }
}
