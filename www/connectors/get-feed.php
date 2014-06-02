<?php

require_once('../../app/start.php');

\core\FrontController::Connect();

$url = false;

if($_GET['q']) {
	$url = "http://news.google.co.uk/news/feeds?hl=en&gl=uk&q=".urlencode($_GET['q'])."&um=1&ie=UTF-8&output=rss";
}


if($_GET['url']) {
	
	$_GET['url'] = str_replace(" ","+",$_GET['url']);
	
	if(filter_var($_GET['url'],FILTER_VALIDATE_URL)) {
		$url = $_GET['url'];
	}
}

if(!$url) {
	$data = array("error"=>"Could not build a valid feedd URL");
} else {

	//echo $url;

	$p = new \libs\xml\xml2json();

	$p->getFromURL($url);

	$json = $p->getJSON();

	$data = json_decode($json,1);

	for($i=0;$i<count($data['channel']['item']);$i++) {
		$data['channel']['item'][$i]['description'] = strip_tags($data['channel']['item'][$i]['description']);
	}
}
echo json_encode($data);