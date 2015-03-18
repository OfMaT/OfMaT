<?php

function swQueryCurl ($swmmURL, $swmmQueryJson, &$swOutput) {
	//Pre-Condition ==> None
	//Effects ========> Returns a message containing "OK" or the curl error
	//					if sucess in curl command, stores the answer in $swOutput
	
	$err = "OK";
	$ch = curl_init(); // initiate curl
	curl_setopt($ch, CURLOPT_HTTPGET, true);  // ensures HTTP method is GET
	curl_setopt($ch, CURLOPT_POST, false);  // tell curl you want to post something
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_URL, $swmmURL.$swmmQueryJson);

	if ( ! $swOutput = curl_exec($ch)) { // execute curl command
      $err = curl_error($ch);
    }
    curl_close($ch); 	
	return $err;
}

function loadSwitchesList ($aResult) {
	//Pre-Condition ==> None
	//Effects ========> Returns a select with Switch's list
	
    echo '<div class="title1">Switch *:<br>';
    echo '<select name="switch">';
    echo '  <option value="">select a switch</option>';
    // walk and show elements...
    for ($i = 0; $i < count($aResult); $i++) {
      $val = $aResult[$i]["dpid"];
      if ($val <> ' ')
        echo '<option value="'.$val.'"> '.$val.'</option>';
    }
    echo '</select><br><br>';
}

function showSwitchDescription($sw) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Switch Description and returns the number of errors found
	
	global $swURL;
	$errors = 1;
    $output1 = '';
    $query1 = '/wm/core/switch/'.$sw.'/desc/json';
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection
       if (strpos($output1,"softwareDescription") === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
    	 $key=key($Json);
    	 $Result = $Json[$key][0];
    
    	 if (count($Result)>0) { //we got any info from switch
      	   echo '
           <div class="text_pre_table">Switch\'s Description: </div>
           <table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">
            <tr>
              <td class="table_col_left" width="35%"><div class="table_text1">dpid</div></td>
              <td class="table_col_right" width="65%"><div class="table_text2">'.$key.'</div></td>
            </tr>';
      	   // walk and show items
      	   for ($i = 0; $i <  count($Result); $i++) {
             $key=key($Result);
             $val=$Result[$key];
        	 echo ' <tr>';
        	 echo '   <td class="table_col_left" width="35%"><div class="table_text1">'.$key.'</div></td>';
        	 echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$val.'</div></td>';
        	 echo ' </tr>';
        	 next($Result);
      	   }
           echo '</table>';
           $errors = 0;
    	 }
    	 else { //didn´t get info from switch
           echo '<div class="Message1"><br>ERROR: Empty result.</div>'; 
    	 }

       }
    }
    return $errors;

}

function showSwitchFeaturesAndPorts($sw) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Switch Features and returns the number of errors found
	
	global $swURL;
	$errors = 1;	
    $output1 = '';
    $query1 = '/wm/core/controller/switches/json';
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection
       if (strpos($output1,"\"dpid\":") === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
    	 $Result = searchSubArray($Json,"dpid",$sw);
    
    	 if (count($Result)>0) { //we got any info from switch
      	   echo '<div class="text_pre_table">Switch\'s Features: </div>';
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">harole</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Result["harole"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">actions</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Result["actions"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">buffers</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Result["buffers"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">connected Since</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Result["connectedSince"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">capabilities</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Result["capabilities"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">supportsOfppFlood</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.boolString($Result["attributes"]["supportsOfppFlood"]).'</div></td>';
           echo ' </tr>';           
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">supportsNxRole</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.boolString($Result["attributes"]["supportsNxRole"]).'</div></td>';
           echo ' </tr>';                      
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">FastWildcards</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Result["attributes"]["FastWildcards"].'</div></td>';
           echo ' </tr>';                                 
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">supportsOfppTable</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.boolString($Result["attributes"]["supportsOfppTable"]).'</div></td>';
           echo ' </tr>';                                 
           echo '</table>';
           
      	   echo '<div class="text_pre_table">Switch\'s Ports: </div>';
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="12%"><div class="table_text3">Port</div></td>';
           echo '   <td class="table_col_left" width="17%"><div class="table_text3">Hw Address</div></td>';
           echo '   <td class="table_col_left" width="8%"><div class="table_text3">Config</div></td>';
           echo '   <td class="table_col_left" width="8%"><div class="table_text3">State</div></td>';
           echo '   <td class="table_col_left" width="55%" colspan="4"><div class="table_text3">Features</div></td>';           
           echo ' </tr>';
       	   echo ' <tr>';
       	   $Ports = $Result["ports"];

      	   for ($i = 0; $i <  count($Ports); $i++) {       	   
             echo '   <td class="table_col_right" width="12%"><div class="table_text4">'.$Ports[$i]["portNumber"].' ('.$Ports[$i]["name"].')</div></td>';
             echo '   <td class="table_col_right" width="17%"><div class="table_text4">'.$Ports[$i]["hardwareAddress"].'</div></td>';
             echo '   <td class="table_col_right" width="8%"><div class="table_text4">'.$Ports[$i]["config"].'</div></td>';
             echo '   <td class="table_col_right" width="8%"><div class="table_text4">'.$Ports[$i]["state"].'</div></td>';
             echo '   <td class="table_col_right" width="13%"><div class="table_text4">current: '.$Ports[$i]["currentFeatures"].'</div></td>';           
             echo '   <td class="table_col_right" width="16%"><div class="table_text4">advertised: '.$Ports[$i]["advertisedFeatures"].'</div></td>';                      
             echo '   <td class="table_col_right" width="16%"><div class="table_text4">supported: '.$Ports[$i]["supportedFeatures"].'</div></td>';                                 
             echo '   <td class="table_col_right" width="10%"><div class="table_text4">peer: '.$Ports[$i]["peerFeatures"].'</div></td>';           
             echo ' </tr>';           
           }
           echo '</table>';           
		   $errors = 0;
    	 }
    	 else { //didn´t get info from switch
           echo '<div class="Message1"><br>ERROR: Empty result.</div>';  
    	 }

       }
    }
    return $errors;

}

