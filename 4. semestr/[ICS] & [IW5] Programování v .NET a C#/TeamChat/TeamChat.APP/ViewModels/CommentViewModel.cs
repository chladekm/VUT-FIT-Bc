using System;
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
    public class CommentViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly APIClient apiClient;
        private CommentModelInner model;

        public CommentViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.apiClient = apiClient;
            this.mediator = mediator;

            SaveCommand = new RelayCommand(Save, CanSave);
            DeleteCommand = new RelayCommand(Delete);
            
            mediator.Register<CommentSelectedMessage>(CommentSelected);
            mediator.Register<CommentPostNewMessage>(CommentNew);
            mediator.Register<CommentDeleteMessage>(CommentDelete);
        }

        public ICommand CommentNewCommand { get; set; }

        public ICommand DeleteCommand { get; set; }

        public CommentModelInner Model
        {
            get => model;
            set
            {
                model = value;
                OnPropertyChanged();
            }
        }

        public ICommand SaveCommand { get; }

        //private void commentSelected(CommentSelectedMessage commentSelectedMessage) => Model = commentSelectedMessage.CommentModel;

        private void CommentSelected(CommentSelectedMessage commentSelectedMessage)
        {
            var comment = apiClient.GetAllComments();

            Model = (CommentModelInner)comment;
        }

        private void CommentNew(CommentPostNewMessage message)
        {
            Model = new CommentModelInner();
            Model.Post = message.Id;
        }

        private void CommentDelete(CommentDeleteMessage message)
        {
            Model = null;
        }

        private void Delete()
        {
            mediator.Send(new TeamSelectedMessage());
            //apiClient.DeleteComment(Model.Id ?? default(int));
            Model = null;
        }

        private Boolean CanSave() =>
            Model != null
            && !string.IsNullOrWhiteSpace(Model.Text);

        private void Save()
        {
            Model.Author = IDHolder.IDUser;
            Model.Date = DateTime.Now;
            Model.AuthorName = IDHolder.NameUser;
            
            apiClient.CreateComment(Model);

            mediator.Send(new PostUpdatedMessage());
            Model = null;
        }
    }
}