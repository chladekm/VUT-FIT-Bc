/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    PasswordService.cs                                    */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using System;
using System.Security.Cryptography;

namespace Services.Cryptography
{
    public static class PasswordService
    {
        // Sizes of arrays in bytes (32 * 8 -> 256 bits)
        private const int SaltArrayLength = 32;
        private const int HashArrayLength = 32;

        // Number of iterations for hash generator
        private const int NumberOfHashingIterations = 10000;

        // Calculate final hash from passed password and salt
        public static byte[] CalculateHash(string password, byte[] salt)
        {
            using Rfc2898DeriveBytes hashGenerator = new Rfc2898DeriveBytes(password, salt);
            hashGenerator.IterationCount = NumberOfHashingIterations;
            return hashGenerator.GetBytes(HashArrayLength);
        }
        
        // Generates random numbers (creates salt to pulverize password)
        public static byte[] GenerateSalt()
        {
            byte[] salt = new byte[SaltArrayLength];

            using RNGCryptoServiceProvider saltGenerator = new RNGCryptoServiceProvider();
            saltGenerator.GetBytes(salt);
            return salt;
        }

        // Generates Salt and Hash for new registrations
        public static Tuple<string, string> CreateNewStringHashAndSalt(string password)
        {
            var passwordSalt = GenerateSalt();
            var passwordHash = CalculateHash(password, passwordSalt);

            return Tuple.Create(Convert.ToBase64String(passwordSalt), Convert.ToBase64String(passwordHash));
        }

        // Verify if string password matches the hash
        public static bool VerifyPassword(String password, byte[] databaseSalt, byte[] databaseHash)
        {
            byte[] calculatedHash = CalculateHash(password, databaseSalt);
            
            return AreHashesIdentic(calculatedHash, databaseHash);
        }

        // Compare two hash arrays and return if are equal
        public static bool AreHashesIdentic(byte[] userHash, byte[] databaseHash)
        {
            if (userHash.Length != databaseHash.Length)
                return false;

            // Initialize XOR operation to null
            var xor = 0;

            // Compare every byte
            for (int i = 0; i < HashArrayLength; i++)
                xor |= userHash[i] ^ databaseHash[i];
            
            return (xor == 0);
        }
    }
}
