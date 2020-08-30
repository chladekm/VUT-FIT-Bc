/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CollisionRelationshipDto.cs                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

namespace PresentationModels.Relationships
{
    public class CollisionRelationshipDto
    {
        public int CollisionId { get; set; }
        public CollisionDto Collision { get; set; }

        public int RelationshipId { get; set; }
        public RelationshipDto Relationship { get; set; }
    }
}
