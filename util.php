<?php

function searchSubArray(Array $array, $key, $value) {
	//Pre-Condition ==> None
	//Effects ========> Returns a subarray containing the search criteria passed as parameter.
		
    foreach ($array as $subarray){  
        if (isset($subarray[$key]) && $subarray[$key] == $value)
          return $subarray;       
    } 
}

function boolString($bValue = false) {
  return ($bValue ? 'True' : 'False');
}

function loadConfigFile(&$OFversion,&$nvmmFvURL,&$nvmmFvAuth,&$swmmURL) {
	//Pre-Condition ==> Variables must be defined before call this function
	//Effects ========> Verify if the config file is OK and load its contents into variables passed as parameters
	
	if (file_exists('ofmat.conf')) {
	  $xml = simplexml_load_file('ofmat.conf');
	  if($xml ===  FALSE) { //Wrong XML format
	    return "Wrong XML format in file 'ofmat.conf'.";
	  }
	  else { //general structure OK 
	    //checking OFversion
  	    if (isset($xml->OFversion) == FALSE)
	      return "OF version not found.";
  	    elseif ($xml->OFversion != "1.0")
	      return "This release only supports OpenFlow 1.0.";
	  
	    //checking NvMM
	    if (isset($xml->NvMM->FVurl) == FALSE)
	      return "URL for NvMM in 'ofmat.conf' not found.";
	    elseif (filter_var($xml->NvMM->FVurl, FILTER_VALIDATE_URL) === FALSE)
	      return "Invalid URL for NvMM in 'ofmat.conf'.";
	    elseif (isset($xml->NvMM->FVuser) == FALSE)
	      return "User for NvMM in 'ofmat.conf' not found.";	      
	    elseif (strlen(trim((string)$xml->NvMM->FVuser)) < 1)
	      return "User for NvMM in 'ofmat.conf' can not be empty.";  
	    elseif (isset($xml->NvMM->FVpassword) == FALSE)
	      return "Password for NvMM in 'ofmat.conf' not found.";
	      
	    //checking SwMM
	    if (isset($xml->SwMM->url) == FALSE)
	      return "URL for SwMM in 'ofmat.conf' not found.";
	    elseif (filter_var($xml->SwMM->url, FILTER_VALIDATE_URL) === FALSE)
	      return "Invalid URL for SwMM in 'ofmat.conf'.";	    

	    //all requirements already checked
	    $OFversion = trim((string)$xml->OFversion);	    
	    $nvmmFvURL = trim((string)$xml->NvMM->FVurl);
	    $nvmmFvAuth = trim((string)$xml->NvMM->FVuser).':'.((string)$xml->NvMM->FVpassword);
	    $swmmURL = trim((string)$xml->SwMM->url);
	    return "OK";
	  }
	}
	else {
	  return "Config file 'ofmat.conf' not found.";
	}
	
}

?>