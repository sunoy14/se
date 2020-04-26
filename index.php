<?php
$start = "http://localhost/se/test.html";

$already_crawled = array();

function get_details($url){
	$options = array('http'=>array('method'=>'GET', 'headers'=>"User-Agent: howBot/0.1\n"));
	$context = stream_context_create($options);

	$doc = new DOMDocument();
	@$doc->loadHTML(@file_get_contents($url, false, $context));

	$title = $doc->getElementsByTagName('title');
	$title = $title->item(0)->nodeValue;

	echo $title . "\n";
}

function follow_links($url){
	global $already_crawled;

	$options = array('http'=>array('method'=>'GET', 'headers'=>"User-Agent: howBot/0.1\n"));
	$context = stream_context_create($options);

	$doc = new DOMDocument();
	$doc->loadHTML(file_get_contents($url, false, $context));

	$linklist = $doc->getElementsByTagName("a");
	foreach($linklist as $link){
		$l = $link->getAttribute('href')."\n";

		if(substr($l, 0, 1) == '/' && substr($l, 0, 2) != '//'){
			$l = parse_url($url)['scheme'] . '://' . parse_url($url)['host'] . $l;
		}
		else if(substr($l, 0, 2) == '//'){
			$l = parse_url($url)['scheme'] . ':' . $l;
		}
		else if (substr($l, 0, 2) == './'){
			$l = parse_url($url)['scheme'] . '://' . parse_url($url)['host'] . dirname(parse_url($url)['path']) . substr($l, 1); 
		}
		else if (substr($l, 0, 1) == '#'){
			$l = parse_url($url)['scheme'] . '://' . parse_url($url)['host'] . parse_url($url)['path'] . $l;
		}
		else if (substr($l, 0, 3) == '../'){
			$l = parse_url($url)['scheme'] . '://' . parse_url($url)['host'] . '/' . $l;
		}
		else if (substr($l, 0, 11) == 'javascript:'){
			continue;
		}
		else if (substr($l, 0, 5) != 'https' && substr($l, 0, 4) != 'http'){
			$l = parse_url($url)['scheme'] . '://' . parse_url($url)['host'] . '/' . $l;
		}

		if(!in_array($l, $already_crawled)){
			$already_crawled[] = $l;
			echo get_details($l);
			//echo $l . "\n";
		}
	}
}

follow_links($start);

print_r($already_crawled);
?>
