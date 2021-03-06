<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// list of Ted Talk pages, generated by getListOfUrls.php
// Filename is tedTalksList.txt
// IF YOU ARE RUNNING THIS A SECOND TIME in order to check if there
// are any missing links (as generated by checklist.php), then replace 
// "tedTalksList.txt" with "missingurls.txt"
$listOfUrls="tedtTalksList.txt"; 
// where in the list you want to start and end
$start = 0;
$end = 256;

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
		
//http://simplehtmldom.sourceforge.net/
//documentation: http://simplehtmldom.sourceforge.net/manual.htm
include "simple_html_dom.php";

//http://stackoverflow.com/questions/574805/how-to-escape-strings-in-sql-server-using-php
function ms_escape_string_UNUSED($data) {
        if ( !isset($data) or empty($data) ) return '';
        if ( is_numeric($data) ) return $data;

        $non_displayables = array(
            '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',             // url encoded 16-31
            '/[\x00-\x08]/',            // 00-08
            '/\x0b/',                   // 11
            '/\x0c/',                   // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ( $non_displayables as $regex )
            $data = preg_replace( $regex, '', $data );
        $data = str_replace("'", "''", $data );
        return $data;
    }

// overall array
$talks = array();

// get the file of tedtalk urls
$talkurlspage = file_get_contents($listOfUrls);
// turn the list of urls into an array
$talkurls = explode(PHP_EOL,$talkurlspage);

print "<p>Scraping " . count($talkurls) . ".</p>";

// go through each page

for ($i=$start; $i < $end; $i++){ // count($talkurls)

	$file = $talkurls[$i];

	$html = file_get_html($file);

	// get author
	$meta = $html->find('[name=author]'); 
	if (isset($meta[0])){
		$author = $meta[0]->getAttribute('content');
	}
	else {$author = "NO AUTHOR";}

	// get title
	$meta = $html->find('[itemprop=name]'); 
	if (isset($meta[0])){
		$title = $meta[0]->getAttribute('content');
	}
	else{
		$title="NO TITLE";
	}

	// get date
	$date = "";
	$div = $html->find('[class=player-hero__meta]'); 
	if (isset($div[0])){
		$divtext = $div[0]->innertext;
		// find the end of the prior span
		$p = strpos($divtext, "'player-hero__meta__label'");
		if ($p > -1){
			$p2 = strpos($divtext,"</strong>", $p);
			if ($p2 > -1){
				$p3 = strpos($divtext,"</span>", $p2);
				if ($p3 > -1){
					$date = substr($divtext, $p2 + 9, $p3 - ($p2 + 9));
				}
			}
		}
	}
	else {
		$date = "";
	}
	print "<p>DATE: $date</p>";

	// get tags
	$meta = $html->find('[name=keywords]'); 
	// returns the entire meta. Look for attribute "content"
	$tags = $meta[0]->getAttribute('content');
	$tagarray = explode(",",$tags);

	// get description
	$meta = $html->find('[itemprop=description]'); 
	if (isset($meta[0])){
		$description = $meta[0]->getAttribute('content');
	}
	else{
		$description = "";
	}

	// get times shared
	$span = $html->find('[class=talk-sharing__value]'); 
	$shared = $span[0]->plaintext;

	// get link to transcript
	$a = $html->find('[class=talk-more__link]');

	if (!empty($a)){
		$transcripturl = "http://www.ted.com" . $a[0]->getAttribute('href');
	}
	else {
		$transcripturl = "";
	}
	
	// get transcript
	if ($transcripturl !== ""){
		$transcript = "";
		$trans = file_get_html($transcripturl);
		// get all the transcript paragraphs
		if (isset($trans)){
			$paras = $trans->find('[class=talk-transcript__para__text]');
		}
		else{
			$paras = array(); // make empty array
		}
		//print "<br>para: " . $paras[1] . "<br>";
		$text = "";
		foreach($paras as $p) {
			// get all spans within each paragraph
			$text = "";
			foreach($p->find('[class=talk-transcript__fragment]') as $span){
				$text = $text . $span->plaintext; 
			}
			$transcript = $transcript . "<p>" . $text . "</p>";
		}
		
	}

	print "<p><i><b>$i: $title: $file</b></i></p>";
	//print $author . "<br>";
	//print $title . "<br>";
	//print $date . "<br>";
	//print $description . "<br>";
	//print $shared . "<br>";
	//print "tags: " . $tags  . "<br>";
	//print "transcripturl: " . $transcripturl . "<br>";
	//print "<div style='font-size:0.8em'>transcript: " . $transcript . "</div>";
	
	// add to the table
	$transcriptsql =  $dbh->real_escape_string($transcript); 
	$transcripturlsql = $dbh->real_escape_string($transcripturl);// 
	$descriptionsql = $dbh->real_escape_string($description);
	$authorsql =  $dbh->real_escape_string($author);
	$titlesql =  $dbh->real_escape_string($title);
	$datesql =  $dbh->real_escape_string($date);
	$tagssql =  $dbh->real_escape_string($tags);
	$query = "INSERT into talks
				(
				url, 		
				author,
				title,
				ddate,
				description,
				transcript_url,
				transcript,
				times_shared,
				tags
				)
		values (
				'$file', 
				'$authorsql',
				'$titlesql',
				'$datesql',
				'$descriptionsql',
				'$transcripturlsql',
				'$transcriptsql',
				'$shared',
				'$tagssql'
				);";

				
		$res = $dbh->query($query);
		 $thisrec = mysqli_insert_id($dbh); // capture most reently created row
		if (!$res) {
			print "<p style='color:red'><b>ERROR " . mysqli_error($dbh) . "</b></p>";
			
		} else {
	       
	        print "<p>------#$i rec:$thisrec </p>";
		}
		
	// go through tags array, creating row in tags table for each tag
	if ($tags !== ""){
			for ($t=0; $t < count($tagarray); $t++){
				$query = "INSERT into tags
				(
				tag,
				talkid
				)
		values (
				'$tagarray[$t]',
				'$thisrec')";

		$res = $dbh->query($query);
		
		if (!$res) {
				print "<p style='color:red'><b>ERROR ON TAGS: " . mysqli_error($dbh) . "</b></p>";	
			} 
			else{
				//print "tag added: $tagarray[$i] rec: $thisrec";
			}
		}
	}

// write out a json file, if you want	
	// build json
	
	$talks[] = array(	"url"=>$file,
						"author"=>$author,
						"title"=>$title,
						"date"=>$date,
						"description"=>$description,
						"times_shared"=>$shared,
						"tags"=>$tags,
						"transcript"=>$transcript,
						"transcript_url"=> $transcripturl
					);
}

	$json = json_encode($talks);
	$f = fopen("tedtalks.json","w");
	fwrite($f,$json);
	fclose($f);

?>
