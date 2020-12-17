<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT Scores.id,username,score,Scores.created,user_id from Scores JOIN Users on Scores.user_id = Users.id WHERE username like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results", var_export($stmt->errorInfo(), true));
    }
}
?>
<form method="POST">
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
		    <div>
                        <div>User:</div>
                        <div><a type="button" href="external_profile.php?id=<?php safer_echo($r['user_id']); ?>"><?php safer_echo($r["username"]); ?></a></div>
                    </div>
                    <div>
                        <div>Score:</div>
                        <div><?php safer_echo($r["score"]); ?></div>
                    </div>
                    <div>
                        <div>Created:</div>
                        <div><?php safer_echo($r["created"]); ?></div>
                    </div>
                    <div>
                        <div>User Id:</div>
                        <div><?php safer_echo($r["user_id"]); ?></div>
                    </div>
                    <div>
                        <a type="button" href="test_edit_scores.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <a type="button" href="test_view_scores.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
                </div>
		</br>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
