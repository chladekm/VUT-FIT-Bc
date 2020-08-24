﻿using System;
using System.Data.SqlClient;
using TeamChat.BL.Messages;

namespace TeamChat.BL.Services
{
    public interface IMediator
    {
        void Register<TMessage>(Action<TMessage> action)
            where TMessage : IMessage;

        void Send<TMessage>(TMessage message)
            where TMessage : IMessage;

        void UnRegister<TMessage>(Action<TMessage> action)
            where TMessage : IMessage;
    }
}