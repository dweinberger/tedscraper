<?php

// how often tags are used with the technology tag?
// Creates CSV file.

ini_set('display_errors', 'On');
error_reporting(E_ALL);




print "<h1>tags with neighbors</h1>";
// get the tag table
$tagsjsonfile = file_get_contents("tags.json");
$tagsjson =  json_decode($tagsjsonfile);
// get the talks table
$talksjsonfile = file_get_contents("tedtalks.json");
//print $jsonfile; // = addslashes($jsonfile);
$talksjson =  json_decode($talksjsonfile);
print "<br>count:" . count($talksjson);;

$ctr = 1;
$tagctr = array();
foreach($talksjson as $talk){
	$tagstring = $talk -> tags; // get the tag string
	if (strpos($tagstring, "technology")){
		//print "<hr><li>tagstring #$ctr $tagstring</li>";
		$ctr++;
		// turn string into array
		$tags = explode(",", $tagstring);
		// add each to array
			//print "<p>count: ". count($tags);
		for ($i=0; $i < count($tags); $i++){
		//print "<p>## $i</p>";
		  $tag = $tags[$i];
		 // print "<li>tag # $i: $tag</li>";
		  if(!isset($tagctr[$tag])){ 
			  $tagctr[$tag]=1;
			}
		  else {
			  $howmany = $tagctr[$tag];
			  $tagctr[$tag] = $howmany + 1; 
		  }
		 //  print "tagarray: $tag : " . $tagctr[$tag] . "<br>";
		  
	}
	}
	
}

print "<HR><HR>KEYS<HR>";
// create CSV file
$f = fopen("technologycohort.csv","w");
$i = 0;
foreach($tagctr as $key => $value){
	print "<p>$key : $value </p>";	
	fwrite($f,$key . "," . $value . PHP_EOL);
}
fclose($f);




?>