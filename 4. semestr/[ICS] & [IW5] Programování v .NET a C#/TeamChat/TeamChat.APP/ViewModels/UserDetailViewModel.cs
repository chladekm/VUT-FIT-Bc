using System;
using System.Linq;
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
    public class UserDetailViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly IMessageBoxService messageBoxService;
        private APIClient apiClient;

        public UserDetailViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.mediator = mediator;
            this.messageBoxService = messageBoxService;
            this.apiClient = apiClient;

            SaveCommand = new RelayCommand(Save, CanSave);
            DeleteCommand = new RelayCommand(Delete);
            DiscardCommand = new RelayCommand(Discard);

            mediator.Register<UserNewMessage>(UserNew);
            mediator.Register<UserSelectedMessage>(UserSelected);
        }
        public ICommand SaveCommand { get; set; }
        public ICommand DeleteCommand { get; set; }
        public ICommand DiscardCommand { get; set; }

        public UserDetailModelInner Model { get; set; }

        private void UserNew(UserNewMessage userNewMessage)
        {
            Model = new UserDetailModelInner();
        }

        private void UserSelected(UserSelectedMessage userSelectedMessage)
        {
            Model = apiClient.GetUserById(userSelectedMessage.Id);
        }

        public void Discard() => Model = null;

        public void Delete(object obj)
        {
            if (Model.Id != null)
            {
                try
                {
                    apiClient.DeleteUser(Model.Id ?? default(int));
                }
                catch
                {
                    messageBoxService.Show($"Deleting of {Model?.Name} filed", "Deleting failed", MessageBoxButton.OK);
                }
            }

            Model = null;
        }

        private Boolean CanSave() =>
            Model != null
            && !string.IsNullOrWhiteSpace(Model.Name)
            && !string.IsNullOrWhiteSpace(Model.Email)
            && !string.IsNullOrWhiteSpace(Model.Password);

        public void Save()
        {
            var encryptor = new Encrypter();

            Model.Password = encryptor.MD5EncryptPassword(Model.Password);

            // Overeni, ze zadany e-mail uz neni v databazi.
            try
            {
                var emailAuthModel = apiClient.GetUserLoginModelByEmail(Model.Email);
                if (emailAuthModel != null)
                {
                    messageBoxService.Show("This E-mail is already registered.", "Error", MessageBoxButton.OK);
                    Model = new UserDetailModelInner();
                    return;
                }
            }
            catch (Exception e)
            {
            }

            apiClient.UpdateUser(Model);

            if (Model.Id != IDHolder.IDUser)
            {
                mediator.Send(new UserUpdatedMessage());
            }
            else
            {
                Update();
                mediator.Send(new UserProfileUpdatedMessage());
            }
            Model = null;
        }

        private void Update()
        {
            var posts = apiClient.GetAllPosts().Where(p => p.Author == Model.Id);
            foreach (var post in posts)
            {
                post.AuthorName = Model.Name;
                apiClient.UpdatePost(post);
            }

            var teams = apiClient.GetAllTeams();
            foreach (var team in teams)
            {

                var teamDetail = apiClient.GetTeamById(team.Id.Value);
                var theUser = teamDetail.Members.FirstOrDefault(m => m.Id == IDHolder.IDUser);

                if (theUser != null)
                {
                    theUser.Name = Model.Name;
                    apiClient.UpdateTeam(teamDetail);
                }
                
            }
        }
    }
}