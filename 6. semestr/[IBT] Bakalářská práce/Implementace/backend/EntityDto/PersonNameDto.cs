/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    PersonNameDto.cs                                      */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using PresentationModels.Base;

namespace PresentationModels
{
    public class PersonNameDto : BaseDto
    {
        public string Name { get; set; }
        public bool IsFirstName { get; set; }

        //References

        // 1 to Many - with Person
        public int PersonId { get; set; }
        public PersonDto Person { get; set; }
    }
}
