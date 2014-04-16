### WARNING
This is not working with the latest implementations of the APIs, but if you want to use this as a base, go right ahead.


### Twitter, Vine, and Instagram Hashtag Feed
Get all Twitter and Vine posts with a certain hashtag, print the feed, and save to a database for future use. See working [example](http://lab.jessfraz.com/hashtag-pull/).

## How it works
- Searches Twitter for images with the hashtag and saves them to the database.
- Searches Twitter for links with the hashtag and a link containing ```vine.co```, parses the link's contents for the ```twitter:player:stream``` and ```twitter:image``` meta properties and saves the mp4 video link and video placeholder to the database.
- Only performs the above API calls every 2 minutes to avoid rate limiting.
- Shows the feed of the pictures and videos from the database, and refreshes the feed every five minutes.

### Configurations
- Set the hashtag to be pulled in [config.php](https://github.com/jfrazelle/hashtag-pull/tree/master/config.php).
- Backups are located [here](https://github.com/jfrazelle/hashtag-pull/tree/master/db).

	- Place database credentials into [config.php](https://github.com/jfrazelle/hashtag-pull/tree/master/config.php).

### Create a Twitter application
1. Sign in with [Twitter Developer](https://dev.twitter.com/)
2. Hover over your name in the top right corner then click "My Applications"
3. Create a New Application. Enter a name (this is for your reference), a description (again for your reference), and your site's URL. The callback URL is a moot point for the use of the application so it can be left blank.
4. Create my Access Token (this is a button, click it)
5. View the details tab. Copy and paste the correlating keys and secrets into [config.php](https://github.com/jfrazelle/hashtag-pull/tree/master/config.php).

### Create an Instagram application
1. Sign in with [Instagram Developer](http://instagram.com/developer/)
2. Click "Register Your Application" (this is a button, click it).
3. Create a New Application. Enter a name (this is for your reference), a description (again for your reference), your site's URL, and the url for the directory ```instagram``` where your app will live for the redirect uri.
4. Create my Access Token (this is a button, click it)
5. View the details tab. Copy and paste the correlating keys and secrets into [config.php](https://github.com/jfrazelle/hashtag-pull/tree/master/config.php).
6. Upload the app to your sever and navigate to the instagram directory from your browser.
7. Authenticate your app.
8. Copy and paste the value for the access token into the access token for
   instagram field in [config.php](https://github.com/jfrazelle/hashtag-pull/tree/master/config.php).

### Recommendations
Since this is using a work around for what will eventually be replaced by the Vine API, I would suggest using a cron job for the ```update()``` function and only calling from the database when a visitor hits the server. This should reduce load time and make sure the server is not over worked.

### Build Instructions
This project uses [Grunt](http://gruntjs.com) to automate build tasks (eg. compile less and minimize js).
- Install [Node.js](http://nodejs.org)
- Install grunt-cli: `npm install -g grunt-cli`
- Install dev dependencies: `npm install`
- Run `grunt` to compile, or `grunt server` to start a live development environment.


[![Analytics](https://ga-beacon.appspot.com/UA-29404280-16/hashtag-pull/README.md)](https://github.com/jfrazelle/hashtag-pull)
