using System;

namespace TeamChat.APP.API
{
    public partial class APIClient
    {
        public APIClient(string uri) 
            : this(new Uri(uri))
        {
        }
    }
}