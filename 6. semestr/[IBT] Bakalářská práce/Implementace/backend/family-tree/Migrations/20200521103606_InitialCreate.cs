using System;
using Microsoft.EntityFrameworkCore.Metadata;
using Microsoft.EntityFrameworkCore.Migrations;

namespace FamilyTree.Migrations
{
    public partial class InitialCreate : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "Collisions",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Type = table.Column<string>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Collisions", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Persons",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    IsFemale = table.Column<bool>(nullable: false),
                    BirthDate = table.Column<DateTime>(nullable: true),
                    BaptismDate = table.Column<DateTime>(nullable: true),
                    DeathDate = table.Column<DateTime>(nullable: true),
                    BirthPlace = table.Column<string>(nullable: true),
                    IsPrivate = table.Column<bool>(nullable: false),
                    IsUndefined = table.Column<bool>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Persons", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "Users",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Name = table.Column<string>(nullable: true),
                    Surname = table.Column<string>(nullable: true),
                    Nickname = table.Column<string>(nullable: false),
                    Email = table.Column<string>(nullable: true),
                    Password = table.Column<string>(nullable: false),
                    Salt = table.Column<string>(nullable: false),
                    RegisterDate = table.Column<DateTime>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Users", x => x.Id);
                });

            migrationBuilder.CreateTable(
                name: "OriginalRecords",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    RecordId = table.Column<int>(nullable: false),
                    PersonId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_OriginalRecords", x => x.Id);
                    table.ForeignKey(
                        name: "FK_OriginalRecords_Persons_PersonId",
                        column: x => x.PersonId,
                        principalTable: "Persons",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "PersonNames",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Name = table.Column<string>(nullable: false),
                    isFirstName = table.Column<bool>(nullable: false),
                    PersonId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_PersonNames", x => x.Id);
                    table.ForeignKey(
                        name: "FK_PersonNames_Persons_PersonId",
                        column: x => x.PersonId,
                        principalTable: "Persons",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Relationships",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Type = table.Column<string>(nullable: false),
                    AncestorOrHusbandPersonId = table.Column<int>(nullable: false),
                    DescendantOrWifePersonId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Relationships", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Relationships_Persons_AncestorOrHusbandPersonId",
                        column: x => x.AncestorOrHusbandPersonId,
                        principalTable: "Persons",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_Relationships_Persons_DescendantOrWifePersonId",
                        column: x => x.DescendantOrWifePersonId,
                        principalTable: "Persons",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "FamilyTrees",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    Type = table.Column<string>(nullable: false),
                    Title = table.Column<string>(nullable: true),
                    UserId = table.Column<int>(nullable: false),
                    StartPersonId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_FamilyTrees", x => x.Id);
                    table.ForeignKey(
                        name: "FK_FamilyTrees_Persons_StartPersonId",
                        column: x => x.StartPersonId,
                        principalTable: "Persons",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_FamilyTrees_Users_UserId",
                        column: x => x.UserId,
                        principalTable: "Users",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "CollisionRelationship",
                columns: table => new
                {
                    CollisionId = table.Column<int>(nullable: false),
                    RelationshipId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_CollisionRelationship", x => new { x.CollisionId, x.RelationshipId });
                    table.ForeignKey(
                        name: "FK_CollisionRelationship_Collisions_CollisionId",
                        column: x => x.CollisionId,
                        principalTable: "Collisions",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_CollisionRelationship_Relationships_RelationshipId",
                        column: x => x.RelationshipId,
                        principalTable: "Relationships",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "Marriages",
                columns: table => new
                {
                    Id = table.Column<int>(nullable: false)
                        .Annotation("MySql:ValueGenerationStrategy", MySqlValueGenerationStrategy.IdentityColumn),
                    MarriageDate = table.Column<DateTime>(nullable: false),
                    MarriageAddress = table.Column<string>(nullable: true),
                    RelationshipId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Marriages", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Marriages_Relationships_RelationshipId",
                        column: x => x.RelationshipId,
                        principalTable: "Relationships",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "FamilyTreeCollision",
                columns: table => new
                {
                    FamilyTreeId = table.Column<int>(nullable: false),
                    CollisionId = table.Column<int>(nullable: false),
                    IsSolved = table.Column<bool>(nullable: false),
                    SolutionDate = table.Column<DateTime>(nullable: true)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_FamilyTreeCollision", x => new { x.FamilyTreeId, x.CollisionId });
                    table.ForeignKey(
                        name: "FK_FamilyTreeCollision_Collisions_CollisionId",
                        column: x => x.CollisionId,
                        principalTable: "Collisions",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_FamilyTreeCollision_FamilyTrees_FamilyTreeId",
                        column: x => x.FamilyTreeId,
                        principalTable: "FamilyTrees",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "FamilyTreePerson",
                columns: table => new
                {
                    FamilyTreeId = table.Column<int>(nullable: false),
                    PersonId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_FamilyTreePerson", x => new { x.FamilyTreeId, x.PersonId });
                    table.ForeignKey(
                        name: "FK_FamilyTreePerson_FamilyTrees_FamilyTreeId",
                        column: x => x.FamilyTreeId,
                        principalTable: "FamilyTrees",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_FamilyTreePerson_Persons_PersonId",
                        column: x => x.PersonId,
                        principalTable: "Persons",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateTable(
                name: "FamilyTreeRelationship",
                columns: table => new
                {
                    FamilyTreeId = table.Column<int>(nullable: false),
                    RelationshipId = table.Column<int>(nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_FamilyTreeRelationship", x => new { x.FamilyTreeId, x.RelationshipId });
                    table.ForeignKey(
                        name: "FK_FamilyTreeRelationship_FamilyTrees_FamilyTreeId",
                        column: x => x.FamilyTreeId,
                        principalTable: "FamilyTrees",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                    table.ForeignKey(
                        name: "FK_FamilyTreeRelationship_Relationships_RelationshipId",
                        column: x => x.RelationshipId,
                        principalTable: "Relationships",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_CollisionRelationship_RelationshipId",
                table: "CollisionRelationship",
                column: "RelationshipId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyTreeCollision_CollisionId",
                table: "FamilyTreeCollision",
                column: "CollisionId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyTreePerson_PersonId",
                table: "FamilyTreePerson",
                column: "PersonId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyTreeRelationship_RelationshipId",
                table: "FamilyTreeRelationship",
                column: "RelationshipId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyTrees_StartPersonId",
                table: "FamilyTrees",
                column: "StartPersonId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyTrees_UserId",
                table: "FamilyTrees",
                column: "UserId");

            migrationBuilder.CreateIndex(
                name: "IX_Marriages_RelationshipId",
                table: "Marriages",
                column: "RelationshipId");

            migrationBuilder.CreateIndex(
                name: "IX_OriginalRecords_PersonId",
                table: "OriginalRecords",
                column: "PersonId");

            migrationBuilder.CreateIndex(
                name: "IX_PersonNames_PersonId",
                table: "PersonNames",
                column: "PersonId");

            migrationBuilder.CreateIndex(
                name: "IX_Relationships_AncestorOrHusbandPersonId",
                table: "Relationships",
                column: "AncestorOrHusbandPersonId");

            migrationBuilder.CreateIndex(
                name: "IX_Relationships_DescendantOrWifePersonId",
                table: "Relationships",
                column: "DescendantOrWifePersonId");
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "CollisionRelationship");

            migrationBuilder.DropTable(
                name: "FamilyTreeCollision");

            migrationBuilder.DropTable(
                name: "FamilyTreePerson");

            migrationBuilder.DropTable(
                name: "FamilyTreeRelationship");

            migrationBuilder.DropTable(
                name: "Marriages");

            migrationBuilder.DropTable(
                name: "OriginalRecords");

            migrationBuilder.DropTable(
                name: "PersonNames");

            migrationBuilder.DropTable(
                name: "Collisions");

            migrationBuilder.DropTable(
                name: "FamilyTrees");

            migrationBuilder.DropTable(
                name: "Relationships");

            migrationBuilder.DropTable(
                name: "Users");

            migrationBuilder.DropTable(
                name: "Persons");
        }
    }
}
