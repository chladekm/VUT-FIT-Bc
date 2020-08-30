/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    InvalidObjectException.cs                             */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System;

namespace Services.Exceptions
{
    public class InvalidObjectException : Exception
    {
        public string TableName { get; }
        public int Id { get; }

        public InvalidObjectException(string message, string tableName, int id) : base(message)
        {
            TableName = tableName;
            Id = id;
        }
    }
}
