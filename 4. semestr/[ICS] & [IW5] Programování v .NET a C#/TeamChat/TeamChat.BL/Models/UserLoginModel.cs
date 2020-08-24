using System;
using System.Collections.Generic;
using System.Text;

namespace TeamChat.BL.Models
{
    public class UserLoginModel
    {
        public int Id { get; set; }
        public string Email { get; set; }
        public string Password { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is UserLoginModel user))
            {
                return false;
            }

            return Id==user.Id &&
                   Email.Equals(user.Email) &&
                   Password.Equals(user.Password);
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Password.GetHashCode();
            hash = (hash * 7) + Email.GetHashCode();

            return hash;
        }
    }
}
