<?php
$filename = 'tedscrapings.txt'; // file to write out
 $hpurl = $_POST['hpurl'];

// get the entire contents
$content = file_get_contents($hpurl);


    $handle = fopen('tedscrapings.txt','a');
	// open a file to write it out to
	
	  fwrite($handle, $content);
	  fclose($handle);




//echo $content;

?>