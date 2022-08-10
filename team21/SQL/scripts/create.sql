DROP TABLE Availabilities;
DROP TABLE schlSubjects;
DROP TABLE Tutors;
DROP TABLE k_12;
DROP TABLE University;
DROP TABLE NeedHelp;
DROP TABLE CanTeach;
DROP TABLE Reports;
DROP TABLE WriteReport;
DROP TABLE ReceiveReport;
DROP TABLE Courses;
DROP TABLE Topics;
DROP TABLE Assignment;
DROP TABLE Give;
DROP TABLE Has;

CREATE TABLE Availabilities
(
    STS int PRIMARY KEY,
    MeetTimes char(100),
    StudentID int,
    UniStudentID int,
    TID int
);

grant select on Availabilities to public;

CREATE TABLE schlSubjects
(
    SubjectName char(20) PRIMARY KEY
);

grant select on schlSubjects to public;

CREATE TABLE Tutors
(
    TutorID int PRIMARY KEY,
    TutorName char(50),
    tAge int,
    Ratings int,
    SubjectName char(20) NOT NULL,
    STS int,
    FOREIGN KEY (STS) REFERENCES Availabilities,
    FOREIGN KEY (SubjectName) REFERENCES schlSubjects
);

grant select on Tutors to public;

CREATE TABLE k_12
(
    StudentID int PRIMARY KEY,
    StudentName char(50),
    Age int NOT NULL,
    Exams int,
    UniApplication int,
    SAT int,
    STS int NOT NULL,
    TutorID int NOT NULL,
    FOREIGN KEY (STS) REFERENCES Availabilities,
    FOREIGN KEY (TutorID) REFERENCES Tutors
);
grant select on k_12 to public;


CREATE TABLE University
(
    StudentID int PRIMARY KEY,
    StudentName char(50),
    Age int,
    LSAT int,
    MCAT int,
    BAR int,
    STS int,
    TutorID int,
    FOREIGN KEY (STS) REFERENCES Availabilities,
    FOREIGN KEY(TutorID) REFERENCES Tutors
);
grant select on University to public;

CREATE TABLE NeedHelp
(
    StudentID int,
    UniStudentID int,
    SubjectName char(20),
    FOREIGN KEY(StudentID) REFERENCES k_12,
    FOREIGN KEY(UniStudentID) REFERENCES University
);
grant select on NeedHelp to public;

CREATE TABLE CanTeach
(
    SubjectName char(20),
    TutorID int,
    PRIMARY KEY (SubjectName, TutorID),
    FOREIGN KEY (SubjectName) REFERENCES schlSubjects,
    FOREIGN KEY (TutorID) REFERENCES Tutors
);
grant select on CanTeach to public;

CREATE TABLE Reports
(
    ReportNumber int PRIMARY KEY,
    ReportDesc char(1000)
);
grant select on Reports to public;

CREATE TABLE WriteReport
(
    ReportNumber int,
    TutorID int,
    PRIMARY KEY(ReportNumber, TutorID),
    FOREIGN KEY(ReportNumber) REFERENCES Reports,
    FOREIGN KEY(TutorID) REFERENCES Tutors
);
grant select on WriteReport to public;

CREATE TABLE ReceiveReport
(
    ReportNumber int,
    StudentID int,
    UniStudentID int,
    PRIMARY KEY(ReportNumber),
    FOREIGN KEY(ReportNumber) REFERENCES Reports,
    FOREIGN KEY(StudentID) REFERENCES k_12,
    FOREIGN KEY(UniStudentID) REFERENCES University
);
grant select on ReceiveReport to public;

CREATE TABLE Courses
(
    CourseName char(20) PRIMARY KEY,
    GradeLevel int,
    SubjectName char(20) NOT NULL,
    FOREIGN KEY (SubjectName) REFERENCES schlSubjects
);
grant select on Courses to public;

CREATE TABLE Topics
(
    TopicName char(20),
    CourseName char(20),
    Difficult int,
    PRIMARY KEY(TopicName, CourseName),
    FOREIGN KEY(CourseName) REFERENCES Courses
);
grant select on Topics to public;

CREATE TABLE Assignment
(
    AssignNumber int PRIMARY KEY,
    AssignDescription char(1000),
    Mark int
);
grant select on Assignment to public;

