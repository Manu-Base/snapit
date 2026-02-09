-- SnapIt database schema and seed data.

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL
);

CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL
);

CREATE TABLE timetables (
  user_id INT NOT NULL,
  day_of_week VARCHAR(10) NOT NULL,
  start_time TIME NULL,
  end_time TIME NULL,
  is_holiday TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (user_id, day_of_week),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE time_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  day_of_week VARCHAR(10) NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  started_at DATETIME NOT NULL,
  ended_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  day_of_week VARCHAR(10) NOT NULL,
  created_at DATETIME NOT NULL,
  completed_at DATETIME NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE task_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_id INT NOT NULL,
  user_id INT NOT NULL,
  session_id INT NOT NULL,
  completed_at DATETIME NOT NULL,
  FOREIGN KEY (task_id) REFERENCES tasks(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (session_id) REFERENCES time_sessions(id)
);

CREATE TABLE roadmaps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE roadmap_nodes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  roadmap_id INT NOT NULL,
  parent_id INT NULL,
  node_type ENUM('goal','phase','task') NOT NULL,
  title VARCHAR(255) NOT NULL,
  FOREIGN KEY (roadmap_id) REFERENCES roadmaps(id)
);

CREATE TABLE roadmap_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  roadmap_id INT NOT NULL,
  user_id INT NOT NULL,
  role ENUM('owner','editor','viewer') NOT NULL DEFAULT 'viewer',
  FOREIGN KEY (roadmap_id) REFERENCES roadmaps(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE knowledge_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  blocks_json JSON NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE learning_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  summary TEXT NOT NULL,
  roadmap_id INT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE analytics_cache (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  cache_key VARCHAR(100) NOT NULL,
  cache_json JSON NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uniq_user_cache (user_id, cache_key),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  event_type VARCHAR(100) NOT NULL,
  event_data JSON NOT NULL,
  created_at DATETIME NOT NULL
);

-- Seed admin user (password: ChangeMe123!)
INSERT INTO admin_users (email, password_hash, status, created_at)
VALUES ('admin@yourdomain.com', '$2y$10$WuhY8j7RNS3fZ2VQd6OeKOxZ9ZyNcE7duajT9tR5evRjd2sK1lL7y', 'active', NOW());
