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
            <input type="submit" name="Show Available Tutors"></p>
        </form>

        <h2>Display Available Courses</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printCourses" name="printCourses">
            <input type="submit" name="Show Available Courses"></p>
        </form>

        <h2>Display Hardest Topics</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printHardest" name="printHardest">
            <input type="submit" name="Display Hardest Topics"></p>
        </form>
        
        <h2>Delete Assignments</h2>
        <form method="POST" action="studentMain.php"> 
            <input type="hidden" id="deleteAssignment" name="deleteAssignment">
            Assignment Number: <input type="text" name="assNum"> <br /><br />
            <input type="submit" value="Delete Assignment" name="deleteAssignment"></p>
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
            $db_conn = OCILogon("ora_iz9877", "a49050693", "dbhost.students.cs.ubc.ca:1522/stu");

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

        function printResult($result) { 
            echo "<br>Retrieved data from table demoTable:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function getResult() {
            $table = executePlainSQL("SELECT * FROM demoTable");

            return $table;
        }

        // passing student ID variable to other scripts so can access certain profiles.
        $studentID = NULL;
        $inUni = NULL;

        //takes in studnet ID input
        function passID() {
            $id = $_POST['studentID'];

            return $id;
        }

        function printTutors($result) { 
            echo "<h1>Hi I got here</h1>";
            echo "<table>";
            echo "<tr><th>ID</th>sele<th>Age</th><th>Rating</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["TUTORID"] . "</td><td>" . $row["TAGE"] . "</td><td>" . $row["RATINGS"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td><td>" . $row["Age"] . "</td><td>" . $row["Rating"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function getTutors() {
            $table = executePlainSQL("SELECT * FROM Tutors");
            return $table;
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
            global $dbconn;

            $old_name = $_POST['oldName'];
            $new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
            OCICommit($db_conn);
        }

        
        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['StudentID'],
                ":bind2" => $_POST['Age'],
                ":bind3" => $_POST['StudentName'],
                ":bind4" => $_POST['Exams'],
                ":bind5" => $_POST['UniApplication'],
                ":bind6" => $_POST['SAT'],
                ":bind7" => $_POST['STS'],
                ":bind8" => $_POST['TutorID'],
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into k_12 values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8,)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM demoTable");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
            }
        }

        function deleteAssignment() {
            global $db_conn;
            $aNum = $_POST['assNum'];
            $result = executePlainSQL("DELETE FROM assignment WHERE ASSIGNNUMBER = $aNum");

            echo "<h1>Deleted Assignment</h1>";

            OCICommit($db_conn);
            
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row['ASSIGNNUMBER'] . "</td><td>" . $row['MARK'] .  "</td></tr>"; 
            }
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
                } else if (array_key_exists('deleteAssignment', $_POST)) {
                    deleteAssignment();
                } 

                disconnectFromDB();
            }
        }


        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('printTutors', $_GET)) {
                    $tutors = getTutors();
                    printTutors($tutors);
                } else if (array_key_exists('printTuples', $_GET)) {
                    $res = getResult();
                    printResult($res);
                } else if (array_key_exists('printCourses', $_GET)) {
                    $res = getCourses();
                    // echo "<h1>I got back</h1>";
                    printCourse($res);
                } else if (array_key_exists('printHardest', $_GET)) {
                    $res = getHardest();
                    // echo "<h1>I got back</h1>";
                    printHardest($res);
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
            //echo $studentID;
        } else if (isset($_POST['loginU'])) {
            // university login
            $studentID = passID();
            $inUni = True;
        } else if (isset($_GET['printTutors'])) {
            handleGETRequest();
        } else if (isset($_GET['printCourses'])) {
            handleGETRequest();
        } else if (isset($_GET['printHardest'])) {
            handleGETRequest();
        } else if (isset($_POST['deleteAssignment'])) {
            handlePOSTRequest();
        }
		?>
	</body>
</html>

