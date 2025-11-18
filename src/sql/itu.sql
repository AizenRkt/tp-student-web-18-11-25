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

-- Subject result types table
-- CREATE TABLE subjectResultType (
--     idSubjectResultType INT PRIMARY KEY AUTO_INCREMENT,
--     name VARCHAR(20) NOT NULL UNIQUE,
--     minValue INT NOT NULL,
--     maxValue INT NOT NULL,
--     CHECK (minValue <= maxValue)
-- );

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

CREATE TABLE moyenne (
    id_moyenne INT PRIMARY KEY,
    id_etudiant INT,
    valeur DECIMAL(5,2),
    id_session INT,
    FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant),
    FOREIGN KEY (id_session) REFERENCES session_examen(id_session_examen)
);
