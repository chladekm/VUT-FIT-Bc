/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    Marriage.cs                                           */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using System;
using System.ComponentModel.DataAnnotations.Schema;

namespace Entity
{
    [Table("Marriages")]
    public class Marriage : BaseEntity
    {
        public DateTime MarriageDate { get; set; }
        public string MarriageAddress { get; set; }

        // References

        // 1 to Many - with Relationship
        public int RelationshipId { get; set; }
        public Relationship Relationship { get; set; }
    }
}
