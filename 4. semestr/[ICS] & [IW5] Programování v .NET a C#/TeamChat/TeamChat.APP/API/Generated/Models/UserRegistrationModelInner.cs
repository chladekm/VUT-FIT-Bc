// <auto-generated>
// Code generated by Microsoft (R) AutoRest Code Generator.
// Changes may cause incorrect behavior and will be lost if the code is
// regenerated.
// </auto-generated>

namespace TeamChat.APP.API.Models
{
    using Newtonsoft.Json;
    using System.Linq;

    public partial class UserRegistrationModelInner
    {
        /// <summary>
        /// Initializes a new instance of the UserRegistrationModelInner class.
        /// </summary>
        public UserRegistrationModelInner()
        {
            CustomInit();
        }

        /// <summary>
        /// Initializes a new instance of the UserRegistrationModelInner class.
        /// </summary>
        public UserRegistrationModelInner(int? id = default(int?), string name = default(string), string password = default(string), string repeatedPassword = default(string), string email = default(string))
        {
            Id = id;
            Name = name;
            Password = password;
            RepeatedPassword = repeatedPassword;
            Email = email;
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
        [JsonProperty(PropertyName = "name")]
        public string Name { get; set; }

        /// <summary>
        /// </summary>
        [JsonProperty(PropertyName = "password")]
        public string Password { get; set; }

        /// <summary>
        /// </summary>
        [JsonProperty(PropertyName = "repeatedPassword")]
        public string RepeatedPassword { get; set; }

        /// <summary>
        /// </summary>
        [JsonProperty(PropertyName = "email")]
        public string Email { get; set; }

    }
}
