USE student;

CREATE TABLE examSession (
    idExamSession INT PRIMARY KEY AUTO_INCREMENT,
    sessionDate DATE NOT NULL
);

CREATE TABLE program (
    idProgram INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE academicYear (
    idAcademicYear INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(10) NOT NULL UNIQUE
);

CREATE TABLE semester (
    idSemester INT PRIMARY KEY AUTO_INCREMENT,
    idAcademicYear INT NOT NULL,
    semesterName VARCHAR(10) NOT NULL,
    FOREIGN KEY (idAcademicYear) REFERENCES academicYear(idAcademicYear) ON DELETE CASCADE
);

CREATE TABLE subject (
    idSubject INT PRIMARY KEY AUTO_INCREMENT,
    subjectCode VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE subjectDetailSemesterYear (
    idDetail INT PRIMARY KEY AUTO_INCREMENT,
    idSubject INT NOT NULL,
    idSemester INT NOT NULL,
    credits INT NOT NULL CHECK (credits > 0),
    FOREIGN KEY (idSubject) REFERENCES subject(idSubject) ON DELETE CASCADE,
    FOREIGN KEY (idSemester) REFERENCES semester(idSemester) ON DELETE CASCADE
);

CREATE TABLE student (
    idStudent INT PRIMARY KEY AUTO_INCREMENT,
    lastName VARCHAR(100) NOT NULL,
    firstNames VARCHAR(150) NOT NULL,
    birthDate DATE NOT NULL,
    registrationNumber VARCHAR(50) NOT NULL UNIQUE,
    promotion VARCHAR(10)
);

CREATE TABLE studentDetail (
    idDetail INT PRIMARY KEY AUTO_INCREMENT,
    idStudent INT NOT NULL,
    idAcademicYear INT NOT NULL,
    idSemester INT NOT NULL,
    detailDate DATE NOT NULL,
    FOREIGN KEY (idStudent) REFERENCES student(idStudent) ON DELETE CASCADE,
    FOREIGN KEY (idAcademicYear) REFERENCES academicYear(idAcademicYear) ON DELETE CASCADE,
    FOREIGN KEY (idSemester) REFERENCES semester(idSemester) ON DELETE CASCADE
);

CREATE TABLE studentGrade (
    idGrade INT PRIMARY KEY AUTO_INCREMENT,
    idStudent INT NOT NULL,
    idSubject INT NOT NULL,
    idSemester INT NOT NULL,
    idExamSession INT NOT NULL,
    grade DECIMAL(5,2) CHECK (grade >= 0 AND grade <= 20),
    gradeDate DATE NOT NULL,
    FOREIGN KEY (idStudent) REFERENCES student(idStudent) ON DELETE CASCADE,
    FOREIGN KEY (idSubject) REFERENCES subject(idSubject) ON DELETE CASCADE,
    FOREIGN KEY (idSemester) REFERENCES semester(idSemester) ON DELETE CASCADE,
    FOREIGN KEY (idExamSession) REFERENCES examSession(idExamSession) ON DELETE CASCADE
);

-- Subject result types table
-- CREATE TABLE subjectResultType (
--     idSubjectResultType INT PRIMARY KEY AUTO_INCREMENT,
--     name VARCHAR(20) NOT NULL UNIQUE,
--     minValue INT NOT NULL,
--     maxValue INT NOT NULL,
--     CHECK (minValue <= maxValue)
-- );

-- -- Students averages table
-- CREATE TABLE studentAverage (
--     idAverage INT PRIMARY KEY AUTO_INCREMENT,
--     idStudent INT NOT NULL,
--     averageValue DECIMAL(5,2) CHECK (averageValue >= 0 AND averageValue <= 20),
--     idExamSession INT NOT NULL,
--     FOREIGN KEY (idStudent) REFERENCES student(idStudent) ON DELETE CASCADE,
--     FOREIGN KEY (idExamSession) REFERENCES examSession(idExamSession) ON DELETE CASCADE,
--     UNIQUE (idStudent, idExamSession)
-- );

-- ==== INSERT ====

-- Academic Years
INSERT INTO academicYear (name) VALUES
('L1'),
('L2');

-- Semesters (avec lien correct vers idAcademicYear)
INSERT INTO semester (idAcademicYear, semesterName) VALUES
(1, 'S1'),
(1, 'S2'),
(2, 'S3'),
(2, 'S4');

-- Students
INSERT INTO student (lastName, firstNames, birthDate, registrationNumber, promotion)
VALUES 
('Smith', 'John', '2000-05-12', 'ETU001', '2025'),
('Doe', 'Jane', '1999-11-03', 'ETU002', '2025'),
('Brown', 'Michael', '2001-07-21', 'ETU003', '2025'),
('Johnson', 'Emily', '2000-02-14', 'ETU004', '2025'),
('Davis', 'Chris', '1998-09-30', 'ETU005', '2025');

-- Subjects Semestre 1
INSERT INTO subject (subjectCode, name) VALUES
('INF101', 'Programmation procédurale'),
('INF104', 'HTML et Introduction au Web'),
('INF107', 'Informatique de Base'),
('MTH101', 'Arithmétique et nombres'),
('MTH102', 'Analyse mathématique'),
('ORG101', 'Techniques de communication');

-- Subjects Semestre 2
INSERT INTO subject (subjectCode, name) VALUES
('INF102', 'Bases de données relationnelles'),
('INF103', "Bases de l'administration système"),
('INF105', 'Maintenance matériel et logiciel'),
('INF106', 'Compléments de programmation'),
('MTH103', 'Calcul Vectoriel et Matriciel'),
('MTH105', 'Probabilité et Statistique');

-- Subjects Semestre 3
INSERT INTO subject (subjectCode, name) VALUES
('INF201', 'Programmation orientée objet'),
('INF202', 'Bases de données objets'),
('INF203', 'Programmation système'),
('INF208', 'Réseaux informatiques'),
('MTH201', 'Méthodes numériques'),
('ORG201', 'Bases de gestion');

-- Link subjects to semesters (credits example)
INSERT INTO subjectDetailSemesterYear (idSubject, idSemester, credits) VALUES
-- S1
(1, 1, 3), (2, 1, 3), (3, 1, 3), (4, 1, 3), (5, 1, 3), (6, 1, 3),
-- S2
(7, 2, 3), (8, 2, 3), (9, 2, 3), (10, 2, 3), (11, 2, 3), (12, 2, 3),
-- S3
(13, 3, 3), (14, 3, 3), (15, 3, 3), (16, 3, 3), (17, 3, 3), (18, 3, 3);

-- Exam Sessions
INSERT INTO examSession (sessionDate) VALUES
('2025-01-15'),
('2025-06-20'),
('2025-09-10');

-- StudentDetail (linking students to academic year and semester)
-- L1 => S1, S2 ; L2 => S3, S4
INSERT INTO studentDetail (idStudent, idAcademicYear, idSemester, detailDate) VALUES
-- Student 1 (ETU001)
(1, 1, 1, '2024-09-01'),
(1, 1, 2, '2025-01-10'),
(1, 2, 3, '2025-09-01'),
(1, 2, 4, '2026-01-10'),
-- Student 2 (ETU002)
(2, 1, 1, '2024-09-01'),
(2, 1, 2, '2025-01-10'),
(2, 2, 3, '2025-09-01'),
(2, 2, 4, '2026-01-10'),
-- Student 3 (ETU003)
(3, 1, 1, '2024-09-01'),
(3, 1, 2, '2025-01-10'),
(3, 2, 3, '2025-09-01'),
(3, 2, 4, '2026-01-10'),
-- Student 4 (ETU004)
(4, 1, 1, '2024-09-01'),
(4, 1, 2, '2025-01-10'),
(4, 2, 3, '2025-09-01'),
(4, 2, 4, '2026-01-10'),
-- Student 5 (ETU005)
(5, 1, 1, '2024-09-01'),
(5, 1, 2, '2025-01-10'),
(5, 2, 3, '2025-09-01'),
(5, 2, 4, '2026-01-10');

-- Example grades only for S1, S2, S3 (S4 to be added later)
INSERT INTO studentGrade (idStudent, idSubject, idSemester, idExamSession, grade, gradeDate) VALUES
-- S1
(1, 1, 1, 1, 15.5, '2025-01-15'),
(1, 2, 1, 1, 12.0, '2025-01-15'),
(2, 1, 1, 1, 14.0, '2025-01-15'),
(3, 3, 1, 1, 16.0, '2025-01-15'),
(4, 4, 1, 1, 11.5, '2025-01-15'),
(5, 5, 1, 1, 13.5, '2025-01-15'),
-- S2
(1, 7, 2, 2, 14.5, '2025-06-20'),
(2, 8, 2, 2, 13.0, '2025-06-20'),
(3, 9, 2, 2, 15.0, '2025-06-20'),
-- S3
(1, 13, 3, 3, 16.0, '2025-09-10'),
(2, 14, 3, 3, 14.5, '2025-09-10'),
(3, 15, 3, 3, 15.5, '2025-09-10');