-- Exam sessions table
CREATE TABLE examSession (
    idExamSession INT PRIMARY KEY AUTO_INCREMENT,
    sessionDate DATE NOT NULL
);

-- Study programs table
CREATE TABLE program (
    idProgram INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Academic years table
CREATE TABLE academicYear (
    idAcademicYear INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(10) NOT NULL UNIQUE
);

-- Semesters table
CREATE TABLE semester (
    idSemester INT PRIMARY KEY AUTO_INCREMENT,
    idAcademicYear INT NOT NULL,
    semesterName VARCHAR(10) NOT NULL,
    FOREIGN KEY (idAcademicYear) REFERENCES academicYear(idAcademicYear) ON DELETE CASCADE
);

-- Subjects table
CREATE TABLE subject (
    idSubject INT PRIMARY KEY AUTO_INCREMENT,
    subjectCode VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL
);

-- Subject details per semester and year table
CREATE TABLE subjectDetailSemesterYear (
    idDetail INT PRIMARY KEY AUTO_INCREMENT,
    idSubject INT NOT NULL,
    idSemester INT NOT NULL,
    credits INT NOT NULL CHECK (credits > 0),
    FOREIGN KEY (idSubject) REFERENCES subject(idSubject) ON DELETE CASCADE,
    FOREIGN KEY (idSemester) REFERENCES semester(idSemester) ON DELETE CASCADE,
    UNIQUE (idSubject, idSemester)
);

-- Students table
CREATE TABLE student (
    idStudent INT PRIMARY KEY AUTO_INCREMENT,
    lastName VARCHAR(100) NOT NULL,
    firstNames VARCHAR(150) NOT NULL,
    birthDate DATE NOT NULL,
    registrationNumber VARCHAR(50) NOT NULL UNIQUE,
    promotion VARCHAR(10)
);

-- Student details per year and semester table
CREATE TABLE studentDetail (
    idDetail INT PRIMARY KEY AUTO_INCREMENT,
    idStudent INT NOT NULL,
    idAcademicYear INT NOT NULL,
    idSemester INT NOT NULL,
    detailDate DATE NOT NULL,
    FOREIGN KEY (idStudent) REFERENCES student(idStudent) ON DELETE CASCADE,
    FOREIGN KEY (idAcademicYear) REFERENCES academicYear(idAcademicYear) ON DELETE CASCADE,
    FOREIGN KEY (idSemester) REFERENCES semester(idSemester) ON DELETE CASCADE,
    UNIQUE (idStudent, idAcademicYear, idSemester)
);

-- Student grades table
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
    FOREIGN KEY (idExamSession) REFERENCES examSession(idExamSession) ON DELETE CASCADE,
    UNIQUE (idStudent, idSubject, idExamSession)
);

INSERT INTO student (lastName, firstNames, birthDate, registrationNumber, promotion)
VALUES 
('Smith', 'John', '2000-05-12', 'STU001', '2025'),
('Doe', 'Jane', '1999-11-03', 'STU002', '2025'),
('Brown', 'Michael', '2001-07-21', 'STU003', '2025'),
('Johnson', 'Emily', '2000-02-14', 'STU004', '2025'),
('Davis', 'Chris', '1998-09-30', 'STU005', '2025');

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(50) NOT NULL, 
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, role)
VALUES ('admin', '1234', 'admin');

-- vue 
-- trouver les matières d'un semestre
CREATE OR REPLACE VIEW semesterSubjects AS
SELECT 
    sds.idSemester,
    sds.idSubject,
    sub.name AS subjectName,
    sds.credits
FROM subjectDetailSemesterYear sds
JOIN subject sub ON sub.idSubject = sds.idSubject;

-- trouver les notes d'un élève sur ce semestre
SELECT 
    ss.idSubject,
    ss.subjectName,
    ss.credits,
    COALESCE(sg.grade, 0) AS grade
FROM semesterSubjects ss
LEFT JOIN studentGrade sg 
    ON sg.idSubject = ss.idSubject
    AND sg.idSemester = ss.idSemester
    AND sg.idStudent = 1      
WHERE ss.idSemester = 1;

-- trouver la moyenne sur un semestre
SELECT 
    SUM(COALESCE(sg.grade, 0) * ss.credits) / SUM(ss.credits) AS moyenne_semestre
FROM semesterSubjects ss
LEFT JOIN studentGrade sg 
    ON sg.idSubject = ss.idSubject
    AND sg.idSemester = ss.idSemester
    AND sg.idStudent = 1       
WHERE ss.idSemester = 1;       

