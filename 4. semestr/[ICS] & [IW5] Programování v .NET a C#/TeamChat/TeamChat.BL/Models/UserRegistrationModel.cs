using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.BL.Models
{
    public class UserRegistrationModel
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Password { get; set; }
        public string RepeatedPassword { get; set; }
        public string Email { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is UserRegistrationModel user))
            {
                return false;
            }

            return Id == user.Id &&
                   Name.Equals(user.Name) &&
                   Email.Equals(user.Email) &&
                   Password.Equals(user.Password) &&
                   RepeatedPassword.Equals(user.RepeatedPassword);
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Name.GetHashCode();
            hash = (hash * 7) + Email.GetHashCode();
            hash = (hash * 7) + Password.GetHashCode();
            hash = (hash * 7) + RepeatedPassword.GetHashCode();

            return hash;
        }
    }
}
