using System;
using System.Collections.Generic;
using System.Text;
using TeamChat.BL.Models;
using TeamChat.DAL.Entities;

namespace TeamChat.BL.Repositories
{
    public interface IGenericRepository<T1, T2>
    {
        List<T2> GetAll();
        T1 GetById(int id);
        T1 Create(T1 model);
        void Update(T1 model);
        void Delete(int id);
    }
}
