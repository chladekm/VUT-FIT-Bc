using System.Collections.ObjectModel;
using TeamChat.BL.Models;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Mappers
{
    public class TeamMapper
    {
        public TeamDetailModel EntityToDetailModel(TeamEntity teamEntity)
        {
            if (teamEntity == null)
            {
                return null;
            }

            var teamModel = new TeamDetailModel
            {
                Id = teamEntity.Id,
                Leader = teamEntity.Leader,
                Name = teamEntity.Name,
                Members = new Collection<UserListModel>()
            };
            if (teamEntity.Members == null)
            {
                teamEntity.Members = new Collection<TeamUserEntity>();
            }
            else
            {
                foreach (var member in teamEntity.Members)
                {
                    teamModel.Members.Add(new UserListModel()
                    {
                        Id = member.UserId,
                        Name = member.UserName
                    });
                }
            }
            return teamModel;
        }

        public TeamListModel EntityToListModel(TeamEntity teamEntity)
        {
            if (teamEntity == null)
            {
                return null;
            }

            var teamModel = new TeamListModel()
            {
                Id = teamEntity.Id,
                Name = teamEntity.Name
            };
            return teamModel;
        }

        public TeamEntity DetailModelToEntity(TeamDetailModel teamDetailModel)
        {
            if (teamDetailModel == null)
            {
                return null;
            }

            var teamEntity = new TeamEntity
            {
                Id = teamDetailModel.Id,
                Leader = teamDetailModel.Leader,
                Name = teamDetailModel.Name,
                Members = new Collection<TeamUserEntity>()
            };

            if (teamDetailModel.Members == null)
            {
                teamDetailModel.Members = new Collection<UserListModel>();
            }
            else
            {
                foreach (var member in teamDetailModel.Members)
                {
                    var user = new TeamUserEntity
                    {
                        UserName = member.Name,
                        UserId = member.Id
                    };
                    teamEntity.Members.Add(user);
                }
            }
            return teamEntity;
        }

        public TeamListModel DetailModelToListModel(TeamDetailModel teamDetailModel)
        {
            if (teamDetailModel == null)
            {
                return null;
            }

            var teamListModel = new TeamListModel()
            {
                Id = teamDetailModel.Id,
                Name = teamDetailModel.Name
            };
            return teamListModel;
        }
    }
}
