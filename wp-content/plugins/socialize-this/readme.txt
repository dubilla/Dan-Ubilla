=== Plugin Name ===
Contributors: Rogem002, fob

Tags: social, icons, twitter, tinyurl, posts, page, admin, links, widgets, bitly, bit.ly, facebook, like, @anywhere

Requires at least: 2.0.2

Tested up to: 3.0

Adds Social Widgets (icons) to your blog posts. It also can update your twitter status when you publish a post.

== Description ==
<p>Socialize This is a WordPress plugin that allows the user to easily generate Social Widgets (Icons) and spread to word about their blog posts. It works out-of-the-box with little to no configuration required. More information can be found at http://www.fullondesign.co.uk/socialize-this</p>
<p>Current Version: 1.6.4<br />
Last Updated: 27th April 2010</p>
<h3>Key Features</h3>
<ul>
	<li>Twitter integration &#8211; It can inform your follows of your new blog posts when you publish them.</li>
	<li>URL Shortening integration &#8211; Automatically generates shortened URL&#8217;s for your posts.</li>
	<li>Support for 13 Social Websites, including Digg, StumbleUpon, LinkedIn and Facebook.</li>
	<li>Fully Customizable with Integrated Templates.</li>
	<li>No Link Back Required.</li>
	<li>Bit.ly Support</li>
	<li>Released under the GPL v.2</li>
</ul>
<h3>Requirements</h3>
<ul>
	<li>PHP5 or better &#8211; This was built in PHP5 for PHP5. Running it in PHP4 may cause compatibility problems.</li>
	<li>cURL must be enabled.</li>
</ul>
<h3>Translations</h3>
<ul>
	<li>Russian &#8211; <a href="http://www.comfi.com/">M.Comfi</a></li>
	<li>Belorussian &#8211; <a href="http://antsar.info/">Ilyuha</a></li>
	<li>Albanian &#8211; <a href="http://www.wp-globe.com">WP-Globe</a></li>
	<li>German &#8211; <a href="http://www.fob-marketing.de/">Oliver Bockelmann</a></li>
        <li>Want to help translate Socialize This? <a href="http://www.fullondesign.co.uk/contact-me">Contact Me!</a></li>
</ul>
== Installation ==

1. Upload `socialize-this` folder to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. (Optional) Place `<?php show_social_widgets(); ?>` in your template.

<p>Note: If you are running PHP4 some errors may occur, this plugin was designed for PHP5.</p>
== Frequently Asked Questions ==
<p>FAQ's can be found at: http://www.fullondesign.co.uk/socialize-this</p>
== Screenshots ==

1. The Settings page

2. The Widgets page 

3. A sample of the widgets available.

== Changelog ==
= 1.6.6 =
* Fix JQuery bug where Twitter username/pass box would not appear.
= 1.6.5 =
* Fix by Oliver Bockelmann when bit.ly adds extra blank data to the end of URL.
* Oliver Bockelmann also added the German translation
= 1.6.4 =
* Added support for @anywhere.
* Fixed a bug which in some cases caused the "Edit Post" page to break - Thanks to <a href="http://dimhorizonstudio.com/wordpress/">http://dimhorizonstudio.com/wordpress/</a> for the heads up.
= 1.6.3 =
* Added support for bit.ly. Improved UI. Added a few more icon sets.
= 1.6.2 =
* Fix for upcomming "show just the icon I want" feature.
= 1.6.1 =
* Added %raw_permalink% tag. 
* Improved templating system (Widgets can now have their own custom templates). 
= 1.6.0 =
* Made Socialize This more "Widget" focused. Improved the backend coding. Added more url shortening services. Added "Advanced Functions" and a donate button. <br />I also made the social icons appear at the start of the comments, instead of at the end of the loop.
= 1.5 =
* You can now change the icons order and I’ve added support for “Icon Sets”.
= 1.4 =
* Fixed RSS feed validation problems.
= 1.3 =
* Fixed the update post twitter problem, where when you updated a post it was twittered again.
= 1.2 =
* Adjusted the environment which PHP runs in. I also added localization support.
= 1.1 =
* Fixed some PHP4 compatibility problems.
= 1.0 =
* Initial version