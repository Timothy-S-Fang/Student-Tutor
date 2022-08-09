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

        <h2>Display Available Tutors</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTutors" name="printTutors">
            <input type="submit" name="tutors"></p>
        </form>

        <h2>Find Tutors That Teach Everything</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="findTutors" name="findTutors">
            <input type="submit" name="help"></p>
        </form>

        <h2>Display Best Rating of Tutor per Subject</h2>
        <form method="POST" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="checkRatings" name="checkRatings">
            Subject: <input type="text" name="subject"> <br /><br />
            <input type="submit" value="Check Highest Rating" name="rater"></p>
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

        

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
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
                if (array_key_exists('tutors', $_GET)) {
                    //$tutors = getTutors();
                    //printTutors($tutors);
                    $res = getTutors();
                    printTutors($res);
                } else if (array_key_exists('help', $_GET)) {
                    goodTutor();
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
            echo $studentID;
        } else if (isset($_GET['printTutors'])) {
            handleGETRequest();
        } else if (isset($_POST['rater'])) {
            handlePOSTRequest();
        } else if (isset($_GET['findTutors'])) {
            handleGETRequest();
        }
        


		?>
	</body>
</html>