CREATE TABLE Give
(
    AssignNumber int,
    StudentID int,
    UniStudentID int,
    PRIMARY KEY(AssignNumber),
    FOREIGN KEY(AssignNumber) REFERENCES Assignment,
    FOREIGN KEY(StudentID) REFERENCES k_12,
    FOREIGN KEY(UniStudentID) REFERENCES University
);
grant select on Give to public;

CREATE TABLE Has
(
    TopicName char(20),
    CourseName char(20),
    TutorID integer,
    AssignNumber integer,
    FOREIGN KEY(TopicName, CourseName) REFERENCES Topics,
    FOREIGN KEY(TutorID) REFERENCES Tutors,
    FOREIGN KEY(AssignNumber) REFERENCES Assignment
);
grant select on Has to public;

INSERT INTO Availabilities
VALUES(112312, 'T/TH', 101, NULL, 1);
INSERT INTO Availabilities
VALUES(212312, 'M/W/F', 201, NULL, 1);
INSERT INTO Availabilities
VALUES(184935, 'T/TH', 301, NULL, 4);
INSERT INTO Availabilities
VALUES(192383, 'M/T/W', 401, NULL, 5);
INSERT INTO Availabilities
VALUES(123944, 'M/TH/F', 501, NULL, 6);
INSERT INTO Availabilities
VALUES(129488, 'M/TH/SAT', NULL, 100, 2);
INSERT INTO Availabilities
VALUES(417475, 'M/W/F', NULL, 200, 3);
INSERT INTO Availabilities
VALUES(183929, 'M/W/F', NULL, 300, 7);
INSERT INTO Availabilities
VALUES(123948, 'M/TH/F', NULL, 400, 8);
INSERT INTO Availabilities
VALUES(128384, 'M/TH/SAT', NULL, 500, 7);

INSERT INTO schlSubjects
VALUES('Mathematics');
INSERT INTO schlSubjects
VALUES('Physics');
INSERT INTO schlSubjects
VALUES('English');
INSERT INTO schlSubjects
VALUES('Computer Science');
INSERT INTO schlSubjects
VALUES('Biology');

INSERT INTO Tutors
VALUES(1, 'Leena', 43, 4, 'Mathematics', 112312);
INSERT INTO Tutors
VALUES(2, 'Aaliyah', 23, 3, 'Physics', 212312);
INSERT INTO Tutors
VALUES(3, 'Mike', 28, 4, 'Mathematics', 184935);
INSERT INTO Tutors
VALUES(4, 'Tony', 26, 4, 'English', 192383);
INSERT INTO Tutors
VALUES(5, 'Josh', 30, 5, 'Computer Science', 123944);
INSERT INTO Tutors
VALUES(6, 'Sophia', 24, 3, 'Physics', 129488);
INSERT INTO Tutors
VALUES(7, 'Marnie', 28, 4, 'Computer Science', 183929);
INSERT INTO Tutors
VALUES(8, 'Lewis', 17, 1, 'Biology', 123948);

INSERT INTO k_12
VALUES(101, 'Tim', 18, 1, 0, 0, 112312, 1);
INSERT INTO k_12
VALUES(201, 'Jimmy', 16, 0, 1, 1, 212312, 1);
INSERT INTO k_12
VALUES(301, 'Bob', 14, 0, 0, 0, 184935, 4);
INSERT INTO k_12
VALUES(401, 'Jones', 12, 0, 0, 0, 192383, 5);
INSERT INTO k_12
VALUES(501, 'Johnny', 18, 1, 1, 1, 123944, 6);

INSERT INTO University
VALUES(100, 'Sean', 20, 1, 0, 0, 129488, 2);
INSERT INTO University
VALUES(200, 'Isaac', 21, 0, 1, 0, 417475, 3);
INSERT INTO University
VALUES(300, 'Hargreeves', 22, 0, 0, 1, 183929, 7);
INSERT INTO University
VALUES(400, 'Bobby', 21, 0, 0, 0, 123948, 8);
INSERT INTO University
VALUES(500, 'Billy', 26, 0, 0, 1, 128384, 7);

INSERT INTO NeedHelp
VALUES(101, NULL, 'Mathematics');
INSERT INTO NeedHelp
VALUES(201, NULL, 'Physics');
INSERT INTO NeedHelp
VALUES(301, NULL, 'Physics');
INSERT INTO NeedHelp
VALUES(401, NULL, 'English');
INSERT INTO NeedHelp
VALUES(501, NULL, 'English');
INSERT INTO NeedHelp
VALUES(NULL, 100, 'Mathematics');
INSERT INTO NeedHelp
VALUES(NULL, 200, 'Computer Science');
INSERT INTO NeedHelp
VALUES(NULL, 300, 'Biology');
INSERT INTO NeedHelp
VALUES(NULL, 400, 'Biology');
INSERT INTO NeedHelp
VALUES(NULL, 500, 'Mathematics');

