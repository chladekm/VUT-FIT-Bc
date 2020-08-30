/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    Startup.cs                                            */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Hosting;
using Microsoft.EntityFrameworkCore;
using Service;
using Services;
using Services.Interfaces;
using Services.RelationshipServices;
using Newtonsoft.Json;
using FamilyTree.Handlers;
using AutoMapper;
using Mapper;
using Services.Interfaces.RelationshipInterfaces;
using Services.Base;
using Microsoft.AspNetCore.Authentication;

namespace FamilyTree
{
    public class Startup
    {
        public Startup(IConfiguration configuration)
        {
            Configuration = configuration;
        }

        public IConfiguration Configuration { get; }

        readonly string AllowedOrigins = "_allowedOrigins";

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
            // DB Context set from appsettings.json
            services.AddDbContext<FamilyTreeDbContext>(
                options => options
                .UseMySql(Configuration.GetConnectionString("FamilyTreeDBConnection"), x => x.MigrationsAssembly("family-tree")
            ));

            // AutoMapper - Profile is in Mapper assembly
            services.AddAutoMapper(typeof(AutoMapping));

            services.AddMvc(options => options.EnableEndpointRouting = true);

            services.AddControllers()
                .AddNewtonsoftJson(
                options =>
                {
                    options.SerializerSettings.ReferenceLoopHandling = ReferenceLoopHandling.Ignore;
                    options.SerializerSettings.NullValueHandling = NullValueHandling.Ignore;
                    options.SerializerSettings.DateTimeZoneHandling = DateTimeZoneHandling.Utc;
                    options.SerializerSettings.Error = (sender, args) =>
                    {
                        var failedMember = args.ErrorContext.Member;
                    };
                }
            );

            // Communication between url with same sources (CORS policy)
            services.AddCors(options =>
            {
                options.AddPolicy(AllowedOrigins,
                builder =>
                {
                    builder.WithOrigins("http://localhost:4200")
                           .AllowAnyHeader()
                           .AllowAnyMethod();
                });
            });

            services.AddAuthentication("BasicAuthentication")
                .AddScheme<AuthenticationSchemeOptions, CustomAuthenticationHandler>("BasicAuthentication", null);

            // My own services
            services.AddScoped<IAutoMappingService, AutoMappingService>();
            services.AddScoped<IServiceDependencies, ServiceDependencies>();

            // Entity Services
            services.AddScoped<IUserService, UserService>();
            services.AddScoped<IPersonService, PersonService>();
            services.AddScoped<IFamilyTreeService, FamilyTreeService>();
            services.AddScoped<IPersonNameService, PersonNameService>();
            services.AddScoped<IOriginalRecordService, OriginalRecordService>();
            services.AddScoped<IRelationshipService, RelationshipService>();
            services.AddScoped<ICollisionService, CollisionService>();
            services.AddScoped<IMarriageService, MarriageService>();

            // Relationship Services
            services.AddScoped<IFamilyTreePersonService, FamilyTreePersonService>();
            services.AddScoped<IFamilyTreeRelationshipService, FamilyTreeRelationshipService>();
            services.AddScoped<IFamilyTreeCollisionService, FamilyTreeCollisionService>();
            services.AddScoped<ICollisionRelationshipService, CollisionRelationshipService>();
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IWebHostEnvironment env)
        {
            if (env.IsDevelopment())
            {
                app.UseDeveloperExceptionPage();
            }

            // Own Exception handler
            app.UseMiddleware(typeof(ExceptionHandling));

            app.UseHttpsRedirection();

            app.UseRouting();

            app.UseCors(AllowedOrigins);

            app.UseAuthentication();
            app.UseAuthorization();

            app.UseEndpoints(endpoints =>
            {
                endpoints.MapControllerRoute(
                    name: "default",
                    pattern: "{controller=Values}/{action=Index}");

                endpoints.MapDefaultControllerRoute();
            });


        }
    }
}
