-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS e_learning_db;

-- Sử dụng cơ sở dữ liệu
USE e_learning_db;

-- Tạo bảng Users
CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    invite_code VARCHAR(50),
    avatar VARCHAR(255),
    gender ENUM('male', 'female', 'other'),
    dob DATE,
    address VARCHAR(255),
    school VARCHAR(255),
    class VARCHAR(50),
    test_class VARCHAR(50),
    grade INT,
    lastLoginAt TIMESTAMP,
    google_id VARCHAR(255)
);

-- Tạo bảng Subjects
CREATE TABLE IF NOT EXISTS Subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    grade INT
);

-- Tạo bảng Chapters
CREATE TABLE IF NOT EXISTS Chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    subject_id INT,
    FOREIGN KEY (subject_id) REFERENCES Subjects(id)
);

-- Tạo bảng Lessons
CREATE TABLE IF NOT EXISTS Lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    chap_id INT,
    content TEXT,
    view_count INT DEFAULT 0,
    type VARCHAR(50),
    subject_id INT,
    likes INT DEFAULT 0,
    FOREIGN KEY (chap_id) REFERENCES Chapters(id),
    FOREIGN KEY (subject_id) REFERENCES Subjects(id)
);

-- Tạo bảng Questions
CREATE TABLE IF NOT EXISTS Questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer_1 TEXT,
    answer_2 TEXT,
    answer_3 TEXT,
    answer_4 TEXT,
    answer_correct INT,
    answer_detail TEXT,
       level INT,
    subject_id INT,
    chaper_id INT,
 
    FOREIGN KEY (subject_id) REFERENCES Subjects(id),
    FOREIGN KEY (lesson_id) REFERENCES Lessons(id)
);

-- Tạo bảng Exams
CREATE TABLE IF NOT EXISTS Exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    time INT,
    questions TEXT,
    join_count INT DEFAULT 0,
    complete_count INT DEFAULT 0,
    subject_id INT,
    FOREIGN KEY (subject_id) REFERENCES Subjects(id)
);

-- Tạo bảng Practices
CREATE TABLE IF NOT EXISTS Practices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    questions TEXT,
    slug VARCHAR(255) UNIQUE NOT NULL,
     subject_id INT,
    chaper_id INT,
 
    FOREIGN KEY (subject_id) REFERENCES Subjects(id),
    FOREIGN KEY (lesson_id) REFERENCES Lessons(id)
);

-- Tạo bảng Topics
CREATE TABLE IF NOT EXISTS Topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT,
    author INT
);

-- Tạo bảng Topic Comments
CREATE TABLE IF NOT EXISTS Topic_Comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT,
    author INT,
    content TEXT,
    likes INT DEFAULT 0,
    FOREIGN KEY (author) REFERENCES users(id) 
    FOREIGN KEY (topic_id) REFERENCES Topics(id)
);

-- Tạo bảng User Targets
CREATE TABLE IF NOT EXISTS User_Targets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    hour_per_day INT,
    target_score INT,
    subject_id INT,
    stage VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (subject_id) REFERENCES Subjects(id)
);

-- Tạo bảng Arenas
CREATE TABLE IF NOT EXISTS Arenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    author INT,
    users TEXT,
    max_users INT,
    time INT,
    questions TEXT,
    start_at DATETIME,
    type VARCHAR(50),
    password VARCHAR(255)
);

-- Tạo bảng Histories
CREATE TABLE IF NOT EXISTS Histories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    model VARCHAR(255),
    foreign_id INT,
    result VARCHAR(50),
    note TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(id)
);

-- Tạo bảng Statistics
CREATE TABLE IF NOT EXISTS Statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    time_online INT,
    exams VARCHAR(1000),
    day_stats TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id)
);