INSERT INTO CanTeach
VALUES('Mathematics', 1);
INSERT INTO CanTeach
VALUES('Physics', 1);
INSERT INTO CanTeach
VALUES('English', 1);
INSERT INTO CanTeach
VALUES('Computer Science', 1);
INSERT INTO CanTeach
VALUES('Biology', 1);
INSERT INTO CanTeach
VALUES('Physics', 2);
INSERT INTO CanTeach
VALUES('English', 3);
INSERT INTO CanTeach
VALUES('Computer Science', 4);
INSERT INTO CanTeach
VALUES('Computer Science', 5);
INSERT INTO CanTeach
VALUES('English', 6);
INSERT INTO CanTeach
VALUES('Mathematics', 7);
INSERT INTO CanTeach
VALUES('Biology', 8);

INSERT INTO Reports
VALUES(324234, 'Good');
INSERT INTO Reports
VALUES(123123, 'Bad');
INSERT INTO Reports
VALUES(123213, 'R u even human');
INSERT INTO Reports
VALUES(956765, 'Stupid');
INSERT INTO Reports
VALUES(456456, 'Genius');

INSERT INTO WriteReport
VALUES(324234, 1);
INSERT INTO WriteReport
VALUES(123123, 2);
INSERT INTO WriteReport
VALUES(123213, 3);
INSERT INTO WriteReport
VALUES(956765, 4);
INSERT INTO WriteReport
VALUES(456456, 5);

INSERT INTO ReceiveReport
VALUES(324234, NULL, 100);
INSERT INTO ReceiveReport
VALUES(123123, 201, NULL);
INSERT INTO ReceiveReport
VALUES(123213, NULL, 200);
INSERT INTO ReceiveReport
VALUES(956765, NULL, 300);
INSERT INTO ReceiveReport
VALUES(456456, 401, NULL);

INSERT INTO Courses
VALUES('Pre-Calc 11', 11, 'Mathematics');
INSERT INTO Courses
VALUES('Biology 12', 12, 'Biology');
INSERT INTO Courses
VALUES('MATH 220', 14, 'Mathematics');
INSERT INTO Courses
VALUES('Language Arts 9', 9, 'English');
INSERT INTO Courses
VALUES('BIOL 111', 13, 'Biology');

INSERT INTO Topics
VALUES('Trigonometry', 'Pre-Calc 11', 7);
INSERT INTO Topics
VALUES('Cells', 'Biology 12', 3);
INSERT INTO Topics
VALUES('Sequence Proofs', 'MATH 220', 10);
INSERT INTO Topics
VALUES('Hamlet', 'Language Arts 9', 1);
INSERT INTO Topics
VALUES('Natural Disasters', 'BIOL 111', 3);

INSERT INTO Assignment
VALUES(2, 'Trigonometry Identity Practice', 90);
INSERT INTO Assignment
VALUES(12, 'Worksheet on cellular composition', 78);
INSERT INTO Assignment
VALUES(5, 'Worksheet on causes of natural disasters', 82);
INSERT INTO Assignment
VALUES(3, 'Essay on Hamlets disasters', 95);
INSERT INTO Assignment
VALUES(6, 'Sequential proof problem set', 60);

INSERT INTO Give
VALUES(2, NULL, 100);
INSERT INTO Give
VALUES(12, 201, NULL);
INSERT INTO Give
VALUES(5, NULL, 200);
INSERT INTO Give
VALUES(3, NULL, 300);
INSERT INTO Give
VALUES(6, NULL, 400);

INSERT INTO Has
VALUES('Trigonometry', 'Pre-Calc 11', 1, 2);
INSERT INTO Has
VALUES('Cells', 'Biology 12', 2, 12);
INSERT INTO Has
VALUES('Natural Disasters', 'BIOL 111', 3, 5);
INSERT INTO Has
VALUES('Hamlet', 'Language Arts 9', 4, 3);
INSERT INTO Has
VALUES('Sequence Proofs', 'MATH 220', 5, 6);
