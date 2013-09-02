/*
hashtag-pull - v - 2013-09-01
Pulls twitter, vine, and instagram posts with a certain hashtag.
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
