<?php

// cycles through ted.com page that lists talks extracting urls of the TED talk pages.

// http://simplehtmldom.sourceforge.net/manual.htm#section_quickstart
include "simple_html_dom.php";

$tedbase = "http://www.ted.com/talks?page="; // where TED's list of talks is
$maxpagestofetch = 100; 
// open file

$f = fopen("tedTalkLinks.txt","w");

$urls = array(); // master array of links

// cycle through known pages
for ($page=1; $page < $maxpagestofetch; $page++){
	// get the page
	$html = file_get_html($tedbase . $page);

	// get the container div
	$container = $html->find('div[id=browse-results]'); 
	
	// get the links to talks
	foreach($container[0]->find('a') as $element) {
		$href = $element->href;
		// check for dupe since thumbnails 
		// have the same link as the ted talk page
		if ( in_array($href,$urls) == false){ 
			// skip links to the next page of listings
			if (strpos($href,"/talks?page=") === false){	
			// add the link to the array
			$urls[] = $href;
			//print "http://www.ted.org" . $element->href . '<br>';
			}
		}
	}
	print "Page: $page. Number of links: " . count($urls) . "<br>";
}
// write the urls out to a file
for ($a = 0; $a < count($urls); $a++){  
	fwrite($f,"http://www.ted.com" . $urls[$a] . ".html");
	if ($a != count($urls) - 1){
		fwrite($f,"\n");
	}
}     

fclose($f);

?>