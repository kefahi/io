
CREATE TABLE IF NOT EXISTS Entity (
	id INTEGER PRIMARY KEY,
  name TEXT NOT NULL,
  uri TEXT NOT NULL,
  source TEXT NULL, -- details on original source
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL,
  owner_id   INTEGER NOT NULL REFERENCES 'Entity' (id), -- of type 00: User, drop this field?
  author_id  INTEGER NOT NULL REFERENCES 'Entity' (id), -- of type 00: User, drop this field?
	description TEXT NULL,
  public_key TEXT NULL, -- For type 00: User only
  credentials TEXT NULL, -- for 00: User only
  'type' INTEGER NOT NULL -- Actor {00: User, 01: Group, ...7}, Content {8: Wiki, 9: Structure, 10: Article, 11: Media (Image, Audio and Video), 12: JSON, 13: Xml, 14: Binary, 15: Message}
);

CREATE IF NOT EXISTS EntityProperty (
  property_name TEXT NOT NULL,
  property_value BLOB NOT NULL,
  entity_id INTEGER NOT NULL REFERENCES Entity(id), 
  PRIMARY KEY(entity_id, property_name) 
);

CREATE TABLE IF NOT EXISTS AccessControl (
  content_id NOT NULL REFERENCES Entity (id), -- of type 1xxx: Content
  actor_id NOT NULL REFERENCES Entity (id), -- of  type 0x: Actor
  privilege INTEGER NOT NULL, -- Bitmap: 1:Read, 2:Create, 4:Update, 8:Sugguest Update, 16:Append Child (like comment), 32:Delete, 64:Manage Access, 128: Search
  PRIMARY KEY (content_id, actor_id)
);

CREATE TABLE IF NOT EXISTS GroupUser (
  user_id INTEGER NOT NULL REFERENCES Entity (id), -- of type 00: User
  group_id INTEGER NOT NULL REFERENCES Entity (id), -- of  type 0x: Group
  created_at INTEGER NOT NULL,
  author_id INTEGER NOT NULL REFERENCES Entity (id), -- of type 00: User
  PRIMARY KEY(user_id, group_id)
);

CREATE TABLE IF NOT EXISTS Entry (
  id INTEGER PRIMARY KEY,
  embdedded BLOB NULL,
  path TEXT NULL,
  checksum TEXT NOT NULL,
  byte_size INTEGER NOT NULL,  
  'type' INTEGER NOT NULL, -- 1: Embedded, 2: External
  entity_id INTEGER NOT NULL REFERENCES Entity (id) -- of type 1xxx: Content
);

CREATE TABLE IF NOT EXISTS Tag (
  id INTEGER PRIMARY KEY,
	name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS EntityTags (
  entity_id INTEGER REFERENCES Entity(id),
	tag_id INTEGER REFERENCES Tag (id),
  PRIMARY KEY (entity_id, tag_id) 
);

CREATE TABLE IF NOT EXISTS Link (
	id INTEGER PRIMARY KEY,
  from_id INTEGER REFERENCES Entity(id),
  to_id INTEGER REFERENCES Entity(id),
  UNIQUE (from_id, to_id, property_name)
);

CREATE TABLE IF NOT EXISTS LinkProperty (
  property_name TEXT NOT NULL,
  property_value BLOB NOT NULL,
  entity_id INTEGER NOT NULL REFERENCES Entity(id), 
  PRIMARY KEY(entity_id, property_name) 
);

CREATE TABLE IF NOT EXISTS Change (
	id INTEGER PRIMARY KEY,
	entity_id INTEGER NOT NULL REFERENCES Entity(id), -- of type 1xxx: Content
  author_id INTEGER NOT NULL REFERENCES Entity(id), -- of type 00: User
  signature BLOB NOT NULL,
  delta BLOB NOT NULL,
  created_at INTEGER NOT NULL,
  description TEXT NULL,
  'type' INTEGER NOT NULL -- 0: New entry, 1: Delete, 1xxx: Modified
);

