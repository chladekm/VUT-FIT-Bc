using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Linq;
using System.Windows;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.BL.Mappers;
using TeamChat.BL.Messages;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class TeamDetailViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly IMessageBoxService messageBoxService;
        private APIClient apiClient;

        public BindingList<UserListModelInner> MembersList { get; set; }

        public TeamDetailViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.mediator = mediator;
            this.messageBoxService = messageBoxService;
            this.apiClient = apiClient;
            this.MembersList = new BindingList<UserListModelInner>(); 

            SaveCommand = new RelayCommand(Save, CanSave);
            DeleteCommand = new RelayCommand(Delete);
            MakeUserCommand = new RelayCommand(MakeUser);
            DeleteUserFromTeamCommand = new RelayCommand<UserListModelInner>(DeleteUserFromTeam);

            mediator.Register<TeamNewMessage>(TeamNew);
            mediator.Register<TeamDetailSelectedMessage>(TeamSelected);
            mediator.Register<UserAddToTeamMessage>(AddUserToTeam);
            mediator.Register<HideMessage>(Hide);
        }

        public TeamDetailModelInner Model { get; set; }

        public ICommand SaveCommand { get; set; }
        public ICommand DeleteCommand { get; set; }
        public ICommand MakeUserCommand { get; set; }
        public ICommand DeleteUserFromTeamCommand { get; set; }

        private void Hide(HideMessage hideMessage)
        {
            Model = null;
        }

        private void MakeUser()
        {
            mediator.Send(new UserListShowMessage());
        }

        private void AddUserToTeam(UserAddToTeamMessage user)
        {
            var mapper = new UserMapper();
            var userModel = apiClient.GetUserById(user.Id);
            MembersList.Add(new UserListModelInner
            {
                Id = userModel.Id,
                Name = userModel.Name
            });
        }

        private void TeamNew(TeamNewMessage teamNewMessage)
        {
            Model = new TeamDetailModelInner();
            IDHolder.IDActualTeam = Model.Id ?? default(int);
            MembersList = new BindingList<UserListModelInner>();
        }

        private void TeamSelected(TeamDetailSelectedMessage teamDetailSelectedMessage)
        {
            Model = apiClient.GetTeamById(teamDetailSelectedMessage.Id);
            this.MembersList = new BindingList<UserListModelInner>();
            foreach (var member in Model.Members)
            {
                MembersList.Add(member);
            }
        }
        private void DeleteUserFromTeam(UserListModelInner user)
        {
            if (Model.Leader == IDHolder.IDUser)
            {
                MembersList.Remove(user);
            }
            
        }
    

        public void Delete()
        {
            if (Model.Id != null)
            {
                try
                {
                    apiClient.DeleteTeam(Model.Id ?? default(int));
                    mediator.Send(new TeamUpdatedMessage());
            }
                catch
                {
                    messageBoxService.Show($"Deleting of {Model?.Name} failed", "Deleting failed", MessageBoxButton.OK);
                }
            }

            Model = null;
        }

        private Boolean CanSave() =>
            Model != null
            && !string.IsNullOrWhiteSpace(Model.Name);

        public void Save()
        {
            Model.Members = MembersList;
            if (Model.Id != IDHolder.IDActualTeam)
            {
                Model.Members.Add(apiClient.GetLessDetailUserById(IDHolder.IDUser));
                Model.Leader = IDHolder.IDUser;
            }
            apiClient.UpdateTeam(Model);
            mediator.Send(new TeamUpdatedMessage());
            Model = null;
            IDHolder.IDActualTeam = apiClient.GetAllTeams().Last().Id.Value;
        }

    }
}