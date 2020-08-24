using System;
using System.Windows;

namespace TeamChat.APP.Services
{
    public interface IMessageBoxService
    {
        MessageBoxResult Show(String messageBoxText, String caption, MessageBoxButton button);
    }
}