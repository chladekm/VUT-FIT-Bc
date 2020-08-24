using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text;
using TeamChat.Utilities;

namespace TeamChat.BL.Models
{
    public class TeamDetailModel
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public Collection<UserListModel> Members { get; set; }
        public int Leader { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is TeamDetailModel team))
            {
                return false;
            }
            
            var comparer = new CollectionComparer<UserListModel>();

            return Id == team.Id &&
                   Name.Equals(team.Name) &&
                   Leader.Equals(team.Leader) &&
                   comparer.Equals(Members, team.Members);
        }

        // https://stackoverflow.com/questions/371328/why-is-it-important-to-override-gethashcode-when-equals-method-is-overridden
        public override int GetHashCode()
        {
            int hash = 13;
            hash = (hash * 7) + Id.GetHashCode();
            hash = (hash * 7) + Name.GetHashCode();
            hash = (hash * 7) + Leader.GetHashCode();

            return hash;
        }
    }
}
