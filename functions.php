<?php
	
	function create_persistence($file='persist.xml', $xml, $persist = 1 )
	{
		// Create the file
		file_put_contents($file, "<?xml version='1.0' encoding='UTF-8'?>\n<persistent_record>\n");
		// Now we should get the timestamp for this original creation
		file_put_contents($file, "<timestamp>".time()."</timestamp>\n", FILE_APPEND);
		
		for ($i = 1; $i <= $persist; $i++)
		{
			// Select a random photo from the possible photos
			$id = $xml->photos->photo[rand(0, $xml->photos->photo->count()-1)]['id'];
			// Write the id to the file
			file_put_contents($file, "<photo id='".$id."'/>\n", FILE_APPEND);
		}
		
		// Close the root of the XML document
		file_put_contents($file, "</persistent_record>", FILE_APPEND);
	}
	
	function validate_constants($constant)
	{
		if (is_numeric($constant) && $constant >= 1)
			return true;
		else
			return false;
	}