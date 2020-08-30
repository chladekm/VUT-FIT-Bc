/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    LoginCredentialsDto.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;
using System.ComponentModel.DataAnnotations;

namespace PresentationModels.Authentication
{
    public class LoginCredentialsDto : BaseDto
    {
        [Required]
        public string Nickname { get; set; }
        public string Password { get; set; }
    }
}
