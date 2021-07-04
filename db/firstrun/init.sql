CREATE TABLE users (
  uuid varchar(36) NOT NULL,
  name varchar(30),
  email varchar(30),
  PRIMARY KEY (uuid)
);

CREATE TABLE tags (
  uuid varchar(36) NOT NULL,
  tag varchar(15) NOT NULL,
  PRIMARY KEY (uuid, tag)
);
