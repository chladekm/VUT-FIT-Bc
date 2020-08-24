using System;
using System.Windows;
using System.Windows.Controls;
using TeamChat.APP.ViewModels;

namespace TeamChat.APP.Views
{
    public abstract class UserControlBase : UserControl
    {
        protected UserControlBase() => Loaded += OnLoaded;

        private void OnLoaded(Object sender, RoutedEventArgs e)
        {
            if (DataContext is IViewModel viewModel)
            {
                viewModel.Load();
            }
        }
    }
}