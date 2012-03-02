<?php
	// Require the file that contains the constants for the API call and any config variables.
	require_once('config.php');
	
	// Declare the l_url variable for later
	$l_url;
	
	// Call the Flickr API to get an XML document of all photos in the gallery.
	$url = "http://api.flickr.com/services/rest/?method=flickr.galleries.getPhotos&api_key=".API_KEY."&gallery_id=".GAL_ID."&extras=url_l";
	$xml = simplexml_load_file($url);
	
	// Check if there is a query attached to the request with an id parameter
	if(isset($_GET['id'])) {
		
		// If an id exists set it to a variable
		$id = $_GET['id'];
		
		// Loop through the collection and count until the count matches the id
		$count = 0;
		$photos = $xml->photos->photo;
		foreach ($photos as $photo)
		{
			// When the count equals the id pull the url and break the loop
			if ($count == $id)
			{
				$l_url = $photo['url_l'];
				break;
			} 
			// If the count doesn't match the id increment the count
			else 
			{
				$count++;
			}
		}
		
		// If the $l_url is empty (which means that the id wasn't found)
		// just pull the first photo in the collection
		if (empty($l_url))
		{
			$photo = $xml->photos->photo;
			$l_url = $photo['url_l'];
		}
		
	} else {
	
		// If no id exists pull the first photo down and display it
		$photo = $xml->photos->photo;
		$l_url = $photo['url_l'];
		
	}
	
	// echo the img tag with the url as the source
	echo "<img src='{$l_url}'/>";