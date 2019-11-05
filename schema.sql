CREATE DATABASE doingsdone
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE project (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50) NOT NULL
);

CREATE INDEX pj_name ON project(name);

CREATE TABLE task (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    crn_date   TIMESTAMP DEFAULT  CURRENT_TIMESTAMP,
    status     INT NOT NULL,
    tsk_name   VARCHAR(50) NOT NULL,
    file_link  VARCHAR(100),
    deadline   TIMESTAMP DEFAULT  CURRENT_TIMESTAMP
);

CREATE INDEX cr_date ON task(crn_date);

CREATE TABLE user (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    reg_date   TIMESTAMP DEFAULT  CURRENT_TIMESTAMP,
    email      VARCHAR(30) UNIQUE NOT NULL,
    user_name  VARCHAR(30) NOT NULL,
    password   VARCHAR(40) NOT NULL
);

CREATE INDEX r_d ON user(reg_date);
CREATE UNIQUE INDEX mail ON user(email);
CREATE INDEX u_name ON user(user_name);

