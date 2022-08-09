<html>
    <body>
    <head>
        <title>Tutoring Service Title
        </title>
    </head>

    <!--Signup / login to tutor or student.
    Login through student / tutor ID
    after login and signup go straight to tutor / student page.-->
    <form method="POST" action="studentMain.php">
        <p><input type="submit" value="Student Login" name="login"></p>
    </form>

    <form method="POST" action="tutorMain.php">
        <p><input type="submit" value="Tutor Login" name="login"></p>
    </form>

    <form method="POST" action="studentSignup.php">
        <p><input type="submit" value="Student Signup" name="ssignup"></p>
    </form> 

    <form method="POST" action="tutorSignup.php">
        <p><input type="submit" value="Tutor Signup" name="tsignup"></p>
    </form>


</body>
</html>