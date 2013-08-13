/*
twitter-vine - v - 2013-08-13
Pulls twitter and vine posts with a certain hashtag.
Lovingly coded by Jess Frazelle  - http://frazelledazzell.com/ 
*/
function get_feed(){
	$.ajax({
		url: "ajax-feed.php"
	}).done(function(feed_data) {
		$('.container').empty().html(feed_data);
	});
}


$(document).ready(function(){
	get_feed();

	var refreshFeed = setInterval(function(){
		get_feed();
	}, 300000);
});
