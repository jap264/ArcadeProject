<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<h3>Reset Password</h3>
<?php
if (isset($_POST["reset"])) {
    $email = null;
    $password = null;
    $confirm = null;
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    if (isset($_POST["confirm"])) {
        $confirm = $_POST["confirm"];
    }
    $isValid = true;
    //check if passwords match on the server side
    if ($password == $confirm) {
        //not necessary to show
        //echo "Passwords match <br>";
    }
    else {
        flash("Passwords don't match");
        $isValid = false;
    }
    if (!isset($email) || !isset($password) || !isset($confirm)) {
        $isValid = false;
    }
    //TODO other validation as desired, remember this is the last line of defense
    if ($isValid) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $db = getDB();
        if (isset($db)) {
            //here we'll use placeholders to let PDO map and sanitize our data
            $stmt = $db->prepare("UPDATE Users set password=:password WHERE email=:email");
            //here's the data map for the parameter to data
            $params = array(":email" => $email, ":password" => $hash);
            $r = $stmt->execute($params);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                flash("Successfully reset password! Please login.");
            }
            else {
                flash("An error occurred, please try again");
            }
        }
    }
    else {
        flash( "There was a validation issue");
    }
}
//safety measure to prevent php warnings
if (!isset($email)) {
    $email = "";
}
if (!isset($username)) {
    $username = "";
}
?>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php safer_echo($email); ?>"/>
        <label for="p1">Password:</label>
        <input type="password" id="p1" name="password" required/>
        <label for="p2">Confirm Password:</label>
        <input type="password" id="p2" name="confirm" required/>
        <input type="submit" name="reset" value="Reset"/>
    </form>
<?php require(__DIR__ . "/partials/flash.php");
