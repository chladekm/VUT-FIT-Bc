/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreeCollisionsDto.cs                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System;

namespace PresentationModels.Relationships
{
    public class FamilyTreeCollisionDto
    {
        public int FamilyTreeId { get; set; }
        public FamilyTreeDto FamilyTree { get; set; }

        public int CollisionId { get; set; }
        public CollisionDto Collision { get; set; }

        public bool IsSolved { get; set; }
        public DateTime? SolutionDate { get; set; }
    }
}
