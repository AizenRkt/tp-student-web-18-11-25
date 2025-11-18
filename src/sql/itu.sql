CREATE TABLE session_examen (
    id_session_examen INT PRIMARY KEY,
    date_session DATE
);

CREATE TABLE parcours (
    id_parcours
    nom
)

CREATE TABLE annee (
    id_annee INT PRIMARY KEY,
    nom VARCHAR(10)
);

CREATE TABLE semestre (
    id_semestre INT PRIMARY KEY,
    id_annee INT,
    nom_semestre VARCHAR(10),
    annee VARCHAR(10),
    FOREIGN KEY (id_annee) REFERENCES annee(id_annee)
);

CREATE TABLE matiere (
    id_matiere INT PRIMARY KEY,
    code_matiere VARCHAR(50),
    nom VARCHAR(100)
);

CREATE TABLE type_resultat_matiere (
    id_type_resultat INT PRIMARY KEY,
    nom VARCHAR(20),
    valeur_min INT,
    valeur_max INT
);

CREATE TABLE matiere_detail_semestre_annee (
    id_detail INT PRIMARY KEY,
    id_matiere INT,
    id_semestre INT,
    credit INT,
    FOREIGN KEY (id_matiere) REFERENCES matiere(id_matiere),
    FOREIGN KEY (id_semestre) REFERENCES semestre(id_semestre)
);

CREATE TABLE 

CREATE TABLE etudiant (
    id_etudiant INT PRIMARY KEY,
    nom VARCHAR(100),
    prenoms VARCHAR(150),
    date_de_naissance DATE,
    num_inscription VARCHAR(50),
    prom VARCHAR(10)
);

CREATE TABLE etudiant_detail (
    id_detail INT PRIMARY KEY,
    id_etudiant INT,
    id_annee INT,
    id_semestre INT,
    date_detail DATE,
    FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant),
    FOREIGN KEY (id_annee) REFERENCES annee(id_annee),
    FOREIGN KEY (id_semestre) REFERENCES semestre(id_semestre)
);

CREATE TABLE etudiant_note (
    id_note INT PRIMARY KEY,
    id_etudiant INT,
    id_matiere INT,
    id_semestre INT,
    id_session INT,
    note DECIMAL(5,2),
    date_note DATE,
    FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant),
    FOREIGN KEY (id_matiere) REFERENCES matiere(id_matiere),
    FOREIGN KEY (id_semestre) REFERENCES semestre(id_semestre),
    FOREIGN KEY (id_session) REFERENCES session_examen(id_session_examen)
);

CREATE TABLE moyenne (
    id_moyenne INT PRIMARY KEY,
    id_etudiant INT,
    valeur DECIMAL(5,2),
    id_session INT,
    FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant),
    FOREIGN KEY (id_session) REFERENCES session_examen(id_session_examen)
);
