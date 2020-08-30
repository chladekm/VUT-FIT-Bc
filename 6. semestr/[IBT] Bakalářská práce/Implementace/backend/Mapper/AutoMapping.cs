/*******************************************************************/
/*  Project: Bachelor's thesis – Genealogy Database System         */
/*           Faculty of Information Technology                     */
/*           Brno University of Technology                         */
/*                                                                 */
/*  File:    AutoMapping.cs                                        */
/*  Date:    2019 – 2020                                           */
/*  Author:  Martin Chládek <xchlad16@stud.fit.vutbr.cz>           */
/*******************************************************************/

using AutoMapper;
using Entity;
using Entity.Relationships;
using PresentationModels;
using PresentationModels.Authentication;
using PresentationModels.Relationships;

namespace Mapper
{
    public class AutoMapping : Profile
    {
        private const int MaxDepth = 5;

        public AutoMapping()
        {
            CreateMap<Collision, CollisionDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<FamilyTree, FamilyTreeDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<Marriage, MarriageDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<Person, PersonDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<PersonName, PersonNameDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<OriginalRecord, OriginalRecordDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<Relationship, RelationshipDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<User, UserDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<User, UserCompleteDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);
            
            CreateMap<User, LoginCredentialsDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);
            
            CreateMap<User, AuthenticateUserDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);


            // Relationships between entities

            CreateMap<CollisionRelationship, CollisionRelationshipDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<FamilyTreePerson, FamilyTreePersonDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<FamilyTreeCollision, FamilyTreeCollisionDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);

            CreateMap<FamilyTreeRelationship, FamilyTreeRelationshipDto>().ReverseMap().PreserveReferences().MaxDepth(MaxDepth);
        }
    }
}
