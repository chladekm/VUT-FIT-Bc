/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    OriginalRecord.cs                                     */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity.Base;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Entity
{
    [Table("OriginalRecords")]
    public class OriginalRecord : BaseEntity
    {
        [Required]
        public int RecordId { get; set; }

        //References

        // 1 to Many - with Person
        public int PersonId { get; set; }
        public Person Person { get; set; }
    }
}
