<?php

include('config.php');
require_once('lib/twitteroauth.php');

function stripEmojis($text){
    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    return $clean_text;
}

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

function shouldUpdate($db, $hashtag){
    $db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);
    $now = time();
    $query = mysqli_query($db_con, "SELECT * FROM media WHERE hashtag='$hashtag' AND source='twitter' ORDER BY source_id DESC LIMIT 1");
    $result = mysqli_fetch_array($query);

    mysqli_close($db_con);

    if (($now - intval($result['time_now'])) >= (2.5*60)){
        return $result['source_id'];
    } else {
        return false;
    }
}

function updateTwitter($db, $twitter, $hashtag){
    $db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);
    $connection = new TwitterOAuth($twitter['consumer_key'], $twitter['consumer_secret'], $twitter['access_token'], $twitter['access_token_secret']);
    $content['twitter'] = $connection->get(
        "search/tweets", array(
            'q' => '#'.$hashtag.' filter:images',
            'since_id' => $twitter['last_id'],
            'include_entities' => true,
            'lang' => 'en',
            'count' => 100
        )
    );

    $content['vine'] = $connection->get(
        "search/tweets", array(
            'q' => '#'.$hashtag.' vine.co filter:links',
            'since_id' => $twitter['last_id'],
            'include_entities' => true,
            'lang' => 'en',
            'count' => 100
        )
    );

    foreach($content as $name => $media){
        if (isset($media->statuses)){
            foreach($media->statuses as $tweet){
                if ($tweet->id > $twitter['last_id'] && (!empty($tweet->entities->urls) || isset($tweet->entities->media))){
                    $twitter_id = $tweet->id;
                    $tweet_id = $tweet->id_str;
                    $created_at = $tweet->created_at;
                    $user_id = $tweet->user->id;
                    $this_name = mysqli_real_escape_string($db_con, stripEmojis($tweet->user->name));
                    $screen_name =  mysqli_real_escape_string($db_con, $tweet->user->screen_name);
                    $user_location = mysqli_real_escape_string($db_con, stripEmojis($tweet->user->location));
                    $text = mysqli_real_escape_string($db_con, stripEmojis($tweet->text));
                    $link_post = 'https://twitter.com/'.$screen_name.'/status/'.$tweet_id;
                    $time_now = time();
                    $is_vine = false;
                    $is_tweet = false;
                    $type = 'photo';
                    if ($name == 'twitter' && isset($tweet->entities->media)){
                        $is_tweet = true;
                        $media_url = $tweet->entities->media[0]->media_url;
                        $media_url_https = $tweet->entities->media[0]->media_url_https;
                    } else if (strpos($tweet->entities->urls[0]->expanded_url,'vine.co') !== false && $name == 'vine' && !empty($tweet->entities->urls) && isset($tweet->entities->urls[0]->expanded_url)){
                        $is_vine = true;
                        $type = 'video';
                        $media_url = $tweet->entities->urls[0]->expanded_url;
                        $dom = new DomFinder($media_url);
                        $video_cell = $dom->find("//meta[@property='twitter:player:stream']", 'content');
                        $picture_cell = $dom->find("//meta[@property='twitter:image']", 'content');
                        $media_url = $picture_cell[0];
                        $media_url_https = $video_cell[0];
                    }
                    if ($is_tweet || $is_vine){
                        if (mysqli_query($db_con,
                            "insert into media (time_now, source_id, created_at, user_id, name, screen_name, user_location, text, media_url, media_url_https, source, type, hashtag, post_url) ".
                            "values('$time_now', '$twitter_id', '$created_at','$user_id','$this_name','$screen_name', '$user_location', '$text', '$media_url', '$media_url_https', 'twitter', '$type', '$hashtag', '$link_post')")){}
                    }
                }
            }
        }
    }

    mysqli_close($db_con);
}

function updateInstagram($db, $instagram, $hashtag){
    $db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);

    // get the last id
    $query = mysqli_query($db_con, "SELECT * FROM media WHERE hashtag='$hashtag' AND source='instagram' ORDER BY source_id DESC LIMIT 1");
    $result = mysqli_fetch_array($query);

    $get_media_url = 'https://api.instagram.com/v1/tags/'.$hashtag.'/media/recent?access_token='.$instagram['access_token'];
    if (isset($result['source_id'])){
        $get_media_url .= '&max_tag_id='.$result['source_id'];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $get_media_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $media = json_decode($response, true);

    foreach($media['data'] as $insta){
        $time_now = time();
        $source_id = $insta['id'];
        $created_at = date('r', $insta['created_time']);
        $user_id = $insta['user']['id'];
        $screen_name = mysqli_real_escape_string($db_con,  $insta['user']['username']);
        $this_name =  mysqli_real_escape_string($db_con, stripEmojis($insta['user']['full_name']));
        $text =  mysqli_real_escape_string($db_con, stripEmojis($insta['caption']['text']));
        $likes = $insta['likes']['count'];

        if ($insta['type'] == 'video'){
            $type= 'video';
            $media_url=$insta['images']['standard_resolution']['url'];
            $media_url_https=$insta['videos']['standard_resolution']['url'];
        } else {
            $type= 'photo';
            $media_url=$insta['images']['standard_resolution']['url'];
            $media_url_https= '';
        }

        if (mysqli_query($db_con,
            "insert into media (time_now, source_id, created_at, user_id, name, screen_name, text, likes, media_url, media_url_https, source, type, hashtag, post_url) ".
            "values('$time_now', '$source_id', '$created_at','$user_id','$this_name','$screen_name', '$text', '$likes', '$media_url', '$media_url_https', 'instagram', '$type', '$hashtag', '$post_link')")){}
    }

    mysqli_close($db_con);
}


function outputFeed($db, $hashtag){
    $html = '<ul class="feed">';
    $db_con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['name']);
    $query = mysqli_query($db_con, "SELECT * FROM media WHERE hashtag='$hashtag' ORDER BY time_now DESC");
    if (mysqli_num_rows($query) > 0) {
        while ($post = mysqli_fetch_assoc($query)) {
            if ($post['type'] == 'photo'){
                $media = '<a href="' . $post['post_url'] . '"><img class="' . $post['source'] . '" src="' . $post['media_url'] . '" alt=""/></a>';
            } else if ($post['media_url_https']!='') {
                $media = '<video width="100%" height="100%" controls poster="'. $post['media_url'] . '">
                    <source src="'. $post['media_url_https'] . '" type="video/mp4">
                    Your browser does not support the video tag.
                    </video>';
            }
            $html .= '<li class="'.$post['source'].' col-sm-3">'.$media.'</li>';
        }
    }
    $html .= '</ul>';
    mysqli_close($db_con);

    return $html;
}

$shouldUpdate = shouldUpdate($db, $hashtag);

if ($shouldUpdate !== false){
    $twitter['last_id'] = $shouldUpdate;
    updateInstagram($db, $instagram, $hashtag);
    updateTwitter($db, $twitter, $hashtag);
}

echo outputFeed($db, $hashtag);


?>
