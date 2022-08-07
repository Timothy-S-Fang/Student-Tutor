
// foreign key reference doesnt work; try oracle version

CREATE TABLE k_12 (
    StudentID int PRIMARY KEY,
    Age int NOT NULL,
    StudentName char(50),
    Exams char(30),
    UniApplication int,
    SAT int,
    STS int NOT NULL,
    TutorID int NOT NULL,
    FOREIGN KEY (STS) REFERENCES Availabilities(STS),
    FOREIGN KEY (TutorID) REFERENCES Tutors
);
 
CREATE TABLE University (
    StudentID int PRIMARY KEY,
    StudentName char(50),
    Age int,
    LSAT int,
    MCAT int,
    BAR int,
    STS int,
    TutorID int,
    FOREIGN KEY (STS) REFERENCES Availabilities(STS),
    FOREIGN KEY(TutorID) REFERENCES Tutors
);
 
CREATE TABLE Availabilities (
    STS int PRIMARY KEY,
    MeetTimes char(100),
    StudentID int,
    UniStudentID int,
    TID int NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES k_12(StudentID),
    FOREIGN KEY (UniStudentID) REFERENCES University(StudentID),
    FOREIGN KEY (TID) REFERENCES Tutors
);
 
CREATE TABLE Tutors (
    TutorID int PRIMARY KEY,
    StudentName char(50),
    tAge int,
    Ratings int,
    SubjectName char(50) NOT NULL,
    STS int,
    FOREIGN KEY (STS) REFERENCES Availabilities(STS),
    FOREIGN KEY (SubjectName) REFERENCES Subject
);
 
CREATE TABLE NeedHelp (
    StudentID int PRIMARY KEY,
    UniStudentID int,
    SubjectName Char(50),
    FOREIGN KEY(StudentID) REFERENCES k_12,
    FOREIGN KEY(UniStudentID) REFERENCES University
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
    UniStudentID int,
    PRIMARY KEY(ReportNumber, StudentID),
    FOREIGN KEY(ReportNumber) REFERENCES Reports,
    FOREIGN KEY(StudentID) REFERENCES k_12(StudentID),
    FOREIGN KEY(UniStudentID) REFERENCES University(StudentID)
)
 
CREATE TABLE Subjects(
    SubjectName char(20) PRIMARY KEY
)
 
CREATE TABLE Courses(
    CourseName char(50) PRIMARY KEY,
    GradeLevel int,
    SubjectName char(80) NOT NULL,
    FOREIGN KEY (SubjectName) REFERENCES Subjects(SubjectName)
)
 
CREATE TABLE Topics(
    TopicName char(20),
    CourseName char(20),
    Difficult int,
    PRIMARY KEY(TopicName, CourseName),
    FOREIGN KEY(CourseName) REFERENCES Courses(CourseName)
)
 
CREATE TABLE Assignment(
    AssignNumber int PRIMARY KEY,
    AssignDescription char(1000),
    Mark int
)
 
