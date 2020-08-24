using System.Collections.ObjectModel;
using System.Linq;
using TeamChat.Utilities;

namespace TeamChat.BL.Models
{
    public class UserDetailModel
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Password { get; set; }
        public string Email { get; set; }
        public Collection<PostModel> Posts { get; set; }
        public Collection<CommentModel> Comments { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is UserDetailModel User))
            {
                return false;
            }

            return Id == User.Id &&
                   Name == User.Name &&
                   Password == User.Password &&
                   Email == User.Email;
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Name.GetHashCode();
            hash = (hash * 7) + Password.GetHashCode();
            hash = (hash * 7) + Email.GetHashCode();

            return hash;
        }
    }
}