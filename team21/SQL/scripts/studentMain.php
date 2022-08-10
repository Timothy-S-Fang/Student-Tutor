<html>
    <head>
        <title>Student Main Page</title>
    </head>

    <body>

        <form method="POST" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Student ID: <input type="text" name="studentID"> <br /><br />   
            <input type="submit" value="Login as K-12 Student" name="loginK"></p>
            <input type="submit" value="Login as University Student" name="loginU"></p>
        </form>

        <hr />

        <h2>Show All Students</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTuplesRequest" name="printTuplesRequest">
            <input type="submit" value="Show" name="printTuples"></p>
        </form>

        <hr />

        <h2>Update Profile</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="studentMain.php"> 
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            SID: <input type="text" name="StudentID"> <br /><br />
            Updated Name: <input type="text" name="newName"> <br /><br />
            Update Age: <input type="text" name="newAge"> <br /><br />
            Updated Exams: <input type="text" name="newExams"> <br /><br />
            Updated UniApplication: <input type="text" name="newUniApplication"> <br /><br />
            Updated SAT: <input type="text" name="newSAT"> <br /><br />
            Updated STS: <input type="text" name="newSTS"> <br /><br />
            Updated TutorID: <input type="text" name="newTutorID"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>
        <hr />
        <h2>Easiest Subject</h2>
        <p>Calculated by the highest average of all assignment grades</p>
        <form method="GET" action="studentMain.php"> 
            <input type="hidden" id="calculateAvgQueryRequest" name="calculateAvgQueryRequest">
            <input type="submit" value="Calculate" name="calculateHighAvg"></p>
            <hr />
        <h2>Display Available Tutors</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTutors" name="printTutors">

            <input type="submit" name="Show Available Tutors"></p>
        </form>
        <hr />
        <h2>Display Available Courses</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printCourses" name="printCourses">
            <input type="submit" name="Show Available Courses"></p>
        </form>
        <hr />
        <h2>Display Hardest Topics</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printHardest" name="printHardest">
            <input type="submit" name="Display Hardest Topics"></p>
        </form>
        <hr />
        <h2>Delete Assignments</h2>
        <form method="POST" action="studentMain.php"> 
            <input type="hidden" id="deleteAssignment" name="deleteAssignment">
            Assignment Number: <input type="text" name="assNum"> <br /><br />
            <input type="submit" value="Delete Assignment" name="deleteAssignment"></p>
        </form>
        <hr />

        <h2>Find Tutors That Teach Everything</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="findTutors" name="findTutors">
            <input type="submit" name="help"></p>
        </form>
        <hr />
        <h2>Display Best Rating of Tutor per Subject</h2>
        <form method="POST" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="checkRatings" name="checkRatings">
            Subject: <input type="text" name="subject"> <br /><br />
            <input type="submit" value="Check Highest Rating" name="rater"></p>
        </form>
        <hr />
        <h2>Return Most Outstanding Student</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="bestStudent" name="bestStudent">
            <input type="submit" name="rater"></p>
        </form>

        <hr />
        <h2>Display Failed assignments</h2>
        <form method="GET" action="studentMain.php"> 
            <input type="hidden" id="failedAssignments" name="failedAssignments">
            <input type="submit" name="Display Failed assignments"></p>
        </form> 


        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP
        session_start(); 
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        
        function connectToDB() {
            global $db_conn;
            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.

            $db_conn = OCILogon("ora_stang001", "a22969331", "dbhost.students.cs.ubc.ca:1522/stu");
            

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }

        }

        function disconnectFromDB() {
            global $db_conn;
            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function executePlainSQL($cmdstr) { 
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
         
            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }


        function getStudents() {
            $table = executePlainSQL("SELECT * FROM k_12");
            echo "<br>Retrieved data from stu:<br>";
            echo "<table>";
            echo "<tr><th>StudentID</th><th>Name</th><th>Age</th><th>SAT</th></tr>";

            while ($row = OCI_Fetch_Array($table, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
            }
        }

        function printTutors($result) { //prints results from a select statement
            echo "<br>Available Tutors: <br>";
            echo "<table>";
            echo "<tr><th> ID </th><th> Name </th><th> Age </th><th> Rating/5 </th><th> Subject </th><th> Schedule ID </th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["TUTORID"] . "</td><td>" . $row["TUTORNAME"] ."</td><td>" . $row["TAGE"] ."</td><td>" . $row["RATINGS"] ."</td><td>" . $row["SUBJECTNAME"] ."</td><td>" . $row["STS"] ."</td></tr>"; //or just use "echo $row[0]"

            }
            echo "</table>";

        }

        function getTutors() {
            $table = executePlainSQL("SELECT * FROM tutors");
            return $table;
        }

        // passing student ID variable to other scripts so can access certain profiles.
        $studentID = NULL;
        $inUni = NULL;

        //takes in studnet ID input
        function passID() {
            $id = $_POST['studentID'];
            echo $id;
            return $id;
        }



        function suggestedTutorsPerSubject($course) {
            
            $suggest = executePlainSQL("SELECT MAX(t.ratings) FROM tutors t INNER JOIN CanTeach c ON t.tutorid = c.tutorid GROUP BY c.subjectName HAVING c.subjectName = "."'$course'");
            //$suggest = executePlainSQL("SELECT * from tutors t where t.subjectName = " . "'$course'" );
            echo "<br>Best Tutors For Subject <br>";
            echo "<table>";
            echo "<tr><th> Rating </th></tr>";
            
            while ($row = OCI_Fetch_Array($suggest, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] ."</td></tr>"; //or just use "echo $row[0]"

            }

            echo "</table>";
        }




        function printCourse($result) { 
            // echo "<h1> I got here </h1>";
            echo "<table>";
            echo "<tr><th>Name</th><th>GradeLevel</th><th>Subject</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                // echo "<h1> I got here in the loop </h1>";
                // echo "$row";
                // echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
                echo "<tr><td>" . $row['COURSENAME'] . "</td><td>" . $row['GRADELEVEL'] . "</td><td>" . $row["SUBJECTNAME"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<option value =\"".$row['COURSENAME ']."\">".$row["GRADELEVEL"]."\">".$row["SUBJECTNAME"]."<\option>";
            }

            echo "</table>";
        }

        function getCourses() {
            $table = executePlainSQL("SELECT * FROM Courses");
            return $table;
        }

        function getHardest() {
            $table = executePlainSQL("SELECT TopicName, CourseName, MAX(Difficult) FROM Topics GROUP BY CourseName, TopicName ORDER BY MAX(Difficult)");
            return $table;
        }
        
        function printHardest($result) { 
            echo "<table>";
            echo "<tr><th>Course</th><th>Name</th><th>Difficulty</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row['COURSENAME'] . "</td><td>" . $row['TOPICNAME'] . "</td><td>" . $row["MAX(DIFFICULT)"] . "</td></tr>"; 
            }

            echo "</table>";
        }



        function handleUpdateRequest() {
            global $db_conn;

            $student_ID = $_POST['StudentID'];
            $new_age = $_POST['newAge'];
            $new_name = $_POST['newName'];
            $new_exam = $_POST['newExams'];
            $new_uni_application = $_POST['newUniApplication'];
            $new_SAT = $_POST['newSAT'];
            $new_STS = $_POST['newSTS'];
            $new_TID = $_POST['newTutorID'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE k_12 SET StudentName= '" . $new_name . "' WHERE StudentID = ". $student_ID ."");

            OCICommit($db_conn);
            
        }


        function goodTutor () {
            // finds tutor that can teach all subjects, if exists
            $help = executePlainSQL("SELECT t.tutorname, t.tutorid FROM tutors t 
            WHERE NOT EXISTS (
                (SELECT s.SubjectName FROM schlSubjects s)
                MINUS
                (SELECT c.SubjectName FROM CanTeach c WHERE t.TutorID = c.TutorID)
            )");
            echo "<br>Best Tutors For Subject <br>";
            echo "<table>";
            echo "<tr><th> Name </th><th> ID </th></tr>";
            
            while ($row = OCI_Fetch_Array($help, OCI_BOTH)) {
                echo "<tr><td>" . $row["TUTORNAME"] ."</td><td>" . $row["TUTORID"] ."</td></tr>"; //or just use "echo $row[0]"
            }


            echo "</table>";

        }


        function deleteAssignment() {
            global $db_conn;
            $aNum = $_POST['assNum'];
            $result = executePlainSQL("DELETE FROM assignment WHERE ASSIGNNUMBER = $aNum");

            // echo "<h1>Deleted Assignment</h1>";

            OCICommit($db_conn);
            
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row['ASSIGNNUMBER'] . "</td><td>" . $row['MARK'] .  "</td></tr>"; 
            }
        }

        function handleHighAvgRequest() {
            global $db_conn;
            
            $result = executePlainSQL("SELECT MAX(x.avg) FROM ( SELECT AVG(Mark) as avg FROM Assignment INNER JOIN Has ON Has.AssignNumber=Assignment.AssignNumber GROUP BY CourseName)x");
            if (($row = oci_fetch_row($result)) != false) {
                echo "<br>Highest Avg: " . $row[0] . "<br>";
            }
            OCICommit($db_conn);
        }

        function bestStudent() {
            global $db_conn;

            echo "<br>Best Student <br>";
            echo "<table>";
            echo "<tr><th> Name </th><th> Grade </th></tr>";
            
            $result = executePlainSQL("SELECT StudentName, MAX(Mark) FROM Give INNER JOIN k_12 ON Give.StudentID = k_12.StudentID INNER JOIN Assignment ON Assignment.AssignNumber = Give.AssignNumber GROUP BY StudentName");
            if (($row = oci_fetch_row($result)) != false) {
                echo "<tr><td>" . $row[0] ."</td><td>" . $row[1] ."</td></tr>";
            }
            OCICommit($db_conn);
        }
        
        function failed() {
            global $db_conn;

            echo "<br>Failed Assignemnts <br>";
            echo "<table>";
            echo "<tr><th>Assignemnt Number</th><th>Mark</th></tr>";
            
            $result = executePlainSQL("SELECT * FROM assignment WHERE Mark < 50");
            if ($row = oci_fetch_row($result)) {
                echo "<tr><td>" . $row[0] ."</td><td>" . $row[2] ."</td></tr>";
            }
            OCICommit($db_conn);
        }



        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('deleteAssignment', $_POST)) {
                    deleteAssignment();
                } else if (array_key_exists('rater', $_POST)) {
                    $course = $_POST['subject'];
                    echo $course;
                    suggestedTutorsPerSubject($course);
                }
                disconnectFromDB();
            }
        }


        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples   ', $_GET)) {
                    $table = executePlainSQL("SELECT * FROM k_12");
                    echo "<br>Retrieved data from stu:<br>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th></tr>";

                    if ($row = OCI_Fetch_Array($table, OCI_BOTH)) {
                        echo "<tr><td>" . $row["Age"] . "</td><td>" . $row["StudentName"] . "</td></tr>"; //or just use "echo $row[0]"
                    }
                    echo "</table>";
                } else if (array_key_exists('printTuples', $_GET)) {
                    getStudents();
                } else if (array_key_exists('calculateHighAvg', $_GET)) {
                    handleHighAvgRequest();
                } else if (array_key_exists('printCourses', $_GET)) {
                    $res = getCourses();
                    // echo "<h1>I got back</h1>";
                    printCourse($res);
                } else if (array_key_exists('printTutors', $_GET)) {
                    $tutors = getTutors();
                    printTutors($tutors);
                } else if (array_key_exists('printHardest', $_GET)) {
                    $res = getHardest();
                    // echo "<h1>I got back</h1>";
                    printHardest($res);

                } else if (array_key_exists('help', $_GET)) {
                    goodTutor();
                } else if (array_key_exists('bestStudent', $_GET)) {
                    bestStudent();
                } else if (array_key_exists('failedAssignments', $_GET)) {
                    failed();
                }
                disconnectFromDB();
            }
        }
        
		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_POST['loginK'])) {
            // k-12 login
            $studentID = passID();
            $inUni = False;
            echo $studentID;
        } else if (isset($_POST['loginU'])) {
            // university login
            $studentID = passID();
            $inUni = True;
        } else if (isset($_GET['printTuplesRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['calculateAvgQueryRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['printTutors'])) {
            handleGETRequest();
        } else if (isset($_GET['printCourses'])) {
            handleGETRequest();
        } else if (isset($_GET['printHardest'])) {
            handleGETRequest();
        } else if (isset($_POST['deleteAssignment'])) {
            handlePOSTRequest();
        } else if (isset($_POST['rater'])) {
            handlePOSTRequest();
        } else if (isset($_GET['bestStudent'])) {
            handleGETRequest();
        } else if (isset($_GET['failedAssignments'])) {
            handleGETRequest();
        }
        


		?>
	</body>
</html>

