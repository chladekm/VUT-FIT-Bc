/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    FamilyTreePerson.cs                                   */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System.ComponentModel.DataAnnotations.Schema;

namespace Entity.Relationships
{
    [Table("FamilyTreePerson")]
    public class FamilyTreePerson
    {
        public int FamilyTreeId { get; set; }
        public FamilyTree FamilyTree { get; set; }

        public int PersonId { get; set; }
        public Person Person { get; set; }
    }
}
