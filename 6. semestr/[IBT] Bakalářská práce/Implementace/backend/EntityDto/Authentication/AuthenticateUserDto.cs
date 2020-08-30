/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    AuthenticateUserDto.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using System.ComponentModel.DataAnnotations;

namespace PresentationModels.Authentication
{
    public class AuthenticateUserDto : BaseDto
    {
        [Required]
        public string Hash { get; set; }
    }
}
