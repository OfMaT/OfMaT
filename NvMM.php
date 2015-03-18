<?php

function fvQueryCurl ($nvmmURL, $nvmmAuth, $nvmmQueryJson, &$nvOutput) {
	//Pre-Condition ==> None
	//Effects ========> Returns a message containing "OK" or the curl error
	//					if sucess in curl command, stores the answer in $nvOutput
	
	$err = "OK";
	$ch = curl_init(); // initiate curl
	curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_URL, $nvmmURL);
	curl_setopt($ch, CURLOPT_USERPWD, $nvmmAuth);	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvmmQueryJson); // define what you want to post
	if ( ! $nvOutput = curl_exec($ch)) { // execute curl command
      $err = curl_error($ch);
    }
    curl_close($ch);
	return $err;
}

function loadSlicesList ($aResult) {
	//Pre-Condition ==> None
	//Effects ========> Returns a select with Slices' list
	
    echo '<div class="title1">Slice *:<br>';
    echo '<select name="slice">';
    echo '  <option value="">select a slice</option>';
    // walk and show items...
    for ($i = 0; $i < count($aResult); $i++) {
      $val = $aResult[$i]['slice-name'];
      if ($val <> ' ')
        echo '<option value="'.$val.'"> '.$val.'</option>';
    }
    echo '</select><br><br>';
}

function showSliceInformation($sliceName) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Slice Information and returns the number of errors found

	global $fvURL,$fvAuth;
	$errors = 1;
    $output = '';
    $query1 = '{"method":"list-slice-info", "params":{"slice-name":"'.$sliceName.'"}, "id":"x", "jsonrpc":"2.0"}';
    $msg = fvQueryCurl($fvURL,$fvAuth,$query1,$output);

    if ($msg != "OK") {
      echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connections
    
      if (strpos($output,"result") === false) { //we have some error in json1
         echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
      }
      else { //json1 is OK
        $arrayJson = json_decode($output, true);
        $aResult = $arrayJson['result'];
        if (count($aResult)>0) { //we got info from slice
          echo '<div class="text_pre_table">Slice\'s Information: </div>
	              <table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';

	      // walk and show items
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Slice Name</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.$aResult["slice-name"].'</div></td>';
          echo ' </tr>';
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Controller URL</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.$aResult["controller-url"].'</div></td>';
          echo ' </tr>';          
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Admin Contact</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.$aResult["admin-contact"].'</div></td>';
          echo ' </tr>';          	      
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Admin Status</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.boolString($aResult["admin-status"]).'</div></td>';
          echo ' </tr>';          	      	      
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Drop Policy</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.$aResult["drop-policy"].'</div></td>';
          echo ' </tr>';          	      	                
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Current Rate</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.$aResult["current-rate"].'</div></td>';
          echo ' </tr>';          	      	                
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Current Flowmod Usage</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.$aResult["current-flowmod-usage"].'</div></td>';
          echo ' </tr>';          	      	                
          echo ' <tr>';
          echo '   <td class="table_col_left" width="30%"><div class="table_text1">Receive LLDP</div></td>';
          echo '   <td class="table_col_right" width="70%"><div class="table_text2">'.boolString($aResult["recv-lldp"]).'</div></td>';
          echo ' </tr>';          	      	                
	      echo '</table>';
	      $errors = 0;
        }
        else { //no info from this slice
           echo '<div class="Message1"><br>ERROR: Empty result.</div>'; 
	    }
      }

    }
    return $errors;
	
}

