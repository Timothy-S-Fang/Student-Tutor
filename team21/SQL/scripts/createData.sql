
CREATE TABLE k_12 (
    StudentID int PRIMARY KEY,
    Age int NOT NULL,
    StudentName char(50),
    Exams char(30),
    UniApplication bit,
    SAT bit,
    STS int NOT NULL,
    TutorID int NOT NULL,
    FOREIGN KEY (STS) REFERENCES Availabilities,
    FOREIGN KEY (TutorID) REFERENCES Tutors
);

CREATE TABLE University (
    StudentID int PRIMARY KEY,
    StudentName char(50),
    Age int,
    LSAT bit,
    MCAT bit,
    BAR bit,
    STS int,
    TutorID int,
    FOREIGN KEY (STS) REFERENCES Availabilities,
    FOREIGN KEY(TutorID) REFERENCES Tutors
);

CREATE TABLE Availabilities (
    STS int PRIMARY KEY,
    MeetTimes char(100),
    StudentID int NOT NULL,
    TID int NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES k_12,
    FOREIGN KEY (StudentID) REFERENCES University,
    FOREIGN KEY (TID) REFERENCES Tutors
);

CREATE TABLE Tutors (
    TutorID int PRIMARY KEY,
    StudentName char(50),
    tAge int,
    Ratings int,
    SubjectName char(50) NOT NULL,
    FOREIGN KEY(STS) REFERENCES Availabilities,
    FOREIGN KEY(SubjectName) REFERENCES Subject
);

CREATE TABLE NeedHelp (
    StudentID integer,
    SubjectName Char(50)
    PRIMARY KEY (StudentID, SubjectName),
    FOREIGN KEY(StudentID) REFERENCES k_12,
    FOREIGN KEY(StudentID) REFERENCES University
);


CREATE TABLE CanTeach(
    SubjectName char(50),
    TutorID int,
    PRIMARY KEY (SubjectName, TutorID),
    FOREIGN KEY (SubjectName) REFERENCES Subject,
    FOREIGN KEY (TutorID) REFERENCES Tutor
)

CREATE TABLE Reports(
    ReportNumber int PRIMARY KEY,
    ReportDesc char(1000)
)

CREATE TABLE WriteReport(
    ReportNumber int,
    TutorID int,
    PRIMARY KEY(ReportNumber, TutorID),
    FOREIGN KEY(ReportNumber) REFERENCES Reports,
    FOREIGN KEY(TutorID) REFERENCES Tutors
)

CREATE TABLE ReceiveReport(
    ReportNumber int,
    StudentID int,
    PRIMARY KEY(ReportNumber, StudentID),
    FOREIGN KEY(ReportNumber) REFERENCES Reports,
    FOREIGN KEY(StudentID) REFERENCES Student
)

CREATE TABLE Subjects(
    SubjectName char(20) PRIMARY KEY
)

CREATE TABLE Courses(
    CourseName char(50) PRIMARY KEY,
    GradeLevel int,
    SubjectName char(80) NOT NULL,
    FOREIGN KEY(SubjectName) REFERENCES Subjects
)

CREATE TABLE Topics(
    TopicName char(20),
    CourseName char(20),
    Difficult int,
    PRIMARY KEY(TopicName, CourseName),
    FOREIGN KEY(TopicName) REFERENCES Topics
)

CREATE TABLE Assignment(
    AssignNumber int PRIMARY KEY,
    AssignDescription char(1000),
    Mark int
)

CREATE TABLE Give(
    AssignNumber int,
    StudentID int,
    PRIMARY KEY(AssignNumber, StudentID),
    FOREIGN KEY(AssignNumber) REFERENCES Assignment,
    FOREIGN KEY(StudentID) REFERENCES K_12,
    FOREIGN KEY(StudentID) REFERENCES University
)

CREATE TABLE Has(
    TopicName char(20),
    TutorID integer,
    AssignNumber integer,
    PRIMARY KEY(TopicName, TutorID, AssignNumber),
    FOREIGN KEY(TopicName, CourseName) REFERENCES Topic,
    FOREIGN KEY(TutorID) REFERENCES Tutor,
    FOREIGN KEY(AssignNumber) REFERENCES Assignment
)

INSERT INTO k_12 VALUES(35694356,"Tim", 18, 1,	0, 0, 112312, 1)
INSERT INTO k_12 VALUES(24357532,"Jimmy", 16, 0, 1, 1, 2, 1)
INSERT INTO k_12 VALUES(13294586,"Bob,", 14, 0, 0,	0, 184935, 4)
INSERT INTO k_12 VALUES(19283844,"Jones", 12,	0, 0, 0, 192383, 5)
INSERT INTO k_12 VALUES(10293948,"Johnny,", 18, 1,	1, 1, 123944, 6)

INSERT INTO University VALUES(23423672,	Sean,	20,	1,	0,	0,	129488,	2)
INSERT INTO University VALUES(85566722,	Isaac,	21,	0,	1,	0,	417475,	3)
INSERT INTO University VALUES(18553137,	Hargreeves,	22,	0,	0,	1,	183929,	7)
INSERT INTO University VALUES(32904858,	Bobby,	21,	0,	0,	0,	123948,	8)
INSERT INTO University VALUES(1239485,	Billy,	26,	0,	0,	1,	128384,	7)

INSERT INTO Availabilities VALUES(112312,	T/TH,	35694356,	1)
INSERT INTO Availabilities VALUES(2,	M/W/F,	24357532,	1)
INSERT INTO Availabilities VALUES(184935,	T/TH,	13294586,	4)
INSERT INTO Availabilities VALUES(192383,	M/T/W,	19283844,	5)
INSERT INTO Availabilities VALUES(123944, M/TH/F,	10293948,	6)

INSERT INTO tutors VALUES(1, Leena,	43, 4)
INSERT INTO tutors VALUES(2, Aaliyah, 23, 3)
INSERT INTO tutors VALUES(3, Mike, 28, 4)
INSERT INTO tutors VALUES(4, Tony, 26, 4)
INSERT INTO tutors VALUES(5, Josh, 30, 5)

INSERT INTO NeedHelp VALUES(35694356, Mathematics)
INSERT INTO NeedHelp VALUES(24357532, Physics)
INSERT INTO NeedHelp VALUES(13294586, Physics)
INSERT INTO NeedHelp VALUES(19283844, English)
INSERT INTO NeedHelp VALUES(10293948, English)

