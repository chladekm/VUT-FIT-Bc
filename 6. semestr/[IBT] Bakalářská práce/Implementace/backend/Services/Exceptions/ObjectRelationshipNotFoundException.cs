/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ObjectRelationshipNotFountException.cs                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System;

namespace Services.Exceptions
{
    public class ObjectRelationshipNotFoundException : Exception
    {
        public string TableName { get; }
        public int IdRef1 { get; }
        public int IdRef2 { get; }

        public ObjectRelationshipNotFoundException(string tableName, int idRef1, int idRef2)
        {
            TableName = tableName;
            IdRef1 = idRef1;
            IdRef2 = idRef2;
        }
    }
}
