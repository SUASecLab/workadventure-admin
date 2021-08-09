CREATE TABLE users (
  uuid varchar(36) NOT NULL,
  name varchar(30) NOT NULL,
  email varchar(60) NOT NULL UNIQUE,
  visitCardUrl varchar(100),
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

CREATE TABLE global_messages (
  message_id integer NOT NULL AUTO_INCREMENT,
  message varchar(1000),
  PRIMARY KEY (message_id)
);

CREATE TABLE hidden_global_messages (
  uuid varchar(36) NOT NULL,
  message_id integer NOT NULL,
  PRIMARY KEY (uuid, message_id),
  FOREIGN KEY (uuid) REFERENCES users(uuid),
  FOREIGN KEY (message_id) REFERENCES global_messages(message_id)
);

CREATE TABLE user_messages (
  message_id integer NOT NULL AUTO_INCREMENT,
  user_uuid varchar(36) NOT NULL,
  message varchar(1000),
  PRIMARY KEY (message_id)
);

CREATE TABLE textures (
  texture_table_id integer NOT NULL AUTO_INCREMENT,
  texture_id integer NOT NULL,
  texture_level integer NOT NULL,
  url varchar(100),
  rights varchar(100),
  notice varchar(250),
  PRIMARY KEY (texture_table_id)
);
