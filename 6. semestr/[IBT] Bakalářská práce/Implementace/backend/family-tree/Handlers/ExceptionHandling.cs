/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    ExceptionHandling.cs                                  */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Microsoft.AspNetCore.Http;
using Newtonsoft.Json;
using Services.Exceptions;
using System;
using System.Net;
using System.Threading.Tasks;

namespace FamilyTree.Handlers
{
    // Middleware
    public class ExceptionHandling
    {
        private readonly RequestDelegate _next;

        public ExceptionHandling(RequestDelegate next)
        {
            _next = next;
        }

        public async Task Invoke(HttpContext context)
        {
            try
            {
                await _next(context);
            }
            catch (Exception exception)
            {
                await HandleExceptionAsync(context, exception);
            }
        }

        public async Task HandleExceptionAsync(HttpContext context, Exception exception)
        {
            HttpStatusCode code = 0;
            object response = null;

            if (exception is InvalidObjectException)
            {
                code = HttpStatusCode.BadRequest;
                response = new
                {
                    message = exception.Message,
                    tableName = ((InvalidObjectException)exception).TableName,
                    id = ((InvalidObjectException)exception).Id
                };
            }
            else if (exception is InvalidObjectRelationshipException)
            {
                code = HttpStatusCode.BadRequest;
                response = new
                {
                    message = exception.Message,
                    tableName = ((InvalidObjectRelationshipException)exception).TableName,
                    id_1 = ((InvalidObjectRelationshipException)exception).IdRef1,
                    id_2 = ((InvalidObjectRelationshipException)exception).IdRef2

                };
            }
            else if (exception is ObjectNotFoundException)
            {
                code = HttpStatusCode.NotFound;
                response = new
                {
                    message = "Cannot find record of passed id in selected table",
                    tableName = ((ObjectNotFoundException)exception).TableName,
                    id = ((ObjectNotFoundException)exception).Id
                };
            }
            else if(exception is ObjectRelationshipNotFoundException)
            {
                code = HttpStatusCode.NotFound;
                response = new
                {
                    message = "Cannot find record of passed relationship in selected table",
                    tableName = ((ObjectRelationshipNotFoundException)exception).TableName,
                    id_1 = ((ObjectRelationshipNotFoundException)exception).IdRef1,
                    id_2 = ((ObjectRelationshipNotFoundException)exception).IdRef2,
                };
            }
            else
            {
                code = HttpStatusCode.InternalServerError;
                response = exception;
            }


            // Get JSON from response
            var json = JsonConvert.SerializeObject(response);

            context.Response.ContentType = "application/json";
            context.Response.StatusCode = (int)code;

            await context.Response.WriteAsync(json);
        }
    }
}
