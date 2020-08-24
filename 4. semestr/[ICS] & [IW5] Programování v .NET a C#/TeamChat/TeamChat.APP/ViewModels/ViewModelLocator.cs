using System;
using Microsoft.Rest;
using TeamChat.APP.API;
using TeamChat.APP.Services;
using TeamChat.BL.Messages;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class ViewModelLocator
    {
        private readonly IMediator mediator;
        private readonly IMessageBoxService messageBoxService;
        private APIClient apiClient;

        public ViewModelLocator()
        {
            mediator = new Mediator();
            messageBoxService = new MessageBoxService();
            //apiClient = new APIClient("https://localhost:5001/");
            apiClient = new APIClient("https://localhost:44374/");
            //apiClient.Dispose();
        }

        public CommentViewModel CommentViewModel => new CommentViewModel(apiClient, messageBoxService, mediator);

        public PostViewModel PostViewModel => new PostViewModel(apiClient, messageBoxService, mediator);
        public PostListViewModel PostListViewModel => new PostListViewModel(apiClient, messageBoxService, mediator);

        public TeamDetailViewModel TeamDetailViewModel => new TeamDetailViewModel(apiClient, messageBoxService, mediator);
        public TeamListViewModel TeamListViewModel => new TeamListViewModel(apiClient, messageBoxService, mediator);

        public UserDetailViewModel UserDetailViewModel => new UserDetailViewModel(apiClient, messageBoxService, mediator);
        public UserListViewModel UserListViewModel => new UserListViewModel(apiClient, messageBoxService, mediator);

        public UserProfileViewModel UserProfileViewModel => new UserProfileViewModel(apiClient, messageBoxService, mediator);

        public UserLoginViewModel UserLoginViewModel => new UserLoginViewModel(apiClient, messageBoxService, mediator);

        public UserRegistrationViewModel UserRegistrationViewModel => new UserRegistrationViewModel(apiClient, messageBoxService, mediator);
    }
}