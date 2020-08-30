/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    CustomAuthenticationHandler.cs                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Mapper;
using Microsoft.AspNetCore.Authentication;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Newtonsoft.Json;
using PresentationModels;
using PresentationModels.Authentication;
using Services.Interfaces;
using System;
using System.Security.Claims;
using System.Text.Encodings.Web;
using System.Threading.Tasks;

namespace FamilyTree.Handlers
{
    public class CustomAuthenticationHandler : AuthenticationHandler<AuthenticationSchemeOptions>
    {
        private readonly IUserService _userService;
        private readonly IFamilyTreeService _familytreeService;
        protected readonly IAutoMappingService _mapper;

        public CustomAuthenticationHandler(
            IOptionsMonitor<AuthenticationSchemeOptions> options,
            ILoggerFactory logger,
            UrlEncoder encoder,
            ISystemClock clock,

            IUserService userService,
            IFamilyTreeService familyTreeService,
            IAutoMappingService mapper)
        : base(options, logger, encoder, clock)
        {
            _userService = userService;
            _familytreeService = familyTreeService;
            _mapper = mapper;
        }


        protected override async Task<AuthenticateResult> HandleAuthenticateAsync()
        {
            if (!Request.Headers.Keys.Contains("auth"))
                return AuthenticateResult.Fail("Authorization header is missing");

                UserCompleteDto user = null;

            try
            {
                var authHeader = Request.Headers["auth"];
                var credentials = JsonConvert.DeserializeObject<AuthenticateUserDto>(authHeader);
                
                User data = await _userService.AuthenticateUserAsync(credentials.Id, credentials.Hash);
                user = _mapper.MapToDto<User, UserCompleteDto>(data);

                if (Request.Path.ToString().Contains("/familytree/") && Request.Method.Contains("GET") && !Request.Path.ToString().Contains("/concat-valid/"))
                {
                    var partials = Request.Path.ToString().Split('/');
                    var familyTreeId = Int32.Parse(partials[2]);

                    Entity.FamilyTree fData = await _familytreeService.GetFamilyTreeByIdAsync(familyTreeId);

                    if (fData.UserId != credentials.Id && fData.Type != Entity.Enums.FamilyTreeTypesEnum._public)
                        throw new Exception();
                }
            }
            catch
            {
                return AuthenticateResult.Fail("Invalid Authorization Header");
            }

            if (user == null)
                return AuthenticateResult.Fail("Authentication failed");

            var claims = new[] {
                new Claim(ClaimTypes.NameIdentifier, user.Id.ToString()),
                new Claim(ClaimTypes.Name, user.Nickname),
            };

            var identity = new ClaimsIdentity(claims, Scheme.Name);
            var principal = new ClaimsPrincipal(identity);

            // Set authentication ticket -> authentization succesfull
            var ticket = new AuthenticationTicket(principal, Scheme.Name);

            return AuthenticateResult.Success(ticket);
        }



    }
}
