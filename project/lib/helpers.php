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

function today(){
    return date("Y-m-d H-i-s");
}

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

function getURL($path) {
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/IT202/project/$path";
}

function getBalance() {
    if (is_logged_in() && isset($_SESSION["user"]["balance"])) {
        return $_SESSION["user"]["balance"];
    }
    return 0;
}

function getWinners() {
    if (isset($_GET["id"])) {
    	$id = $_GET["id"];
    }	
    
    $comps = [];
    if (isset($id)) {
    	$db = getDB();
    	$stmt = $db->prepare("SELECT id, name, created, expires, participants, min_score, first_place_per, second_place_per, third_place_per, reward FROM F20_Competitions WHERE expires < current_timestamp AND paid_out = 0");
    	$stmt->execute([":id" => $id]);
     	$comps = $stmt->fetchAll(PDO::FETCH_ASSOC);
     	if(!$comps){
        	$e = $stmt->errorInfo();
        	flash($e[2]);
    	}
    }

    foreach($comps as $index=>$c){
	$db = getDB();
	$stmt = $db->prepare("SELECT F20_UserCompetitions.user_id, max(score) from F20_UserCompetitions JOIN Scores ON Scores.user_id = F20_UserCompetitions.user_id where Scores.created BETWEEN (select created from F20_Competitions where id = 9) AND (select expires from F20_Competitions where id = 9) group by F20_UserCompetitions.user_id,score order by score desc LIMIT 3");
	$stmt->execute([":id" => $id]);
	
    }
    
}
?>
