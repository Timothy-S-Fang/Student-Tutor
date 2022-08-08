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

        <h2>Show Student Details</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTuples" name="printTuples">
            <input type="submit" name="printTuples"></p>
        </form>

        <h2>Update Profile</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="tutor-service.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            SID: <input type="text" name="StudentID"> <br /><br />
            Updated Name: <input type="text" name="newName"> <br /><br />
            Updated Exams: <input type="text" name="newExams"> <br /><br />
            Updated UniApplication: <input type="text" name="newUniApplication"> <br /><br />
            Updated SAT: <input type="text" name="newSAT"> <br /><br />
            Updated STS: <input type="text" name="newSTS"> <br /><br />
            Updated TutorID: <input type="text" name="newTutorID"> <br /><br />


            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <h2>Display Available Tutors</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTutors" name="printTutors">
            <input type="submit" name="Show Available Tutors"></p>
        </form>

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP
        session_start(); 
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
       
        
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

        function printResult($result) { 
            echo "<br>Retrieved data from Student:<br>";
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
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Age</th><th>Rating</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td><td>" . $row["Age"] . "</td><td>" . $row["Rating"] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }

        function getTutors() {
            $table = executePlainSQL("SELECT * FROM tutors");
            return $table;
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
        }
		?>
	</body>
</html>

