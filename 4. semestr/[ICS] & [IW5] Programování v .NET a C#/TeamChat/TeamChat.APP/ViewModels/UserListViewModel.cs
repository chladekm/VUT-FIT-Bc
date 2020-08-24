using System;
using System.Collections;
using System.Collections.ObjectModel;
using System.Linq;
using System.Windows.Documents.Serialization;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.APP.ViewModels;
using TeamChat.BL.Extensions;
using TeamChat.BL.Messages;
using TeamChat.BL.Models;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class UserListViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly APIClient apiClient;

        public UserListViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.apiClient = apiClient;
            this.mediator = mediator;

            UserSelectedCommand = new RelayCommand<UserListModelInner>(UserSelected);
            UserNewCommand = new RelayCommand(UserNew);
            UserEndSessionCommand = new RelayCommand(UserEndSession);
            UserAddToTeamCommand = new RelayCommand<UserListModelInner>(UserAddToTeam);

            mediator.Register<UserUpdatedMessage>(UserUpdated);
            mediator.Register<UserDeletedMessage>(UserDeleted);
            mediator.Register<UserListShowMessage>(UserShowList);
            mediator.Register<UserListCloseMessage>(UserEndSession_from_start);
        }

        public ObservableCollection<UserListModelInner> Users { get; } = new ObservableCollection<UserListModelInner>();
        public ObservableCollection<UserListModelInner> AllUsers { get; set; }

        public ICommand UserSelectedCommand { get; }
        public ICommand UserNewCommand { get; }

        public ICommand UserEndSessionCommand { get; }

        public ICommand UserAddToTeamCommand { get; }

        private void UserNew() => mediator.Send(new UserNewMessage());

        private void UserSelected(UserListModelInner user) => mediator.Send(new UserSelectedMessage { Id = user.Id ?? default(int) });

        private void UserUpdated(UserUpdatedMessage user) //=> Load();
        {
            if (AllUsers != null)
            {
                UserShowList(null);
            }
            else
            {
                Load();
            }
        }

        private void UserDeleted(UserDeletedMessage user) => Load();

        private void UserAddToTeam(UserListModelInner user) => mediator.Send(new UserAddToTeamMessage {Id = user.Id ?? default(int)} );

        private void UserEndSession_from_start(UserListCloseMessage user) => UserEndSession();

        private void UserShowList(UserListShowMessage user)
        {
            AllUsers = new ObservableCollection<UserListModelInner>();

            AllUsers.Clear();
            try
            {
                var allusers = apiClient.GetAllUsers().OrderBy(u => u.Name);
                AllUsers.AddRange(allusers);
            }
            catch
            {
                AllUsers.Clear();
            }
        }

        private void UserEndSession()
        {
            try
            {
                AllUsers = null;
            }
            catch{}
            
        }

        public override void Load()
        {
            Users.Clear();
            try
            {
                var users = apiClient.GetAllUsers();
                Users.AddRange(users);
            }
            catch
            {
                Users.Clear();
            }
        }
    }
}