<?php
/*
	Movie Class
*/

class Movie {

	// variables
	var $id;
	var $title;
	var $link;
	var $description;

	// movie constructor
	function Movie($id, $title, $link, $description) {

		// passed id
		if ($id) { 
			// assign variables
			$this->id = intval($id);
			$this->title = trim($title);
			$this->link	= trim($link);
			$this->description = trim($description);
			
		} // end movie_id if

	} // end constructor


	// get functions	
	function get_id() {
		return $this->id;
	}

	function get_title() {
		return $this->title;
	}

	function get_link() {
		return $this->link;
	}

	function get_description() {
		return $this->description;
	}

		// get cover image
	function get_cover_image($size = 'small') {
		
		return '<img src="http://cdn.nflximg.com/us/boxshots/'.$size.'/'.$this->id.'.jpg" alt="'.$this->title.'" title="'.$this->title.'" />';
				
	} // end get_cover_image

	
	// get cover image source
	function get_cover_image_source($size = 'small') {
		
		return 'http://cdn.nflximg.com/us/boxshots/'.$size.'/'.$this->id.'.jpg';
		
	} // end get_cover_image_source
	
} // end of Movie class

?>
