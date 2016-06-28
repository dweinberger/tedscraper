<?php

// check the list of urls to make sure they're all in.
// write oiut a list of ones that need to be added

$files = file_get_contents("tedTalkLinks.txt");
$farray = explode(PHP_EOL,$files);

$database_name = "tedtalks";
$tablename = "talks";

print "<p><b>Checking $databasename (table: $tablename) for duplicates...</b></p>"

$o = fopen("missingurls.txt","w");

//Get user and database info from a file you've created like this:
// {
	// "mysql":"PATH TO MYSQL",
	// "username" :"USERNAME FOR MYSQL",
	// "password" : "PASSWORD",
	// "database": "THE PARTICULAR DATABASE YOU'RE USING"
// }
$idinfo = file_get_contents("id.json");
$idjson = json_decode($idinfo,true);

$dbh = new mysqli($idjson['mysql'],$idjson['username'],$idjson['password'],$idjson['database']);
   
		if ($dbh->connect_error > 0){
	 	die('login.php: Unable to connect to database [' . $dbh->connect_error . ']');
	 }
    

$ct = count($farray);
print "<p>Count: $ct </p>";
$yesctr = 0;
$noctr = 0;

for ($i=0; $i < count($farray); $i++){
	$f = $farray[$i];
	// check the file
	$query = "SELECT * FROM `" . $tablename . "` where url=\"$f\";";
		$res = $dbh->query($query);
		if ($res->num_rows > 0){
			$yesctr++;
		}
		else {
			$noctr++;
			print "<li>$f</li>";
			fwrite($o,$f . PHP_EOL);
		}
	}
	
	print "<h2>OK: $yesctr MISSING: $noctr</h2>";
	fclose($o);



?>
