<?php
date_default_timezone_set('America/New_York');

$twitter = array(
    'consumer_key' =>           'YOUR_CONSUMER_KEY',
    'consumer_secret' =>        'YOUR_CONSUMER_SECRET',
    'access_token' =>           'YOUR_ACCESS_TOKEN',
    'access_token_secret' =>    'YOUR_TOKEN_SECRET'
);

$instagram = array(
    'client_id' =>       'YOUR_CLIENT_ID',
    'client_secret' =>  'YOUR_CLIENT_SECRET',
    'website_url' =>    'http://YOUR_DOMAIN.co/hashtag-pull/instagram',
    'redirect_uri' =>   'http://YOUR_DOMAIN.co/hashtag-pull/instagram',
    'access_token' =>   'YOUR_ACCESS_TOKEN'
);

$db = array(
    'host' =>       '127.0.0.1',
    'user' =>       'root',
    'password' =>   '',
    'name' =>       'hashtag_pull'
);

$hashtag = 'legendsofsummer';

?>
