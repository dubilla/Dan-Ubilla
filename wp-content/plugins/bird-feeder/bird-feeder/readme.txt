=== Bird Feeder ===
Contributors: ajaswa
Tags: twitter, posts
Donate link: http://andrewjaswa.com
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: trunk

Tweets about your posts when they are published.

== Description ==

This plug-in serves one purpose and that is to tweet published posts. It doesn't do anything other then tweet. It tweets in this format: [your message] [post title] [short url].

On the options page you will have to enter your twitter username and password. You can also configure your message there.

If you try to publish a bunch of posts quickly Bird Feeder url shortening service will not handle them and result un-expected tweets. Your mileage may vary. Don't spam. 

Current: 1.2.2

== Installation ==

This section describes how to install the plugin and get it working.

1. Extract bird-feeder.zip to `/wp-content/plugins/bird-feeder/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Setup your twitter account on the options page.
1. Publish a post!
1. See tweet.

== Frequently Asked Questions ==

= Will this do anything other then tweet? =

No.

= What about some other function? =

See above.


== Change Log ==

Change log:

1.2.2 Updated the password field to be type="password". Fixed some stability issues with tweeting. Updated the shortna.me api call. 

1.2.1 Ver 1.2 broke the "Press This" Bookmarklet. It's now fixed. Thanks to @RobertBasil for catching this.

1.2 Added a few more checks to make sure the short url hash returns and gets tweeted. No longer tweets on page publishes. Cleaned up the options page. 

1.1.8 Added option to allow for preview URLs. Especially because of what has been going on with twitter lately. Thanks for the idea from Ben Clapton (http://benclapton.id.au)

1.1.7 Added Twitter source to Bird Feeder. YAY!

1.1.6 Now works with Quickpress. May also work with the iPhone app., need to do further testing. 

1.1.5 Added option to allow use of url that was used for the install of Wordpress. Uses bloginfo('url').
 
1.1.4 Fixed a few instances of misspelling of Bird Feeder.

1.1.3 Added checks to make sure that the user name and password are set before trying to tweet.

1.1.2 Added support for long post titles. Now truncates post titles if the entire tweet is longer then what Twitter allows.