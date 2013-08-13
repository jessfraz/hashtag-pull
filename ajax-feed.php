<?php

include('config.php');
require_once('lib/twitteroauth.php');

class DomFinder {
	function __construct($page) {
		$html = @file_get_contents($page);
		$doc = new DOMDocument();
		$this->xpath = null;
		if ($html) {
			$doc->preserveWhiteSpace = true;
			$doc->resolveExternals = true;
			@$doc->loadHTML($html);
			$this->xpath = new DOMXPath($doc);
			$this->xpath->registerNamespace("html", "http://www.w3.org/1999/xhtml");
		}
	}

	function find($criteria = NULL, $getAttr = FALSE) {
		if ($criteria && $this->xpath) {
			$entries = $this->xpath->query($criteria);
			$results = array();
			foreach ($entries as $entry) {
				if (!$getAttr) {
					$results[] = $entry->nodeValue;
				} else {
					$results[] = $entry->getAttribute($getAttr);
				}
			}
			return $results;
		}
		return NULL;
	}

	function count($criteria = NULL) {
		$items = 0;
		if ($criteria && $this->xpath) {
			$entries = $this->xpath->query($criteria);
			foreach ($entries as $entry) {
				$items++;
			}
		}
		return $items;
	}
}

function shouldUpdate($db){
	$db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);
	$now = time();
	$query = mysqli_query($db_con, "SELECT * FROM media ORDER BY twitter_id DESC LIMIT 1");
	$result = mysqli_fetch_array($query);

	mysqli_close($db_con);

	if (($now - intval($result['time_now'])) >= (2.5*60)){
		return $result['twitter_id'];
	} else {
		return false;
	}
}

function update($db, $twitter){
	$db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);
	$connection = getConnectionWithAccessToken($twitter['access_token'], $twitter['access_token_secret'], $twitter['consumer_key'], $twitter['consumer_secret']);
	$content['twitter'] = $connection->get(
		"search/tweets", array(
			'q' => '#'.$twitter['hashtag'].' filter:images',
			'since_id' => $twitter['last_id'],
			'include_entities' => true,
			'lang' => 'en',
			'count' => 100
		)
	);

	$content['vine'] = $connection->get(
		"search/tweets", array(
			'q' => '#'.$twitter['hashtag'].' vine.co filter:links',
			'since_id' => $twitter['last_id'],
			'include_entities' => true,
			'lang' => 'en',
			'count' => 100
		)
	);

	foreach($content as $name => $media){
		foreach($media->statuses as $tweet){
			if ($tweet->id > $twitter['last_id'] && (!empty($tweet->entities->urls) || isset($tweet->entities->media))){
				$twitter_id = $tweet->id;
				$created_at = $tweet->created_at;
				$user_id = $tweet->user->id;
				$this_name = mysqli_real_escape_string($db_con, $tweet->user->name);
				$screen_name = $tweet->user->screen_name;
				$user_location = $tweet->user->location;
				$text = mysqli_real_escape_string($db_con, $tweet->text);
				$time_now = time();
				$is_vine = false;
				$is_tweet = false;
				if ($name == 'twitter' && isset($tweet->entities->media)){
					$is_tweet = true;
					$media_url = $tweet->entities->media[0]->media_url;
					$media_url_https = $tweet->entities->media[0]->media_url_https;
				} else if (strpos($tweet->entities->urls[0]->expanded_url,'vine.co') !== false && $name == 'vine' && !empty($tweet->entities->urls) && isset($tweet->entities->urls[0]->expanded_url)){
					$is_vine = true;
					$media_url = $tweet->entities->urls[0]->expanded_url;
					$dom = new DomFinder($media_url);
					$content_cell = $dom->find("//meta[@property='twitter:player:stream']", 'content');
					$media_url_https = $content_cell[0];
				}
				if ($is_tweet || $is_vine){
					mysqli_query($db_con, 
						"insert into media (time_now, twitter_id, created_at, user_id, name, screen_name, user_location, text, media_url, media_url_https, source) ".
						"values('$time_now', '$twitter_id', '$created_at','$user_id','$this_name','$screen_name', '$user_location', '$text', '$media_url', '$media_url_https', '$name')") or die(mysqli_error($db_con));
				}
			}
		}
	}

	mysqli_close($db_con);
}

function outputFeed($db){
	$html = '<ul class="feed">';
	$db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);
	$query = mysqli_query($db_con, "SELECT * FROM media ORDER BY twitter_id DESC");
	if (mysqli_num_rows($query) > 0) {
		while ($post = mysqli_fetch_assoc($query)) { 
			if ($post['source'] == 'twitter'){
				$media = '<img src="' . $post['media_url'] . '" alt=""/>';
			} else {
				$media = '<video width="320" height="320" controls>
					<source src="'. $post['media_url_https'] . '" type="video/mp4">
					Your browser does not support the video tag.
					</video>';
			}
			$html .= '<li class="'.$post['source'].'">'.$media.'</li>';
		}
	}
	$html .= '</ul>';
	mysqli_close($db_con);
	
	return $html;
}

$shouldUpdate = shouldUpdate($db);
if ($shouldUpdate !== false){
	$twitter['last_id'] = $shouldUpdate;
	update($db, $twitter);
}

echo outputFeed($db);


?>
