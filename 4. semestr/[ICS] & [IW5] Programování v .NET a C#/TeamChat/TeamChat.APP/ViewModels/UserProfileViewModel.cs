using System;
using System.Threading;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.BL.Messages;
using TeamChat.BL.Models;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class UserProfileViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly IMessageBoxService messageBoxService;
        private readonly APIClient apiClient;

        public string LastPost { get; set; }
        public string LastComment { get; set; }

        public UserProfileViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.apiClient = apiClient;
            this.messageBoxService = messageBoxService;
            this.mediator = mediator;

            mediator.Register<UserProfileShowMessage>(ShowProfile_from_menu);
            mediator.Register<UserProfileUpdatedMessage>(ShowProfile_from_user);
            mediator.Register<UserProfileCloseMessage>(CloseProfile_from_start);



            UserProfileCloseCommand = new RelayCommand(CloseProfile);
            UserProfileUpdateCommand = new RelayCommand(UpdateProfile);
            UserProfileDeleteCommand = new RelayCommand(DeleteProfile);

        }

        public UserProfileModelInner Profile { get; set; }

        public ICommand UserProfileCloseCommand { get; set; }
        public ICommand UserProfileUpdateCommand { get; set; }
        public ICommand UserProfileDeleteCommand { get; set; }


        public void ShowProfile_from_menu(UserProfileShowMessage user) => ShowProfile();
        public void ShowProfile_from_user(UserProfileUpdatedMessage user) => ShowProfile();
        public void CloseProfile_from_start(UserProfileCloseMessage user) => CloseProfile();


        public void ShowProfile()
        {
            Profile = new UserProfileModelInner();

            Profile = apiClient.GetUserProfileModelById(IDHolder.IDUser);

            try
            {
                LastPost = apiClient.GetLastPostByUserId(IDHolder.IDUser).Title;
            }
            catch (Exception e)
            {
                LastPost = null;
            }

            try
            {
                LastComment = apiClient.GetLastCommentByUserId(IDHolder.IDUser).Text;
            }
            catch (Exception e)
            {
                LastComment = null;
            }
            
        }

        public void UpdateProfile()
        {
            mediator.Send(new UserSelectedMessage{ Id = Profile.Id ?? default(int)});
        }

        public void DeleteProfile()
        {
            apiClient.DeleteUser(IDHolder.IDUser);
            mediator.Send(new UserLogoutMessage());
        }

        public void CloseProfile()
        {
            try
            {
                Profile = null;
            }
            catch{}

        }
    }
}