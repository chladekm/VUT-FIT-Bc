/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    UserService.cs                                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using Microsoft.EntityFrameworkCore;
using Services.Base;
using Services.Cryptography;
using Services.Exceptions;
using Services.Interfaces;
using System;
using System.Threading.Tasks;

namespace Services
{
    public class UserService : BaseService<User>, IUserService
    {
        public UserService(IServiceDependencies _serviceDependencies) : base(_serviceDependencies)
        {
        }

        #region Authentication validation

        /// <summary>
        /// Method provides authentication, if null returned -> authentication failed
        /// </summary>
        public async Task<User> AuthenticateUserAsync(int Id, string Hash)
        {
            try
            {
                var result = await GetById(Id);

                if (PasswordService.AreHashesIdentic(Convert.FromBase64String(Hash), Convert.FromBase64String(result.Password)))
                    return result;
                else
                    return null;
            }
            // No record found, Authentication failed
            catch
            {
                return null;
            }
        }

        /// <summary>
        /// Method checks if username and password is identic with record from database
        /// </summary>
        public async Task<User> LoginUserAsync(User userToLogin)
        {

            var result = await _serviceDependencies._context
                                                    .Users
                                                    .AsNoTracking()
                                                    .FirstOrDefaultAsync(user => (user.Nickname == userToLogin.Nickname));
            // No record found
            if (result != null)
            {
                if (PasswordService.VerifyPassword(userToLogin.Password, Convert.FromBase64String(result.Salt), Convert.FromBase64String(result.Password)))
                    return result;
                else
                    return null;
            }
            else
                return null;
        }

        /// <summary>
        /// Inspects if passed username is unique in database
        /// </summary>
        public async Task<bool> AreCredentialsUnique(string username)
        {
            var result = await _serviceDependencies._context.Users
                                                            .AsNoTracking()
                                                            .FirstOrDefaultAsync(user => user.Nickname == username);
            return result == null ? true : false;
        }

        #endregion

        #region CRUD operations

        public async Task<User> GetUserAsync(int id)
        {
            var result = await _serviceDependencies._context.Users
                                                            .AsNoTracking()
                                                            .Include(user => user.FamilyTrees)
                                                            .ThenInclude(familytree => familytree.FamilyTreePerson)
                                                            .Include(user => user.FamilyTrees)
                                                            .ThenInclude(familytree => familytree.FamilyTreeRelationship)
                                                            .Include(user => user.FamilyTrees)
                                                            .ThenInclude(familytree => familytree.FamilyTreeCollisions)
                                                            .FirstOrDefaultAsync(user => user.Id == id);
            if (result == null)
                throw new ObjectNotFoundException(nameof(Entity.User), id);

            return result;
        }

        public async Task<User> CreateUserAsync(User userToCreate)
        {
            var result = await _serviceDependencies._context.Users.FirstOrDefaultAsync(user => user.Nickname == userToCreate.Nickname);

            if (result != null)
                throw new InvalidObjectException("Cannot create user, nickname already exists", nameof(Entity.User), 0);

            (userToCreate.Salt, userToCreate.Password) = PasswordService.CreateNewStringHashAndSalt(userToCreate.Password);

            await _serviceDependencies._context.Users.AddAsync(userToCreate);
            await _serviceDependencies._context.SaveChangesAsync();

            return userToCreate;
        }

        public async Task<User> UpdateUserAsync(User userToUpdate)
        {
            var result = await _serviceDependencies._context.Users
                                                            .AsNoTracking()
                                                            .FirstOrDefaultAsync(i => i.Id == userToUpdate.Id);
            // Record found
            if (result != null)
            {
                result.Name = userToUpdate.Name;
                result.Surname = userToUpdate.Surname;
                result.Email = userToUpdate.Email;
                _serviceDependencies._context.Users.Update(result);

                await _serviceDependencies._context.SaveChangesAsync();
            }
            else
            {
                throw new ObjectNotFoundException(typeof(User).FullName, userToUpdate.Id);
            }

            return result;
        }

        public async Task<User> DeleteUserAsync(int id)
        {
            return await Delete(id);
        }

        #endregion
    }
}
