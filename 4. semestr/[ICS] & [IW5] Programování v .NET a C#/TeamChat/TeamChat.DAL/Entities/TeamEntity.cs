using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Reflection.Metadata;
using System.Text;
using TeamChat.Utilities;

namespace TeamChat.DAL.Entities
{
    public class TeamEntity : EntityBase
    {
        public string Name { get; set; }
        public Collection<TeamUserEntity> Members  { get; set; }
        public int Leader { get; set; }

        public override bool Equals(object obj)
        {
            if (!(obj is TeamEntity Team))
            {
                return false;
            }

            var Comparer = new CollectionComparer<TeamUserEntity>();

            return Id == Team.Id &&
                   Name.Equals(Team.Name) &&
                   Leader.Equals(Team.Leader) &&
                   Comparer.Equals(Members, Team.Members);
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
