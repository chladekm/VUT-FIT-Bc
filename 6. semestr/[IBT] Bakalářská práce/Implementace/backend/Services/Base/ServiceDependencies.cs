/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ServiceDependencies.cs                                */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Service;
using System;

namespace Services.Base
{
    public class ServiceDependencies : IServiceDependencies
    {
        public FamilyTreeDbContext _context { get; }
        public IServiceProvider ServiceProvider { get; }

        public ServiceDependencies(FamilyTreeDbContext context, IServiceProvider serviceProvider)
        {
            _context = context;
            ServiceProvider = serviceProvider;
        }
    }
}
