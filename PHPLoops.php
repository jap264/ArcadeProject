<?php

//Jansen Pimentel
//IT202011 Matt Toegel

$numbers = array(0,1,2,3,4);

foreach($numbers as &$value){
	if($value % 2 == 0){
		echo $value."<br>\n";
	}
}

?>