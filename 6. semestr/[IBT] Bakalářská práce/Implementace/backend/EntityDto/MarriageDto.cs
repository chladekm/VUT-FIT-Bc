/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    MarriageDto.cs                                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using System;

namespace PresentationModels
{
    public class MarriageDto : BaseDto
    {
        public DateTime MarriageDate { get; set; }
        public string MarriageAddress { get; set; }

        //References

        // 1 to Many - with Relationship
        public int RelationshipId { get; set; }
        public RelationshipDto Relationship { get; set; }
    }
}
