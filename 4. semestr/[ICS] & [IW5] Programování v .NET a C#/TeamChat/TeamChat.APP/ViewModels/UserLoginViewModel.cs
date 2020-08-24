using System;
using System.Windows;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.BL.Messages;
using TeamChat.BL.Services;
using TeamChat.Utilities;

namespace TeamChat.APP.ViewModels
{
    public class UserLoginViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly IMessageBoxService messageBoxService;
        private APIClient apiClient;

        public UserLoginViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.mediator = mediator;
            this.messageBoxService = messageBoxService;
            this.apiClient = apiClient;

            LoginCommand = new RelayCommand(Login, CanSave);

            mediator.Register<UserLogoutMessage>(Relogin);

            Model = new UserLoginModelInner();
        }
      
        public UserLoginModelInner Model { get; set; }
        private UserLoginModelInner Control { get; set; }

        public ICommand LoginCommand { get; set; }

        private Boolean CanSave() =>
            Model != null
            && !string.IsNullOrWhiteSpace(Model.Password)
            && !string.IsNullOrWhiteSpace(Model.Email);

        public void Relogin(UserLogoutMessage user)
        {
            mediator.Send(new CommentDeleteMessage());
            mediator.Send(new HideMessage());
            IDHolder.IDActualTeam = 0;
            Model = new UserLoginModelInner();
        }

        public void Login()
        {
            try
            {
                Control = apiClient.GetUserLoginModelByEmail(Model.Email);
                var encrypter = new Encrypter();
                if (encrypter.MD5EncryptPassword(Model.Password) != Control.Password)
                {
                    messageBoxService.Show("Wrong combination of e-mail and password!", "Error", MessageBoxButton.OK);
                    Model = new UserLoginModelInner();
                }
                else
                {
                    IDHolder.IDUser = Control.Id ?? default(int);
                    var userModel = apiClient.GetUserById(IDHolder.IDUser);
                    IDHolder.NameUser = userModel.Name;

                    mediator.Send(new UserProfileCloseMessage());
                    mediator.Send(new UserListCloseMessage());
                    mediator.Send(new TeamUpdatedMessage());
                    Model = null;
                }
            }
            catch
            {
                messageBoxService.Show("Wrong combination of e-mail and password!", "Error", MessageBoxButton.OK);
                Model = new UserLoginModelInner();
            }
            

            
        }
    }
}