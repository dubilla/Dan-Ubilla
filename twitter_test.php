<?php
//URL encode the query string
$q = urlencode("fun");

//request URL
$request = "http://search.twitter.com/search.atom?q=$q&lang=en";

$curl = curl_init();

curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

curl_setopt($curl,CURLOPT_URL,$request);

$response = curl_exec($curl);

curl_close($curl);

//remove "twitter:" from the $response string
$response = str_replace("twitter:","",$response);

//convert response XML into an object
$xml = simplexml_load_string($response);

//warpping the whole output with <result></result>
echo "<results>";

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
	
	//get the status link
	$status_link = $xml->entry[$i]->link[0]->attributes()->href;
	
	//get the tweet
	$tweet = $xml->entry[$i]->content;
	
	//remove <b> and </b> from the tweet. If you want to show bold keyword then you can comment this line
	$tweet = str_replace(array("<b>","</b>"),"",$tweet);
	
	//get the source link
	$source = $xml->entry[$i]->source;
	
	//the result div tha tholds the information
	echo '<div class="result" id="'.tweet_id.'">
					<div class="profile_image">
						<a href="'.$account_link.'">
							<img src="'.$image_link.'">
						</a>
					</div>
					<div class="status">
						<div class="content">
							<strong>
								<a href="'.$account_link.'">'.$screen_name.'</a>
							</strong>'.$tweet.'
						</div>
						<div class="time">
							'.$real_name.' at <a href="'.$status_link.'">'.$published_time.'</a> via '.$source.'
						</div>
					</div>
				</div>';
	}
	echo "</results>";

?>