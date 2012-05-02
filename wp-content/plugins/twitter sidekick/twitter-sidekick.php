<?php
/*
Plugin Name: Twitter Sidekick
Version: 0.4
Plugin URI: http://www.danubilla.com
Description: Displays your latest public Twitter message. Based on <a href="http://rick.jinlabs.com/code/twitter">Twitter for Wordpress</a> by <a href="http://rick.jinlabs.com/">Rick</a>.
Author: Dan Ubilla
Author URI: http://www.danubilla.com
*/

/*  Copyright 2009  Dan Ubilla (dan.ubilla[at]gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
function twitter_me() {
	$here = 'here';
	return $here;
}
function twitter_feed($u){

//URL encode the query string
$user = urlencode($u);

//request URL
$request = "http://search.twitter.com/search.atom?q=from%3A$user&rpp=1";

$curl = curl_init();

curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

curl_setopt($curl,CURLOPT_URL,$request);

$response = curl_exec($curl);

curl_close($curl);

//remove "twitter:" from the $response string
$response = str_replace("twitter:","",$response);

//convert response XML into an object
$xml = simplexml_load_string($response);

//loop through all the entry(s) in the feed
for($i=0;$i<count($xml->entry);$i++){

	//get the id from entry
	$id = $xml->entry[$i]->id;
	
	//explode the $id by ":"
	$id_parts = explode(":",$id);
	
	//the last part is the tweet id
	$tweet_id = array_pop($id_parts);
	
	//get the account link
	$account_link = $xml->entry[$i]->author->uri;
	
	//get the image link
	$image_link = $xml->entry[$i]->link[1]->attributes()->href;
	
	//get name from entry and trim the last ")"
	$name = trim($xml->entry[$i]->author->name,")");
	
	//explode $name by the rest "(" inside it
	$name_parts = explode("(",$name);
	
	//get the real name of user from the last part
	$real_name = trim(array_pop($name_parts));
	
	//the rest part is the screen name
	$screen_name = trim(array_pop($name_parts));
	
	//get the published time, replace T and Z with " " and trim the last " "
	$published_time = trim(str_replace(array("T","Z"),"",$xml->entry[$i]->published));
	
	//get the published date
	$published_date = substr($published_time,0,10);
	
	//get the REAL published time
	$published_time = substr($published_time,10,5);
	$published_hour = substr($published_time,0,2);
	$published_minutes = substr($published_time,2,3);
	$published_hour = (int)$published_hour;
	$published_hour = $published_hour - 6;
	if($published_hour < 0) $published_hour = 24 + $published_hour;
	if($published_hour > 12){
		$published_hour = $published_hour - 12;
		$meridiem = 'pm';
	} else
		$meridiem = 'am';
	$published_time = $published_hour.$published_minutes.' '.$meridiem;
	
	//get the status link
	$status_link = $xml->entry[$i]->link[0]->attributes()->href;
	
	//get the tweet
	$tweet = $xml->entry[$i]->content;
	
	//remove <b> and </b> from the tweet. If you want to show bold keyword then you can comment this line
	$tweet = trim(str_replace(array("<b>","</b>"),"",$tweet));
	
	//get the source link
	$source = $xml->entry[$i]->source;
	
	//the result that holds the information
	echo '<p class="twitter-message">'.$tweet.'</p>
		<p class="twitter-timestamp" style="text-align:right">
			on <a href="'.$status_link.'" class="twitter-link">'.$published_date.' at '.$published_time.'</a>
		</p>';
	}

}

?>