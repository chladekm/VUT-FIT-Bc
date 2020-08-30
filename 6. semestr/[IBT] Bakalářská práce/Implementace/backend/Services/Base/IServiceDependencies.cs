/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IServiceDependencies.cs                               */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Service;
using System;

namespace Services.Base
{
    public interface IServiceDependencies
    {
        FamilyTreeDbContext _context { get;  }
        IServiceProvider ServiceProvider { get; }
    }
}
