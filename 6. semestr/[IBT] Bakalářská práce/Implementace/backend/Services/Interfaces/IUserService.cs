/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    IUserService.cs                                       */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using Entity;
using System.Threading.Tasks;

namespace Services.Interfaces
{
    public interface IUserService
    {
        // Authentication methods
        public Task<User> AuthenticateUserAsync(int Id, string Password);
        public Task<User> LoginUserAsync(User userToLogin);
        public Task<bool> AreCredentialsUnique(string username);

        // CRUD methods
        public Task<User> GetUserAsync(int Id);
        public Task<User> CreateUserAsync(User userToCreate);
        public Task<User> UpdateUserAsync(User userToUpdate);
        public Task<User> DeleteUserAsync(int Id);
    }
}