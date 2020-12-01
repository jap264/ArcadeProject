<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we'll put this at the top so both php block have access to it
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
//fetching
$result = [];
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT ph.*, Users.username FROM PointsHistory as ph JOIN Users on ph.user_id = Users.id WHERE ph.id = $id like :q LIMIT 10");
    $r = $stmt->execute([":q" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>
    <h3>View Points History</h3>
<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
        <div class="card-title">
            <?php safer_echo($result["username"]); ?>
        </div>
        <div class="card-body">
            <div>
                <p>Stats</p>
                <div>Points Change: <?php safer_echo($result["points_change"]); ?></div>
		<div>Reason: <?php safer_echo($result["reason"]); ?></div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Error looking up id...</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");
