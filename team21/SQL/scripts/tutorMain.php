<html>
    <head>
        <title>Tutor Main Page</title>
    </head>

    <body>

        <form method="POST" action="tutorMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Tutor ID: <input type="text" name="tutorID"> <br /><br />   
            <input type="submit" value="Login as Tutor" name="login"></p>
        </form>

        <h2>Show Tutor Details</h2>
        <form method="GET" action="tutorMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTuples" name="printTuples">
            <input type="submit" name="printTuples"></p>
        </form>

        <h2>Update Profile</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="tutor-service.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            TutorID: <input type="text" name="tutorID"> <br /><br />
            Updated Age: <input type="text" name="age"> <br /><br />
            Updated Name: <input type="text" name="name"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

<!--
        <h2>insert k-12 student</h2>

        <form method="POST" action="main.php">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            StudentID: <input type="text" name="StudentID"> <br /><br />
            Age: <input type="text" name="Age"> <br /><br />
            StudentName: <input type="text" name="StudentName"> <br /><br />
            Exams: <input type="text" name="Exams"> <br /><br />
            UniApplication: <input type="text" name="UniApplication"> <br /><br />
            SAT: <input type="text" name="SAT"> <br /><br />
            STS: <input type="text" name="STS"> <br /><br />
            TutorID: <input type="text" name="TutorID"> <br /><br />
            <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

        <h2>Update Name in DemoTable</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="main.php">
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Old Name: <input type="text" name="oldName"> <br /><br />
            New Name: <input type="text" name="newName"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>Count the Tuples in DemoTable</h2>
        <form method="GET" action="main.php">
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

        <hr />
-->
        <h2>Display Available Tutors</h2>
        <form method="GET" action="studentMain.php"> <!--refresh page when submitted-->
            <input type="hidden" id="printTuples" name="printTuples">
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
        $tutorID = NULL;
        $inUni = NULL;

        //takes in studnet ID input
        function passID() {
            $id = $_POST['tutorID'];

            return $id;
        }

        function printStudents($result) { 
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

            executeBoundSQL("INSERT INTO k_12 VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8,)", $alltuples);
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
                if (array_key_exists('countTuples   ', $_GET)) {
                    $tutors = getTutors();
                    printTuples($tutors);
                } else if (array_key_exists('printTuples', $_GET)) {
                    $res = getResult();
                    printResult($res);
                }

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_POST['login'])) {
            // k-12 login
            $tutorID = passID();
        } else if (isset($_GET['printTuples'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>

