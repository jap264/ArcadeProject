<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();
if (isset($_POST["join"])) {
    $balance = getBalance();
    //prevent user from joining expired or paid out comps
    $stmt = $db->prepare("select fee,participants,reward,id from F20_Competitions where id = :id && expires > current_timestamp && paid_out = 0");
    $r = $stmt->execute([":id" => $_POST["cid"]]);
    if ($r) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $fee = (int)$result["fee"];
	    $id = $result["id"];
           // if ($balance >= $fee) {
		$reward = (int)$result["reward"] + ($fee * .5);
		$participants = (int)$result["participants"] + 1;
                $stmt = $db->prepare("INSERT INTO F20_UserCompetitions (competition_id, user_id) VALUES(:cid, :uid)");
                $r = $stmt->execute([":cid" => $_POST["cid"], ":uid" => get_user_id()]);
                if ($r) {
                    	$stmt = $db->prepare("UPDATE F20_Competitions set reward=:reward, participants=:participants where id=:id");
			$r = $stmt->execute([
				":reward"=>$reward,
				":participants"=>$participants,
				":id" => $id
			]);
			
			flash("Successfully joined competition!", "success");
                    	die(header("Location: #"));
               /* }
                else {
                    flash("There was a problem joining the competition: " . var_export($stmt->errorInfo(), true), "danger");
                }*/
            }
            else {
                flash("You can't afford to join this competition, try again later", "warning");
            }
        }
        else {
            flash("Competition is unavailable", "warning");
        }
    }
    else {
        flash("Competition is unavailable", "warning");
    }
}
$stmt = $db->prepare("SELECT c.*, UC.user_id as reg FROM F20_Competitions c LEFT JOIN (SELECT * FROM F20_UserCompetitions where user_id = :id) as UC on c.id = UC.competition_id WHERE c.expires > current_timestamp AND paid_out = 0 ORDER BY expires ASC LIMIT 10");
$r = $stmt->execute([":id" => get_user_id(),]);
if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem looking up competitions: " . var_export($stmt->errorInfo(), true), "danger");
}
?>
    <div class="container-fluid">
        <h3>Competitions</h3>
        <div class="list-group">
            <?php if (isset($results) && count($results)): ?>
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item">
                        <div class="row">
                            </br>
			    <div class="col">
                               Comp Name: <?php safer_echo($r["name"]); ?>
                            </div>
                            <div class="col">
                                # of Participants: <?php safer_echo($r["participants"]); ?>
                            </div>
                            <div class="col">
                                Required Score: <?php safer_echo($r["min_score"]); ?>
                            </div>
                            <div class="col">
                                Reward: <?php safer_echo($r["reward"]); ?>
                                <!--TODO show payout-->
                            </div>
                            <div class="col">
                                Expires: <?php safer_echo($r["expires"]); ?>
                            </div>
                            <div class="col">
                                Actions: <?php if ($r["reg"] != get_user_id()): ?>
                                    <form method="POST">
                                        <input type="hidden" name="cid" value="<?php safer_echo($r["id"]); ?>"/>
                                        <input type="submit" name="join" class="btn btn-primary"
                                               value="Join (Cost: <?php safer_echo($r["fee"]); ?>)"/>
                                    </form>
                                <?php else: ?>
                                    Already Registered
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item">
                    No competitions available right now
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require(__DIR__ . "/partials/flash.php");
