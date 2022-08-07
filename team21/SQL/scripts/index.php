<html>

    <head>
        <title>tutor service test</title>
    </head>


    <body>
        <!--K-12--> 
        <h2>insert k-12 student</h2>

        <form method="POST" action="index.php"> <!--refresh page when submitted-->
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

        <h2>delete</h2>
        <form method="POST" action="index.php">
                <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
                <input type="hidden" id="deleteTablesRequest" name="deleteTablesRequest">
                <p><input type="submit" value="delete" name="delete"></p>
        </form>


        <?php

        $db_conn = NULL;
            // conneciton to oracle
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

            function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
                //echo "<br>running ".$cmdstr."<br>";
                global $db_conn, $success;

                $statement = OCIParse($db_conn, $cmdstr);
                //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

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

            function handleDeleteRequest() {
                global $db_conn;
                executePlainSQL("DELETE FROM k-12");
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

            function handlePOSTRequest() {
                if (connectToDB()) {
                    if (array_key_exists('insertQueryRequest', $_POST)) {
                        handleInsertRequest();
                    } else if (array_key_exists('deleteTablesRequest', $_POST)) {
                        handleDeleteRequest();
                    }
                    disconnectFromDB();
                }
            }

        if (isset($_POST['delete']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        }
        ?>

    </body>
</html>