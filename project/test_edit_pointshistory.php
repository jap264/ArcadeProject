<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//saving
if (isset($_POST["save"])) {
    //TODO add proper validation/checks
    $reason = $_POST["reason"];
    $pc = $_POST["points_change"];
    $user = get_user_id();
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE PointsHistory set reason=:reason, points_change=:pc where id=:id");
        $r = $stmt->execute([
            ":reason" => $reason,
            ":pc" => $pc,
            ":id" => $id
        ]);
        if ($r) {
            flash("Updated successfully with id: " . $id);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error updating: " . var_export($e, true));
        }
    }
    else {
        flash("ID isn't set, we need an ID in order to update");
    }
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT ph.user_id, points_change, username, reason FROM PointsHistory as ph JOIN Users on ph.user_id = Users.id WHERE ph.id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
//get scores for dropdown
$db = getDB();
$stmt = $db->prepare("SELECT id,score from Scores LIMIT 10");
$r = $stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>Edit Points History</h3>
    <form method="POST">
	<label>User</label>
	<input name="user" placeholder="User" value="<?php echo $result["username"]; ?>"/>
        <label>Score</label>
        <select name="score" value="<?php echo $result["score"];?>" >
            <option value="-1">None</option>
            <?php foreach ($scores as $score): ?>
                <option value="<?php safer_echo($score["id"]); ?>" <?php echo ($result["user_id"] == $score["id"] ? 'selected="selected"' : ''); ?>
                ><?php safer_echo($score["score"]); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Points Change</label>
        <input type="number" min="1" name="points_change" value="<?php echo $result["points_change"]; ?>"/>
        <label>Reason</label>
        <input name="reason" placeholder="Reason" value="<?php echo $result["reason"]; ?>"/>
        <input type="submit" name="save" value="Update"/>
    </form>


<?php require(__DIR__ . "/partials/flash.php");
