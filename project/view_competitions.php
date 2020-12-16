<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
//we'll put this at the top so both php block have access to it
if(isset($_GET["id"])){
        $id = $_GET["id"];
}
?>

<?php
     $result = [];
     $db = getDB();
     $stmt = $db->prepare("SELECT name, created, expires FROM F20_Competitions WHERE id = :id");
     $stmt->execute(["id" => $id]);
     $result = $stmt->fetch(PDO::FETCH_ASSOC);
     $stmt->errorInfo();
     if(!$result){
        $e = $stmt->errorInfo();
        flash($e[2]);
	}
     $name = $result["name"];
     $created = $result["created"];
     $expires = $result["expires"];
?>

<?php
     $result = [];
     $params[":created"] = $created;
     $params[":expires"] = $expires;
     $db = getDB();
     $stmt = $db->prepare("SELECT score.id,score,username,score.created FROM Scores as score JOIN Users on score.user_id = Users.id WHERE score.created BETWEEN :created AND :expires ORDER BY score DESC, score.created ASC LIMIT 10");
     $stmt->execute($params);
     $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $stmt->errorInfo();
     if(!$scores){
        $e = $stmt->errorInfo();
        flash($e[2]);
        }
?>
<h3> <?php echo $name ?>: Top 10 Scoreboard</h3>
<?php if (isset($scores) && !empty($scores)): ?>
      <div class="card">
      <div class="card-body">
        <div>
            <?php foreach ($scores as $r): ?>
            <div> User: <?php safer_echo($r["username"]); ?></div>
            <div> Score: <?php safer_echo($r["score"]); ?></div>
            <div> Time Achieved: <?php safer_echo($r["created"]); ?></div>
            <br>
            <?php endforeach; ?>
        </div>
     </div>
   </div>
<?php else: ?>
 <p> No Results Found </p>
<?php endif; ?>

<?php require(__DIR__ . "/partials/flash.php");