CREATE TABLE Give(
    AssignNumber int,
    StudentID int,
    UniStudentID int,
    PRIMARY KEY(AssignNumber, StudentID),
    FOREIGN KEY(AssignNumber) REFERENCES Assignment,
    FOREIGN KEY(StudentID) REFERENCES k_12,
    FOREIGN KEY(UniStudentID) REFERENCES University
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
 
INSERT INTO k_12 VALUES(35694356,"Tim", 18, 1,  0, 0, 112312, 1)
INSERT INTO k_12 VALUES(24357532,"Jimmy", 16, 0, 1, 1, 2, 1)
INSERT INTO k_12 VALUES(13294586,"Bob,", 14, 0, 0,  0, 184935, 4)
INSERT INTO k_12 VALUES(19283844,"Jones", 12,   0, 0, 0, 192383, 5)
INSERT INTO k_12 VALUES(10293948,"Johnny,", 18, 1,  1, 1, 123944, 6)
 
INSERT INTO University VALUES(23423672, Sean,   20, 1,  0,  0,  129488, 2)
INSERT INTO University VALUES(85566722, Isaac,  21, 0,  1,  0,  417475, 3)
INSERT INTO University VALUES(18553137, Hargreeves, 22, 0,  0,  1,  183929, 7)
INSERT INTO University VALUES(32904858, Bobby,  21, 0,  0,  0,  123948, 8)
INSERT INTO University VALUES(1239485,  Billy,  26, 0,  0,  1,  128384, 7)
 
INSERT INTO Availabilities VALUES(112312,   T/TH,   35694356, NULL,   1)
INSERT INTO Availabilities VALUES(2,    M/W/F,  24357532, NULL,   1)
INSERT INTO Availabilities VALUES(184935,   T/TH,   13294586, NULL,   4)
INSERT INTO Availabilities VALUES(192383,   M/T/W,  19283844, NULL,   5)
INSERT INTO Availabilities VALUES(123944, M/TH/F,   10293948, NULL,   6)
INSERT INTO Availabilities VALUES(129488, M/TH/SAT, NULL, 23423672,   2)
INSERT INTO Availabilities VALUES(417475, M/W/F,    NULL, 85566722,   3)
INSERT INTO Availabilities VALUES(183929, M/W/F,    NULL, 18553137,   7)
INSERT INTO Availabilities VALUES(123948, M/TH/F,   NULL, 32904858,   8)
INSERT INTO Availabilities VALUES(128384, M/TH/SAT, NULL, 1239485,    7)
 
INSERT INTO tutors VALUES(1, Leena, 43, 4)
INSERT INTO tutors VALUES(2, Aaliyah, 23, 3)
INSERT INTO tutors VALUES(3, Mike, 28, 4)
INSERT INTO tutors VALUES(4, Tony, 26, 4)
INSERT INTO tutors VALUES(5, Josh, 30, 5)
INSERT INTO tutors VALUES(6, Sophia, 24, 3)
INSERT INTO tutors VALUES(7, Marnie, 28, 4)
INSERT INTO tutors VALUES(8, Lewis, 17, 1)
 
INSERT INTO NeedHelp VALUES(35694356, NULL, Mathematics)
INSERT INTO NeedHelp VALUES(24357532, NULL, Physics)
INSERT INTO NeedHelp VALUES(13294586, NULL, Physics)
INSERT INTO NeedHelp VALUES(19283844, NULL, English)
INSERT INTO NeedHelp VALUES(10293948, NULL, English)
INSERT INTO NeedHelp VALUES(NULL, 23423672, Mathematics)
INSERT INTO NeedHelp VALUES(NULL, 85566722, Computer Science)
INSERT INTO NeedHelp VALUES(NULL, 18553137, Biology)
INSERT INTO NeedHelp VALUES(NULL, 32904858, Biology)
INSERT INTO NeedHelp VALUES(NULL, 1239485, Mathematics)
 
INSERT INTO CanTeach VALUES(Mathematics, 1)
INSERT INTO CanTeach VALUES(Physics, 2)
INSERT INTO CanTeach VALUES(English, 3)
INSERT INTO CanTeach VALUES(Computer Science, 4)
INSERT INTO CanTeach VALUES(Biology, 5)
 
INSERT INTO Reports VALUES(324234, Good)
INSERT INTO Reports VALUES(123123, Bad)
INSERT INTO Reports VALUES(123213, R u even human)
INSERT INTO Reports VALUES(956765, Stupid)
INSERT INTO Reports VALUES(456456, Genius)
 
INSERT INTO WriteReports VALUES(324234, 1)
INSERT INTO WriteReports VALUES(123123, 2)
INSERT INTO WriteReports VALUES(123213, 3)
INSERT INTO WriteReports VALUES(956765, 4)
INSERT INTO WriteReports VALUES(456456, 5)
 
INSERT INTO ReceiveReport VALUES(NULL, 23423672, 324234)
INSERT INTO ReceiveReport VALUES(24357532, NULL 123123)
INSERT INTO ReceiveReport VALUES(NULL, 85566722, 123213)
INSERT INTO ReceiveReport VALUES(NULL 18553137, 956765)
INSERT INTO ReceiveReport VALUES(32904858, NULL, 456456)
 
INSERT INTO Subjects VALUES(Mathematics)
INSERT INTO Subjects VALUES(Physics)
INSERT INTO Subjects VALUES(English)
INSERT INTO Subjects VALUES(Computer Science)
INSERT INTO Subjects VALUES(Biology)
 
INSERT INTO Courses VALUES(Pre-calc 11, 11, Math)
INSERT INTO Courses VALUES(Biology 12, 12, Science)
INSERT INTO Courses VALUES(MATH 220, 14, Math)
INSERT INTO Courses VALUES(Language Arts 9, 9, English)
INSERT INTO Courses VALUES(BIOL 111, 13, Science)
 
INSERT INTO Topics VALUES(Trigonometry, Pre-calc 11, 7)
INSERT INTO Topics VALUES(Cells, Biology 12, 3)
INSERT INTO Topics VALUES(Sequence Proofs, MATH 220, 10)
INSERT INTO Topics VALUES(Hamlet, Language Arts 9, 1)
INSERT INTO Topics VALUES(Natural Disasters, BIOL 111, 3)
 
INSERT INTO Assignment VALUES(2, Trigonometry Identity Practice, 90)
INSERT INTO Assignment VALUES(12, Worksheet on cellular composition, 78)
INSERT INTO Assignment VALUES(5, Worksheet on causes of natural disasters, 82)
INSERT INTO Assignment VALUES(3, Essay on Hamlets disasters, 95)
INSERT INTO Assignment VALUES(6, Sequential proof problem set, 60)
 
INSERT INTO Give VALUES(2, NULL, 23423672)
INSERT INTO Give VALUES(12, 24357532, NULL)
INSERT INTO Give VALUES(5, NULL, 85566722)
INSERT INTO Give VALUES(3, NULL, 18553137)
INSERT INTO Give VALUES(6, NULL, 32904858)
 
 
INSERT INTO Has VALUES(Trigonometry Identity Practice, Pre-calc 11, 23423672, 2)
INSERT INTO Has VALUES(Worksheet on cellular composition, Biology 12, 24357532, 12)
INSERT INTO Has VALUES(Worksheet on causes of natural disasters, BIOL 111, 85566722, 5)
INSERT INTO Has VALUES(Essay on Hamlets disasters, Language Arts 9, 18553137, 3)
INSERT INTO Has VALUES(Sequential proof problem set, MATH 220, 32904858, 6)

