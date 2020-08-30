/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreePersonDto.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

namespace PresentationModels.Relationships
{
    public class FamilyTreePersonDto
    {
        public int FamilyTreeId { get; set; }
        public FamilyTreeDto FamilyTree { get; set; }

        public int PersonId { get; set; }
        public PersonDto Person { get; set; }
    }
}