function showSliceFlowSpace($sliceName) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with FlowSpace of specified slice and returns the number of errors found

	global $fvURL, $fvAuth;
	$errors = 1;
    $output1 = '';
    $query1 = '{"method":"list-flowspace", "params":{"slice-name":"'.$sliceName.'"}, "id":"x", "jsonrpc":"2.0"}';
    $msg = fvQueryCurl($fvURL,$fvAuth,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection
       if (strpos($output1,"\"dpid\":") === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>Some error occured or there are no flowspaces of this slice to show.</div>';  
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
         $Result = $Json['result'];
    	 //var_dump($Result);
    
    	 if (count($Result)>0) { //we got any info from switch
      	   echo '<div class="text_pre_table">Slice\'s FlowSpace: </div>';
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';
       	   echo ' <tr>';
           echo '   <td class="table_col_left" width="25%"><div class="table_text3">Id / Name</div></td>';
           echo '   <td class="table_col_left" width="20%"><div class="table_text3">DPID</div></td>';
           echo '   <td class="table_col_left" width="10%"><div class="table_text3">Priority / Level</div></td>';
           echo '   <td class="table_col_left" width="31%"><div class="table_text3">Match</div></td>';
           echo '   <td class="table_col_left" width="6%"><div class="table_text3">FE</div></td>';
           echo '   <td class="table_col_left" width="8%"><div class="table_text3">Queues</div></td>';              
           echo ' </tr>';
       	   echo ' <tr>';
       	   //var_dump($Ports);
      	   for ($i = 0; $i <  count($Result); $i++) {       	   
             echo '  <td class="table_col_right" width="25%"><div class="table_text4">'.$Result[$i]["id"].' ('.$Result[$i]["name"].')</div></td>';
             echo '  <td class="table_col_right" width="20%"><div class="table_text4">'.$Result[$i]["dpid"].'</div></td>';             
             echo '  <td class="table_col_right" width="10%"><div class="table_text4">'.$Result[$i]["priority"].' / '.$Result[$i]["slice-action"][0]["permission"].'</div></td>';
             echo '  <td class="table_col_right" width="31%"><div class="table_text4">';
             $arrayMatch = $Result[$i]["match"];
			 for ($j = 0; $j <  count($arrayMatch); $j++) {
                $key=key($arrayMatch);
                $val=$arrayMatch[$key];
                echo ' ['.$key.']='.$val.' ';
                next($arrayMatch);
		     }             

             echo '</div></td>';
             echo '  <td class="table_col_right" width="6%"><div class="table_text4">'.$Result[$i]["force-enqueue"].'</div></td>';
             if (count($Result[$i]["queues"])>0)
			   echo '  <td class="table_col_right" width="8%"><div class="table_text4">'.$Result[$i]["queues"][0].'</div></td>';               
             else
               echo '  <td class="table_col_right" width="8%"><div class="table_text4">none</div></td>';
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

function showSliceSwitches($sliceName) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with switches of this Slice and returns the number of errors found

	global $fvURL,$fvAuth;
	$errors = 1;
    $output2 = '';
    $query2 = '{"method":"list-slice-health", "params":{"slice-name":"'.$sliceName.'"}, "id":"x", "jsonrpc":"2.0"}'; 
    $msg2 = fvQueryCurl($fvURL,$fvAuth,$query2,$output2);

    if (strlen($msg2)==0) {
      echo '<div class="Message1"><br>No switches in this slice.</div>';  
    }	  
    elseif ($msg2 != "OK") {
      echo '<div class="Message1"><br>ERROR: '.$msg2.'</div>';  
    }	  
    else { //no errors in curl connections
    
      
	      if (strpos($output2,"result") === false) { ////we have some error in json2
            echo '<div class="Message1"><br>ERROR: Invalid content in results. Please try again.</div>';  
	      }
	      else {  //json2 is OK
	        $arrayJson = json_decode($output2, true);
	        $arrayResult = $arrayJson['result']['connected-dpids'];
	        if (count($arrayResult)>0) { //we have some switch(s) belonging this slice
			  echo '<div class="text_pre_table">Slice\'s Switches: </div>
	                  <table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';	        
		      // walk and show items
   		      for ($i = 0; $i <  count($arrayResult); $i++) {
              echo ' <tr>';
              echo '   <td class="table_col_left" width="30%"><div class="table_text1">Switch '.($i+1).'</div></td>';
              echo '   <td class="table_col_right" width="70%"><div class="table_text2">';
		         $val = $arrayResult[$i];
		         if ($val <> ' ')
		           //echo $val.' <a class="internal2" href="flowspace.php?dpid='.$val.'">(view its flowspace)</a><br>';
		           echo $val.'<br>';
              echo '</div></td></tr>';		           
		      }
              echo '</table>';
       	      $errors = 0;
	        }
	        else { //no switches connected to this slice
              echo '<div class="Message1"><br>ERROR: Empty result.</div>'; 
		    }
	      }		      
    }
    return $errors;
	
}

function showSliceStats($sliceName) {
	//Pre-Condition ==> None
	//Effects ========> Draws a table with Stats of specified slice and returns the number of errors found

	global $fvURL, $fvAuth;
	$errors = 1;
    $output1 = '';
    $query1 = '{"method":"list-slice-stats", "params":{"slice-name":"'.$sliceName.'"}, "id":"x", "jsonrpc":"2.0"}';
    $msg = fvQueryCurl($fvURL,$fvAuth,$query1,$output1);

    if ($msg != "OK") {
       echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
    }
    else { //no errors in curl connection
       if (strpos($output1,"\"drop\":") === false) { 
        //we have some error in json1
         echo '<div class="Message1"><br>Some error occured. Probably this slice don\'t have stats available.</div>';
       }
       else { //query without errors
            
         $Json = json_decode($output1, true);
         $Result = $Json['result'];

    	 if (count($Result)>0) { //we got any info from switch
      	   echo '<div class="text_pre_table">Slice\'s Stats: </div>';
           echo '<table align="center" cellspacing="0" cellpadding="0" width="95%" class="table_style">';

           //showing tx stats...
       	   echo ' <tr>';
           echo '   <td class="table_col_centered" width="100%" colspan="2"><div class="table_text3">Tx</div></td>';
           echo ' </tr>';                      
      	   $ResultTmp = $Result["tx"];

    	   if (count($ResultTmp)>0) {
	         // walk and show items
	         ksort($ResultTmp);
    	     for ($i = 0; $i <  count($ResultTmp); $i++) {
               $key=key($ResultTmp);
               $arrayVal=$ResultTmp[$key];
               echo ' <tr>';
               echo '   <td class="table_col_left" width="50%"><div class="table_text5">'.$key.'</div></td>';
               echo '   <td class="table_col_right" width="50%"><div class="table_text2">';
               // walk and show items
               if (count($arrayVal)>0) {
    	         for ($j = 0; $j <  count($arrayVal); $j++) {
                   $subkey=key($arrayVal);
                   $val=$arrayVal[$subkey];
                   echo ' ['.$subkey.']='.$val;
                   next($arrayVal);
   	             }
	           }
	           else
	             echo 'None';
               echo ' </div></td></tr>';
               next($ResultTmp);
   		     }
	       }
	       else {
       	     echo ' <tr>';
             echo '   <td class="table_col_right" width="100%" colspan="2"><div class="table_text2">No data found.</div></td>';
             echo ' </tr>';      
		   }

		   //showing rx stats...
       	   echo ' <tr>';
           echo '   <td class="table_col_centered" width="100%" colspan="2"><div class="table_text3">Rx</div></td>';
           echo ' </tr>';                      
      	   $ResultTmp = $Result["rx"];

    	   if (count($ResultTmp)>0) {
	         // walk and show items
	         ksort($ResultTmp);
	         for ($i = 0; $i <  count($ResultTmp); $i++) {
               $key=key($ResultTmp);
               $arrayVal=$ResultTmp[$key];
               echo ' <tr>';
               echo '   <td class="table_col_left" width="50%"><div class="table_text5">'.$key.'</div></td>';
               echo '   <td class="table_col_right" width="50%"><div class="table_text2">';
               // walk and show items
               if (count($arrayVal)>0) {
    	         for ($j = 0; $j <  count($arrayVal); $j++) {
                   $subkey=key($arrayVal);
                   $val=$arrayVal[$subkey];
                   echo ' ['.$subkey.']='.$val;
                   next($arrayVal);
	             }
	           }
	           else
	             echo 'None';
               echo ' </div></td></tr>';
               next($ResultTmp);
   		     }
	       }
	       else {
       	     echo ' <tr>';
             echo '   <td class="table_col_right" width="100%" colspan="2"><div class="table_text2">No data found.</div></td>';
             echo ' </tr>';      
		   }
           
           //showing drop stats...
       	   echo ' <tr>';
           echo '   <td class="table_col_centered" width="100%" colspan="2"><div class="table_text3">Drop</div></td>';
           echo ' </tr>';                      
      	   $ResultTmp = $Result["drop"];

    	   if (count($ResultTmp)>0) {
    	   	 ksort($ResultTmp);
	         // walk and show items
	         for ($i = 0; $i <  count($ResultTmp); $i++) {
               $key=key($ResultTmp);
               $arrayVal=$ResultTmp[$key];
               echo ' <tr>';
               echo '   <td class="table_col_left" width="50%"><div class="table_text5">'.$key.'</div></td>';
               echo '   <td class="table_col_right" width="50%"><div class="table_text2">';
               // walk and show items
               if (count($arrayVal)>0) {
    	         for ($j = 0; $j <  count($arrayVal); $j++) {
                   $subkey=key($arrayVal);
                   $val=$arrayVal[$subkey];
                   echo ' ['.$subkey.']='.$val;
                   next($arrayVal);
	             }
	           }
	           else
	             echo 'None';
               echo ' </div></td></tr>';
               next($ResultTmp);
   		     }
           }
	       else {
       	     echo ' <tr>';
             echo '   <td class="table_col_right" width="100%" colspan="2"><div class="table_text2">No data found.</div></td>';
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

?>