<?php


// Sample query
$query = "SELECT * from talks WHERE (tags LIKE '%green%') OR (tags LIKE '%sustainab%') OR (tags LIKE '%ecolog%')  OR (tags LIKE '%environment%') OR (tags LIKE '%conservation%') OR (tags LIKE '%conserving%') OR (tags LIKE '%climate change%') OR (tags LIKE '%resources%')";


// name of file of results to write
$outfile_name="tedTalks.txt";

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
 
    
    
    // run the query
    $result = $dbh->query($query);
    if ($result->num_rows > 0) {
    // output data of each row
    $rows = array();
    print "<p>Writing file...</p>";
    while($row = $result->fetch_assoc()) {
      //  echo "title: " . $row["title"]. " - Name: " . $row["author"]. " " . $row["description"]. "<br>";
        $rows[]= $row;
    }
} else {
    echo "0 results";
}

print "<p>Done getting results. Count: " . count($rows) . ". Writing results to $outfile_name</p>";
//print json_encode($rows);
   
$f = fopen($outfile_name, "w");
fwrite($f, json_encode($rows)); 
fclose($f);

print"<p>$outfile_name written.</p>";
    

?>
