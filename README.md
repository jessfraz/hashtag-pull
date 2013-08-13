twitter-vine
=======
Get all twitter and vine posts with a certain hashtag, print the feed, and save to a database for future use. See working [example](http://junit.co/twitter-vine/).

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

