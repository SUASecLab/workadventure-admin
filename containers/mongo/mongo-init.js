db.createUser(
        {
            user: "workadventure",
            pwd: "workadventure",
            roles: [
                {
                    role: "readWrite",
                    db: "workadventure"
                }
            ]
        }
);

db.createCollection("maps");
db.createCollection("mapRedirects");
db.createCollection("textures");
db.createCollection("users");
db.createCollection("websiteUsers");
