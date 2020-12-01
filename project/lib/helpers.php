<?php
session_start();//we can start our session here so we don't need to worry about it on other pages
require_once(__DIR__ . "/db.php");
//this file will contain any helpful functions we create
//I have provided two for you
function is_logged_in() {
    return isset($_SESSION["user"]);
}

function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//Top 10 Functions

function top_weekly_scores(){
<?php
$result = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT score.id,username,score.created,score FROM Scores as score JOIN Users on score.user_id = Users.id ORDER by score DESC, score.created ASC LIMIT 10");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
?>

<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-body">
            <div>
                <p>Top 10 Lifetime Scores:</p>
                <?php foreach ($result as $r): ?>
                <div> User: <?php safer_echo($r["username"]); ?></div>
                <div> Score: <?php safer_echo($r["score"]); ?></div>
                <div> Created: <?php safer_echo($r["created"]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
 <p>No results...</p>
<?php endif; ?>
}

function top_monthly_scores(){
<?php
$result = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT score.id,username,score.created,score FROM Scores as score JOIN Users on score.user_id = Users.id ORDER by score DESC, score.created ASC LIMIT 10");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
?>

<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-body">
            <div>
                <p>Top 10 Lifetime Scores:</p>
                <?php foreach ($result as $r): ?>
                <div> User: <?php safer_echo($r["username"]); ?></div>
                <div> Score: <?php safer_echo($r["score"]); ?></div>
                <div> Created: <?php safer_echo($r["created"]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
 <p>No results...</p>
<?php endif; ?>
}

function top_lifetime_scores(){
<?php
//fetching
$result = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT score.id,username,score.created,score FROM Scores as score JOIN Users on score.user_id = Users.id ORDER by score DESC, score.created ASC LIMIT 10");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
?>

<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-body">
            <div>
                <p>Top 10 Lifetime Scores:</p>
                <?php foreach ($result as $r): ?>
                <div> User: <?php safer_echo($r["username"]); ?></div>
                <div> Score: <?php safer_echo($r["score"]); ?></div>
                <div> Created: <?php safer_echo($r["created"]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
 <p>No results...</p>
<?php endif; ?>
}

//end Top 10 Functions

//for flash feature
function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

//end flash
?>