function showSwitchPortStats($sw) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Switch Port Stats and returns the number of errors found
	
	global $swURL;
	$errors = 1;
    $output1 = '';
    $query1 = '/wm/core/switch/'.$sw.'/port/json';
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection
       if (strpos($output1,'"receivePackets":') === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
    	 $Result = $Json[$sw];
    	 if (count($Result)>0) { //we got any info from switch
      	   echo '
           <div class="text_pre_table">Switch\'s Port Stats: </div>
           <table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';
      	   // walk and show items
      	   for ($i = 0; $i <  count($Result); $i++) { //walk on each port
      	   	 $ResultPort = $Result[$i];
      	   	 $key=key($ResultPort);
             $val=$ResultPort[$key];
        	 echo ' <tr>';
        	 echo '   <td class="table_col_left" width="35%"><div class="table_text1">Port '.$val.'</div></td>';
        	 echo '   <td class="table_col_right" width="65%"><div class="table_text2">';
        	 next($ResultPort);
        	 for ($j = 1; $j <  count($ResultPort); $j++) {
        	   $key=key($ResultPort);
               $val=$ResultPort[$key];	
        	   echo $key.' = '.$val.'<br>';
        	   next($ResultPort);
        	 }
        	 echo ' </div></td></tr>';
        	 next($Result);
      	   }
           echo '</table>';
           $errors = 0;
    	 }
    	 else { //didn´t get info from switch
           echo '<div class="Message1"><br>ERROR: Empty result.</div>'; 
    	 }

       }
    }
    return $errors;

}

function showSwitchFlows($sw) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Switch Flows and returns the number of errors found
	
	global $swURL;
	$errors = 1;	
    $output1 = '';
    $query1 = '/wm/core/switch/'.$sw.'/flow/json';
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection
       if (strpos($output1,$sw) === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
    	 $Result = $Json[$sw];
      	 echo '<div class="text_pre_table">Switch\'s Flows: </div>';
    	 if (count($Result)>0) { //we got any info from switch
             
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';       	   
      	   for ($i = 0; $i < count($Result); $i++) {       	   
       	     echo ' <tr>';
             echo '   <td class="table_col_left" width="10%"><div class="table_text3">Flow<br>'.($i+1).'</div></td>';      	   
             echo '   <td class="table_col_right" width="90%"><div class="table_text2">';
             echo 'table = '.$Result[$i]["tableId"];
           	 $Match = $Result[$i]["match"];
             echo '<br>MATCH => {';
      	     for ($k = 0; $k < count($Match); $k++) {              
          	   $key = key($Match);
               $val = $Match[$key];	
        	   echo '<b>'.$key.'</b> = '.$val.' ';
        	   next($Match);
      	     }
             echo '}';             
             echo '<br>duration (s) = '.$Result[$i]["durationSeconds"];
             echo '<br>duration (ns) = '.$Result[$i]["durationNanoseconds"];  
             echo '<br>priority = '.$Result[$i]["priority"];
             echo '<br>idleTimeout = '.$Result[$i]["idleTimeout"];
             echo '<br>hardTimeout = '.$Result[$i]["hardTimeout"];             
             echo '<br>cookie = '.$Result[$i]["cookie"];
             echo '<br>packetCount = '.$Result[$i]["packetCount"];
             echo '<br>byteCount = '.$Result[$i]["byteCount"];
        	 $Actions = $Result[$i]["actions"][0];

             echo '<br>ACTIONS => {';
      	     for ($j = 0; $j < count($Actions); $j++) {              
          	   $key = key($Actions);
               $val = $Actions[$key];	
        	   echo '<b>'.$key.'</b> = '.$val.' ';
        	   next($Actions);
      	     }
             echo '}';
        	 echo ' </div></td></tr>';
           }
           echo '</table>';           
		   $errors = 0;
    	 }
    	 else { //didn´t get info from switch
           echo '<div class="Message1"><br>Empty result. No flows defined in this switch.</div>';  
    	 }

       }
    }
    return $errors;

}

?>