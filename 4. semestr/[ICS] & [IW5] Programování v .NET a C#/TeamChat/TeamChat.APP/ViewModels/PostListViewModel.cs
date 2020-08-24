using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Linq;
using System.Windows.Input;
using TeamChat.APP.API;
using TeamChat.APP.API.Models;
using TeamChat.APP.Commands;
using TeamChat.APP.Services;
using TeamChat.BL.Extensions;
using TeamChat.BL.Messages;
using TeamChat.BL.Services;

namespace TeamChat.APP.ViewModels
{
    public class PostListViewModel : ViewModelBase
    {
        private readonly IMediator mediator;
        private readonly IMessageBoxService messageBoxService;
        private readonly APIClient apiClient;

        public PostListViewModel(APIClient apiClient, IMessageBoxService messageBoxService, IMediator mediator)
        {
            this.apiClient = apiClient;
            this.messageBoxService = messageBoxService;
            this.mediator = mediator;

            PostNewCommand = new RelayCommand(PostNew, CanSave);
            CommentNewCommand = new RelayCommand<PostModelInner>(CommentNew);
            PostEditTeamCommand = new RelayCommand(TeamEdit);


            mediator.Register<TeamSelectedMessage>(Refresh_by_team);
            mediator.Register<PostUpdatedMessage>(Refresh_by_post);
            mediator.Register<HideMessage>(Hide);
        }

        public ObservableCollection<PostModelInner> Posts { get; } = new ObservableCollection<PostModelInner>();

        public ICommand PostNewCommand { get; }
        public ICommand CommentNewCommand { get; }
        public ICommand PostEditTeamCommand { get; }

        private System.Boolean CanSave() =>
            Posts != null
            && IDHolder.IDActualTeam != 0;

        private void CommentNew(PostModelInner postModelInner) => mediator.Send(new CommentPostNewMessage { Id = postModelInner.Id ?? default(int)});

        private void PostNew() => mediator.Send(new PostNewMessage());

        private void TeamEdit()
        {
            if (IDHolder.IDActualTeam != 0)
            {
                mediator.Send(new TeamDetailSelectedMessage {Id = IDHolder.IDActualTeam});
            }
            
        }

        private void Refresh_by_team(TeamSelectedMessage teamSelectedMessage) => LoadPosts();

        private void Refresh_by_post(PostUpdatedMessage postUpdatedMessage) => LoadPosts();

        private void Hide(HideMessage hideMessage)
        {
            Posts.Clear();
        }
        public void LoadPosts()
        {
            Posts.Clear();
            var posts = apiClient.GetAllPostsByTeamId(IDHolder.IDActualTeam);
            foreach (var post in posts)
            {
                post.Comments = apiClient.GetCommentsByPostId(post.Id ?? default(int));
            }

            if (posts != null)
            {
                posts = SortPosts(posts);
            }

            Posts.AddRange(posts);
        }
        private IList<PostModelInner> SortPosts(IList<PostModelInner> posts)
        {
            return posts.OrderByDescending(o => o.Comments.LastOrDefault() != null ? o.Comments.LastOrDefault().Id : o.Id).ToList();
        }
    }
}