CREATE TABLE users (
    id INT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    pwd VARCHAR(150),
    perm INT,
    creation DATETIME DEFAULT CURRENT_TIMESTAMP
);