// <auto-generated>
// Code generated by Microsoft (R) AutoRest Code Generator.
// Changes may cause incorrect behavior and will be lost if the code is
// regenerated.
// </auto-generated>

namespace TeamChat.APP.API.Models
{
    using Newtonsoft.Json;
    using System.Linq;

    public partial class UserLoginModelInner
    {
        /// <summary>
        /// Initializes a new instance of the UserLoginModelInner class.
        /// </summary>
        public UserLoginModelInner()
        {
            CustomInit();
        }

        /// <summary>
        /// Initializes a new instance of the UserLoginModelInner class.
        /// </summary>
        public UserLoginModelInner(int? id = default(int?), string email = default(string), string password = default(string))
        {
            Id = id;
            Email = email;
            Password = password;
            CustomInit();
        }

        /// <summary>
        /// An initialization method that performs custom operations like setting defaults
        /// </summary>
        partial void CustomInit();

        /// <summary>
        /// </summary>
        [JsonProperty(PropertyName = "id")]
        public int? Id { get; set; }

        /// <summary>
        /// </summary>
        [JsonProperty(PropertyName = "email")]
        public string Email { get; set; }

        /// <summary>
        /// </summary>
        [JsonProperty(PropertyName = "password")]
        public string Password { get; set; }

    }
}
