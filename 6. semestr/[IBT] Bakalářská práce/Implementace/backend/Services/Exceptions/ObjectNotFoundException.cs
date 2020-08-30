/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ObjectNotFoundException.cs                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System;

namespace Services.Exceptions
{
    public class ObjectNotFoundException : Exception
    {
        public int Id { get; }
        public string TableName { get; }

        public ObjectNotFoundException(string tableName, int id)
        {
            Id = id;
            TableName = tableName;
        }
    }
}
