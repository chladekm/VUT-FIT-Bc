using System;
using System.Collections.Generic;
using System.Security.Cryptography;
using System.Text;

namespace TeamChat.Utilities
{
    public class Encrypter
    {
        public string MD5EncryptPassword(string password)
        {
            var md5Hash = MD5.Create();
            var byteHashedPassword = md5Hash.ComputeHash(Encoding.UTF8.GetBytes(password));

            var sBuilder = new StringBuilder();
            foreach (var t in byteHashedPassword)
            {
                sBuilder.Append(t.ToString("x2"));
            }

            return sBuilder.ToString();
        }
    }
}
