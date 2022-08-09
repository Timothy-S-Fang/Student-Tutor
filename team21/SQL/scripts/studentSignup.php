<html>
    <head>
        <title>Student Registration</title>
    </head>

    <body>
    <h2>Student Registration</h2>

        <form method="POST" action="studentSignup.php">
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
            $db_conn = OCILogon("ora_timfang1", "a78166642", "dbhost.students.cs.ubc.ca:1522/stu");
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


        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['StudentID'],
                ":bind2" => $_POST['StudentName'],
                ":bind3" => $_POST['Age'],
                ":bind4" => $_POST['Exams'],
                ":bind5" => $_POST['UniApplication'],
                ":bind6" => $_POST['SAT'],
                ":bind7" => $_POST['STS'],
                ":bind8" => $_POST['TutorID']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("INSERT INTO k_12 VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8)", $alltuples);
            // execurePlainSQL("INSERT INTO k_12 VALUES(12, 12, 12,12,12,12,112312, 1)", $alltuples);
            OCICommit($db_conn);
        }


        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
               if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                }
                disconnectFromDB();
            }
        }

		if (isset($_POST['insertSubmit'])) {

            handlePOSTRequest();
        }
		?>
	</body>
</html>


