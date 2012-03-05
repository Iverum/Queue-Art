<?php
	// Require the file that contains the constants for the API call and any config variables.
	require_once('config.php');
	// Require the file that contains any functions being used here
	require_once('functions.php');
	
	// Declare any variables
	$file = 'persist.xml';
	$persist;
	$l_url;
	
	// Call the Flickr API to get an XML document of all photos in the gallery.
	$url = "http://api.flickr.com/services/rest/?method=flickr.galleries.getPhotos&api_key=".API_KEY."&gallery_id=".GAL_ID."&extras=url_l";
	$xml = simplexml_load_file($url);
	
	// Check for the PERSIST constant
	if(PERSIST)
	{
	
		// Check the PERSIST_NUM and PERSIST_LEN variables for correct settings
		if ((!validate_constants(PERSIST_NUM)) && (!validate_constants(PERSIST_LEN)))
		{
			exit("Constants improperly defined.");
		}
		
		// If it is set to true, we need to check if a file containing the persistent IDs exists
		if (file_exists($file))
		{
			// If the file exists we should read it into memory
			$persist = simplexml_load_file($file);
			// Check if the timestamp has expired based on the PERSIST_LEN constant
			$org_time = $persist->timestamp;
			$org_time = $org_time + (PERSIST_LEN * 24 * 60 * 60);
			if (time() >= $org_time)
			{
				// If the timestamp is expired replace the id saved in the file with a new id
				create_persistence($file, $xml, PERSIST_NUM);
			}
		}
		else
		{
			// If the file doesn't exist we should create it
			create_persistence($file, $xml, PERSIST_NUM);
		}
		
		// At this point we are aware that persistence is set and the file has been created/updated
		// If $_GET['id'] is set we need to get that value
		if (isset($_GET['id']))
		{
			$p_id = $_GET['id'] - 1;
			$id = $persist->photo[$p_id]['id'];
			$photos = $xml->photos->photo;
			foreach ($photos as $photo)
			{
				if (trim($photo['id']) == trim($id))
				{
					$l_url = $photo['url_l'];
					break;
				}
			}
			// if the id isn't found in the persistence records
			if (empty($l_url))
			{
				$id = $persist->photo[0]['id'];
				$photos = $xml->photos->photo;
				foreach ($photos as $photo)
				{
					if (trim($photo['id']) == trim($id))
					{
						$l_url = $photo['url_l'];
						break;
					}
				}
			}
		}
		else
		// $_GET['id'] isn't set. Just grab the first image
		{
			// Now we want to get the id from the file and find that id in the API call
			$id = $persist->photo[0]['id'];
			$photos = $xml->photos->photo;
			foreach ($photos as $photo)
			{
				if (trim($photo['id']) == trim($id))
				{
					$l_url = $photo['url_l'];
					break;
				}
			}
		}
	}
	
	// If the PERSIST constant isn't set
	else
	{
	
		if (isset($_GET['id']))
		{
			// If an id exists set it to a variable
			// It should be noted that $id does not actually correspond to an id in the XML.
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
		}
		else
		{
			// Pull the first photo in the collection
			$photo = $xml->photos->photo;
			$l_url = $photo['url_l'];
		}
	}
	
	// echo the img tag with the url as the source
	echo "<img src='{$l_url}'/>";