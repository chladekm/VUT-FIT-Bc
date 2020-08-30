/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    UserDto.cs                                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using System;
using System.Collections.Generic;

namespace PresentationModels
{
    public class UserDto : BaseDto
    {
        public string Name { get; set; }
        public string Surname { get; set; }
        public string Nickname { get; set; }
        public string Email { get; set; }
        public DateTime RegisterDate { get; set; }

        // References

        // 1 to Many - with FamilyTree
        public ICollection<FamilyTreeDto> FamilyTrees { get; set; }
    }
}
