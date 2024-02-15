CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    attempts INT DEFAULT 0,
    last_attempt TIMESTAMP CURRENT_TIMESTAMP,
    status_lock INT DEFAULT 0
);