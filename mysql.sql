-- PHP interview exercise: org chart (MySQL 8.0+)
-- Option B. Skip this file if you are using SQLite (sqlite.sql).
-- Usage: docker exec -i php-test mysql < mysql.sql

CREATE DATABASE IF NOT EXISTS php_test;
USE php_test;

DROP TABLE IF EXISTS employees;

CREATE TABLE employees (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name  VARCHAR(50)  NOT NULL,
  last_name   VARCHAR(50)  NOT NULL,
  title       VARCHAR(100) NOT NULL,
  timezone    VARCHAR(64)  NOT NULL,
  active      TINYINT(1)   NOT NULL DEFAULT 1,
  manager_id  INT UNSIGNED NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8mb4;

INSERT INTO employees (id, first_name, last_name, title, timezone, active, manager_id) VALUES
  (1001, 'Maria',  'Chen',    'CEO',                 'America/New_York',    1, NULL),
  (1002, 'David',  'Okafor',  'CTO',                 'America/New_York',    1, 1001),
  (1003, 'Sarah',  'Kim',     'CFO',                 'America/New_York',    1, 1001),
  (1004, 'James',  'Rivera',  'VP of Sales',         'America/New_York',    1, 1001),
  (1005, 'Priya',  'Patel',   'Engineering Manager', 'America/Chicago',     1, 1002),
  (1006, 'Tom',    'Becker',  'Engineering Manager', 'America/Denver',      1, 1002),
  (1007, 'Lisa',   'Nguyen',  'Controller',          'America/New_York',    1, 1003),
  (1008, 'Marcus', 'Hill',    'Sales Manager',       'America/New_York',    1, 1004),
  (1009, 'Ana',    'Torres',  'Senior Engineer',     'America/Chicago',     1, 1005),
  (1010, 'Ben',    'Walsh',   'Engineer',            'America/Los_Angeles', 1, 1005),
  (1011, 'Chloe',  'Martin',  'Engineer',            'America/Denver',      1, 1006),
  (1012, 'Daniel', 'Reyes',   'Engineer',            'America/Phoenix',     1, 1006),
  (1013, 'Emma',   'Scott',   'Accountant',          'America/New_York',    1, 1007),
  (1014, 'Frank',  'Lopez',   'Account Executive',   'America/New_York',    1, 1008),
  (1015, 'Grace',  'Liu',     'Account Executive',   'America/Los_Angeles', 0, 1008),
  (1016, 'Henry',  'Adams',   'Junior Engineer',     'America/Chicago',     0, 1009),
  (1017, 'Isabel', 'Cruz',    'Engineering Intern',  'America/Chicago',     1, 1016),
  (1018, 'Jack',   'Murphy',  'Engineering Intern',  'America/New_York',    1, 1016);
