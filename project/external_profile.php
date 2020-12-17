<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
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
$db = getDB();
if (isset($id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * from Users where id = :id");
    $r = $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $e = $stmt->errorInfo();
        flash($e[2]);
    }
}
?>

<?php if (isset($result) && !empty($result)): ?>
    <div class="card">
	<h3> <?php safer_echo($result["username"]); ?>'s Profile </h3>
        <div class="card-body">
		<div><?php if ($result["privacy"] == 0): ?> Email: <?php safer_echo($result["email"]); ?> <?php endif; ?> </div>
                <div><?php if ($result["privacy"] == 1): ?> Email: hidden <?php endif; ?> </div>
		<div> Acc. Created: <?php safer_echo($result["created"]); ?> </div>
	</div>
    </div>
<?php else: ?>
 <p>Error finding points...</p>
<?php endif; ?>

<?php
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}
$db = getDB();
$stmt = $db->prepare("SELECT count(score) as total from Scores where user_id = :id LIMIT 10");
$stmt->execute([":id"=>$id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$total = 0;
if($r){
    $total = (int)$r["total"];
}
$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page;
$stmt = $db->prepare("SELECT score from Scores where user_id = :id LIMIT :offset, :count");
//need to use bindValue to tell PDO to create these as ints
//otherwise it fails when being converted to strings (the default behavior)
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", $id);
$stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="container-fluid">
    <h3> <?php safer_echo($result["username"]); ?>'s Last 10 Scores</h3>
    <div class="row">
    <div class="card-group">
<?php if($results && count($results) > 0):?>
    <?php foreach($results as $r):?>
        <div class="col-auto mb-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
		    <div>
			Score: <?php safer_echo($r["score"]); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>

<?php else:?>
<div class="col-auto">
    <div class="card">
       You don't have any scores.
    </div>
</div>
<?php endif;?>
    </div>
    </div>
        <nav aria-label="Last 10 Scores">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
                </li>
                <?php for($i = 0; $i < $total_pages; $i++):?>
                <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>

<br>

<?php require(__DIR__ . "/partials/flash.php");
