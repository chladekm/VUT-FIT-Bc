using System;
using System.Windows;

namespace TeamChat.APP.Services
{
    public class MessageBoxService : IMessageBoxService
    {
        public MessageBoxResult Show(String messageBoxText, String caption, MessageBoxButton button) =>
            MessageBox.Show(messageBoxText, caption, button);
    }
}