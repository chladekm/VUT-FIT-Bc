using System;
using System.Collections.Generic;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.BL.Messages;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class PostViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        //private readonly IMessageBoxService messageBoxService;
        private APIClient apiClient;

        public PostViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.mediator = mediator;
            //this.messageBoxService = messageBoxService;
            this.apiClient = apiClient;

            SaveCommand = new RelayCommand(Save, CanSave);
            DeleteCommand = new RelayCommand(Delete);

            mediator.Register<PostNewMessage>(PostNew);
            mediator.Register<HideMessage>(Hide);
        }

        public ICommand SaveCommand { get; set; }
        public ICommand DeleteCommand { get; set; }
        public PostModelInner Model { get; set; }

        private void Hide(HideMessage message)
        {
            Model = null;
        }

        private void PostNew(PostNewMessage postNewMessage)
        {
            Model = new PostModelInner();
            Model.Comments = new List<CommentModelInner>();
        }

        private Boolean CanSave() =>
            Model != null
            && !string.IsNullOrWhiteSpace(Model.Title)
            && !string.IsNullOrWhiteSpace(Model.Text);

        public void Save()
        {
            Model.Author = IDHolder.IDUser;
            Model.AuthorName = apiClient.GetLessDetailUserById(IDHolder.IDUser).Name;
            Model.Team = IDHolder.IDActualTeam;
            Model.Date = DateTime.Now;
            apiClient.CreatePost(Model);

            mediator.Send(new PostUpdatedMessage());
            Model = null;
        }

        public void Delete()
        {
            Model = null;
        }
    }
}