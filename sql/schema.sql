CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id)
);
CREATE TABLE IF NOT EXISTS sessions (
  id INT AUTO_INCREMENT,
  user_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  PRIMARY KEY(id),
  UNIQUE KEY (token),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS channels (
  id INT AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type ENUM('public', 'private', 'direct') NOT NULL DEFAULT 'public',
  owner_id INT NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (owner_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS members (
  id INT AUTO_INCREMENT,
  user_id INT NOT NULL,
  channel_id INT NOT NULL,
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY (user_id, channel_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT,
  type ENUM(
    'default',
    'user_add',
    'user_remove',
    'channel_rename'
  ) NOT NULL DEFAULT 'default',
  content TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  author_id INT,
  channel_id INT NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);