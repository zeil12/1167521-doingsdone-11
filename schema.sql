CREATE DATABASE doingsdone
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE user (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    registration_date   TIMESTAMP DEFAULT  CURRENT_TIMESTAMP,
    email      VARCHAR(30) UNIQUE NOT NULL,
    user_name  VARCHAR(30) NOT NULL,
    password   CHAR(120) NOT NULL,
    INDEX (registration_date),
    UNIQUE UK_email (email),
    INDEX (user_name)
);

CREATE TABLE project (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(50) NOT NULL,
    user_id    INT NOT NULL,
    FOREIGN KEY (user_id)  REFERENCES user (id),
    INDEX (user_id)
);

CREATE TABLE task (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    creation_date   TIMESTAMP DEFAULT  CURRENT_TIMESTAMP,
    status     INT NOT NULL,
    task_name   VARCHAR(50) NOT NULL,
    file_link  VARCHAR(100),
    deadline   DATETIME NOT NULL,
    user_id    INT NOT NULL,
    project_id    INT NOT NULL,
    FOREIGN KEY (user_id)     REFERENCES user (id),
    FOREIGN KEY (project_id)  REFERENCES project (id),
    INDEX (creation_date)
);





