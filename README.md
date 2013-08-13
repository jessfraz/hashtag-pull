twitter-vine
=======
Get all twitter and vine posts with a certain hashtag, print the feed, and save to a database for future use. See working [example](http://junit.co/twitter-vine/).

## How it works
- Searches Twitter for images with the hashtag and saves them to the database.
- Searches Twitter for links with the hashtag and a link containing ```vine.co```, parses the link's contents for the ```twitter:player:stream``` meta property and saves the mp4 video link to the database.
- Only performs the above API calls every 2 minutes to avoid rate limiting.
- Shows the feed of the pictures and videos from the database, and refreshes the feed every five minutes.

### Hashtag
Set the hashtag to be pulled in [config.php](https://github.com/jfrazelle/twitter-vine/tree/master/config.php).

### Database
Backups are located [here](https://github.com/jfrazelle/twitter-vine/tree/master/db).
- Place database credentials into [config.php](https://github.com/jfrazelle/twitter-vine/tree/master/config.php).

### Create a Twitter application
1. Sign in with [Twitter Developer](https://dev.twitter.com/)
2. Hover over your name in the top right corner then click "My Applications"
3. Create a New Application. Enter a name (this is for your reference), a description (again for your reference), and your site's URL. The callback URL is a moot point for the use of the plugin so it can be left blank.
4. Create my Access Token (this is a button, click it)
5. View the details tab. Copy and paste the correlating keys and secrets into [config.php](https://github.com/jfrazelle/twitter-vine/tree/master/config.php).

### Build Instructions
This project uses [Grunt](http://gruntjs.com) to automate build tasks (eg. compile less and minimize js).
- Install [Node.js](http://nodejs.org)
- Install grunt-cli: `npm install -g grunt-cli`
- Install dev dependencies: `npm install`
- Run `grunt` to compile, or `grunt server` to start a live development environment.

