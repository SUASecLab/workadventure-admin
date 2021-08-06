CREATE TABLE users (
  uuid varchar(36) NOT NULL,
  name varchar(30) NOT NULL,
  email varchar(30) NOT NULL,
  PRIMARY KEY (uuid)
);

CREATE TABLE tags (
  uuid varchar(36) NOT NULL,
  tag varchar(15) NOT NULL,
  PRIMARY KEY (uuid, tag),
  FOREIGN KEY (uuid) REFERENCES users(uuid)
);

CREATE TABLE reports (
  report_id integer NOT NULL AUTO_INCREMENT,
  reportedUserUuid varchar(36) NOT NULL,
  reportedUserComment varchar(1000) NOT NULL,
  reporterUserUuid varchar(36) NOT NULL,
  reportWorldSlug varchar(20) NOT NULL,
  PRIMARY KEY (report_id),
  FOREIGN KEY (reportedUserUuid) REFERENCES users(uuid),
  FOREIGN KEY (reporterUserUuid) REFERENCES users(uuid)
);

CREATE TABLE banned_users (
  uuid varchar(36) NOT NULL,
  ban_message varchar(50) NOT NULL,
  PRIMARY KEY (uuid),
  FOREIGN KEY (uuid) REFERENCES users(uuid)
);

CREATE TABLE maps (
  map_url varchar(100) NOT NULL,
  map_file_url varchar(100) NOT NULL,
  policy integer NOT NULL,
  PRIMARY KEY (map_url)
);

CREATE TABLE maps_tags (
  map_url varchar(100) NOT NULL,
  tag varchar(15) NOT NULL,
  PRIMARY KEY (map_url, tag),
  FOREIGN KEY (map_url) REFERENCES maps(map_url)
);

CREATE TABLE website (
  username varchar(16) NOT NULL,
  hashed_password varchar(160) NOT NULL,
  PRIMARY KEY (username)
);

CREATE TABLE preferences (
  preference_key varchar(100) NOT NULL,
  preference_value varchar(250) NOT NULL,
  PRIMARY KEY (preference_key)
);
