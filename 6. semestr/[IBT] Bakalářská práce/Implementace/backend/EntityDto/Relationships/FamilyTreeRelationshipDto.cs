/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeRelationshipDto.cs                          */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

namespace PresentationModels.Relationships
{
    public class FamilyTreeRelationshipDto
    {
        public int FamilyTreeId { get; set; }
        public FamilyTreeDto FamilyTree { get; set; }

        public int RelationshipId { get; set; }
        public RelationshipDto Relationship { get; set; }
    }
}
