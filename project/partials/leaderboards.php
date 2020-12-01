<?php
if(!isset($type)){
    flash("Type variable is not set");
}

$currentdate = date("Y-m-d H:i:s");
$weekagodate = date("Y-m-d H:i:s", strtotime($currentdate.'- 7 days'));
$monthagodate = date("Y-m-d H:i:s", strtotime($currentdate.'- 30 days'));
$params = [];
switch($type){
     case "weekly":
         $params[":currentdate"] = $currentdate;
         $params[":weekagodate"] = $weekagodate;
         $query = "SELECT score.id,username,score.created,score FROM Scores as score JOIN Users on score.user_id = Users.id WHERE score.created BETWEEN :weekagodate AND :currentdate ORDER by score DESC, score.created ASC LIMIT 10";
     break; 
     case "monthly":
         $params[":currentdate"] = $currentdate;
         $params[":monthagodate"] = $monthagodate;
         $query = "SELECT score.id,username,score.created,score FROM Scores as score JOIN Users on score.user_id = Users.id WHERE score.created BETWEEN :monthagodate AND :currentdate ORDER by score DESC, score.created ASC LIMIT 10";
     break;
     case "lifetime":
         $query = "SELECT score.id,username,score.created,score FROM Scores as score JOIN Users on score.user_id = Users.id ORDER by score DESC, score.created ASC LIMIT 10";
     break;
     default:
         flash("That leaderboard is not valid");
     break;
}
if(isset($query)){
     $db = getDB();
     $stmt = $db->prepare($query);
     $stmt->execute($params);
     $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $stmt->errorInfo();
     if(!$scores){
        $e = $stmt->errorInfo();
        flash($e[2]);
}
}
?>

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
