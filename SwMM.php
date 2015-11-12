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
      $val = $aResult[$i]["switchDPID"];
      if ($val <> ' ')
        echo '<option value="'.$val.'"> '.$val.'</option>';
    }
    echo '</select><br><br>';
}

function switchRole($sw) {
	//Pre-Condition ==> None
	//Effects ========> Return the switch role or "ERROR"

	global $swURL;	
	$role = "ERROR";
    $output1 = '';
    $query1 = '/wm/core/switch/'.$sw.'/role/json';
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       $role = $msg;  
    }
    else { //no errors in curl connection
       if (strpos($output1,'"ERROR":') === true) { 
         //we have some error in json1
         return 'ERROR: Invalid content in results.';  
       }
       else { //query without errors
         $Json = json_decode($output1, true);
         if (($Json[$sw]=='MASTER') OR ($Json[$sw]=='SLAVE') OR ($Json[$sw]=='EQUAL'))
    	   $role = $Json[$sw];
    	 else  
    	   $role = 'INVALID VALUE';

       }
    }
    return $role;

}

function connectedSince($sw) {
	//Pre-Condition ==> None
	//Effects ========> Return time since switch is connected to OfMaT or "ERROR"

	global $swURL;	
	$since = "ERROR";
    $output1 = '';
    $query1 = '/wm/core/controller/switches/json'; 
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       $since = $msg;  
    }
    else { //no errors in curl connection
       if (strpos($output1,'"ERROR":') === true) { 
         //we have some error in json1
         return 'ERROR: Invalid content in results.';  
       }
       else { //query without errors
         $Json = json_decode($output1, true);
         $Result = searchSubArray($Json,switchDPID,$sw);
         if ($Result=="ERROR")
           $since = "ERROR in searchSubArray()"; 
         else {
		   $since = substr($Result["connectedSince"],0,10);
	       $since = gmdate('d/m/Y H:i:s T', $since);
		 }
         
       }
    }
    return $since;

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
    	 $Result = $Json[$key];
    
    	 if (count($Result)>0) { //we got any info from switch
      	   echo '
           <div class="text_pre_table">Switch\'s Description: </div>
           <table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">
            <tr>
              <td class="table_col_left" width="35%"><div class="table_text1">dpid</div></td>
              <td class="table_col_right" width="65%"><div class="table_text2">'.$sw.'</div></td>
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

function showSwitchFeaturesAndPorts($sw, $versionOF) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Switch Features and returns the number of errors found
	
	global $swURL;
	$errors = 1;	
    $output1 = '';
    $query1 = '/wm/core/switch/'.$sw.'/features/json';
    $msg = swQueryCurl($swURL,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection

       if (strpos($output1,'"'.$sw.'"') === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
    
    	 if (count($Json)>0) { //we got any info from switch
      	   echo '<br><div class="text_pre_table">Switch\'s Features: </div>';
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">role</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.switchRole($sw).'</div></td>';
           echo ' </tr>';
       	   if ($versionOF == '1.0') {
       	     echo ' <tr>';
             echo '   <td class="table_col_left" width="35%"><div class="table_text1">actions</div></td>';
             echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Json["actions"].'</div></td>';
             echo ' </tr>';
           }
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">buffers</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Json["buffers"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">connected Since</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.connectedSince($sw).'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">capabilities</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Json["capabilities"].'</div></td>';
           echo ' </tr>';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="35%"><div class="table_text1">tables</div></td>';
           echo '   <td class="table_col_right" width="65%"><div class="table_text2">'.$Json["tables"].'</div></td>';
           echo ' </tr>';           
           echo '</table>';

       	   //var_dump($Json);
       	   if ($versionOF == '1.0') {
           
		  	   echo '<br><div class="text_pre_table">Switch\'s Ports: </div>';
		       echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';
		   	   echo ' <tr>';
		       echo '   <td class="table_col_left" width="12%"><div class="table_text3">Port</div></td>';
		       echo '   <td class="table_col_left" width="17%"><div class="table_text3">Hw Address</div></td>';
		       echo '   <td class="table_col_left" width="8%"><div class="table_text3">Config</div></td>';
		       echo '   <td class="table_col_left" width="8%"><div class="table_text3">State</div></td>';
		       echo '   <td class="table_col_left" width="55%" colspan="4"><div class="table_text3">Features</div></td>';           
		       echo ' </tr>';
		   	   echo ' <tr>';
		   	   $Ports = $Json["portDesc"];
		   	   //var_dump($Ports);
		  	   for ($i = 0; $i <  count($Ports); $i++) {       	   
		         echo '   <td class="table_col_right" width="12%"><div class="table_text4">'.$Ports[$i]["portNumber"].' ('.$Ports[$i]["name"].')</div></td>';
		         echo '   <td class="table_col_right" width="17%"><div class="table_text4">'.$Ports[$i]["hardwareAddress"].'</div></td>';
		         echo '   <td class="table_col_right" width="8%"><div class="table_text4">'.$Ports[$i]["config"].'</div></td>';
		         echo '   <td class="table_col_right" width="8%"><div class="table_text4">'.$Ports[$i]["state"].'</div></td>';
		         echo '   <td class="table_col_right" width="13%"><div class="table_text4">current: '.$Ports[$i]["currentFeatures"].'</div></td>';           
		         echo '   <td class="table_col_right" width="16%"><div class="table_text4">advertised: '.$Ports[$i]["advertisedFeatures"].'</div></td>';                      
		         echo '   <td class="table_col_right" width="16%"><div class="table_text4">supported: '.$Ports[$i]["supportedFeatures"].'</div></td>';                                 
		         echo '   <td class="table_col_right" width="10%"><div class="table_text4">peer: '.$Ports[$i]["peerFeatures"].'</div></td>';           
		    
		       }
		       echo ' </tr>';              
		       echo '</table>';  
           
           }         
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
    	 $Result = $Json["port_reply"][0]["port"];
    	 if (count($Result)>0) { //we got any info from switch
      	   echo '<br>
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
        	 echo '   <ul style="list-style-type:square">';
        	 next($ResultPort);
        	 for ($j = 1; $j <  count($ResultPort); $j++) {
        	   $key=key($ResultPort);
               $val=$ResultPort[$key];	
        	   echo '<li>'.$key.' = '.$val.'</li>';
        	   next($ResultPort);
        	 }
        	 echo ' </ul></div></td></tr>';
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

function showSwitchFlows($sw,$versionOF) {
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
       if (strpos($output1,'"version":') === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>The flow table is empty.</div>';  
       }
       else { //query without errors

         $Json = json_decode($output1, true);
    	 $Result = $Json["flows"];
      	 echo '<br><div class="text_pre_table">Switch\'s Flows: </div>';
    	 if (count($Result)>0) { //we got any info from switch
             
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';       	   
      	   for ($i = 0; $i < count($Result); $i++) {       	   
       	     echo ' <tr>';
             echo '   <td class="table_col_left" width="10%"><div class="table_text3">Flow<br>'.($i+1).'</div></td>';      	   
             echo '   <td class="table_col_right" width="90%"><div class="table_text2">';
             echo '   <ul style="list-style-type:square">';
             echo '<li>version = '.$Result[$i]["version"].'</li>';
             echo '<li>cookie = '.$Result[$i]["cookie"].'</li>';
             echo '<li>tableId = '.$Result[$i]["tableId"].'</li>';
             echo '<li>packetCount = '.$Result[$i]["packetCount"].'</li>';
             echo '<li>byteCount = '.$Result[$i]["byteCount"].'</li>';
             echo '<li>duration = '.$Result[$i]["durationSeconds"].' seconds</li>';
             echo '<li>priority = '.$Result[$i]["priority"].'</li>';
             echo '<li>idleTimeout = '.$Result[$i]["idleTimeoutSec"].' seconds</li>';
             echo '<li>hardTimeout = '.$Result[$i]["hardTimeoutSec"].' seconds</li>';             
           	 $Match = $Result[$i]["match"];
             echo '<li>MATCH => {';
      	     for ($k = 0; $k < count($Match); $k++) {              
          	   $key = key($Match);
               $val = $Match[$key];	
        	   echo '<b>'.$key.'</b> = '.$val.' ';
        	   next($Match);
      	     }
             echo '}</li>';
             if ($versionOF == '1.0')
               echo '<li>ACTIONS => {'.$Result[$i]["actions"]["actions"].'}</li>';
             else {
			   echo '<li>INSTRUCTIONS => {'.$Result[$i]["instructions"]["instruction_apply_actions"]["actions"].'}</li>';
			 }  
        	 echo ' </ul></div></td></tr>';
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
