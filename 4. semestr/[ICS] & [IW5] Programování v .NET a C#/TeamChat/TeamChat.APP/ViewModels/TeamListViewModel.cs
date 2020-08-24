using System;
using System.Collections;
using System.Collections.ObjectModel;
using System.Linq;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.BL.Extensions;
using TeamChat.BL.Messages;
using TeamChat.BL.Models;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class TeamListViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly APIClient apiClient;

        public TeamListViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.apiClient = apiClient;
            this.mediator = mediator;

            TeamSelectedCommand = new RelayCommand<TeamListModelInner>(TeamSelected);
            TeamNewCommand = new RelayCommand(TeamNew);
            UserProfileShowCommand = new RelayCommand(UserProfileShow);
            UserLogoutCommand = new RelayCommand(Logout);


            mediator.Register<TeamUpdatedMessage>(TeamUpdated);
            mediator.Register<TeamDeletedMessage>(TeamDeleted);
        }

        public ObservableCollection<TeamListModelInner> Teams { get; } = new ObservableCollection<TeamListModelInner>();

        public ICommand TeamSelectedCommand { get; }
        public ICommand TeamNewCommand { get; }
 
        public ICommand UserProfileShowCommand { get; set; }
        public ICommand UserLogoutCommand { get; set; }


        private void UserProfileShow() => mediator.Send(new UserProfileShowMessage());
        private void Logout() => mediator.Send(new UserLogoutMessage());
        

        private void TeamNew() => mediator.Send(new TeamNewMessage());

        private void TeamSelected(TeamListModelInner team)
        {
            IDHolder.IDActualTeam = team.Id ?? default(int);
            mediator.Send(new UserListCloseMessage());
            mediator.Send(new UserProfileCloseMessage());
            mediator.Send(new TeamSelectedMessage {Id = team.Id ?? default(int)});
        }

        private void TeamUpdated(TeamUpdatedMessage team) => Load();
        
        private void TeamDeleted(TeamDeletedMessage team) => Load();

        public override void Load()
        {
            try
            {
                Teams.Clear();
                var teams = apiClient.GetTeamsForUserId(IDHolder.IDUser).OrderBy(team => team.Name);
                Teams.AddRange(teams);
            }
            catch
            {
                Teams.Clear();
            }
        }
    }
}