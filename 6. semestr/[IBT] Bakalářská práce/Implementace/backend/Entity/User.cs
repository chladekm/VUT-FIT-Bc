/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    User.cs                                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Entity
{
    [Table("Users")]
    public class User : BaseEntity
    {
        public string Name { get; set; }
        public string Surname { get; set; }
        [Required]
        public string Nickname { get; set; }
        public string Email { get; set; }
        [Required]
        public string Password { get; set; }
        [Required]
        public string Salt { get; set; }
        public DateTime RegisterDate { get; set; }
        

        // References

        // 1 to Many - with FamilyTree
        public ICollection<FamilyTree> FamilyTrees { get; set; }
    }
}
