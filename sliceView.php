<?php
error_reporting(E_ERROR | E_PARSE);
include 'util.php';
include 'NvMM.php';

$initialHeight = '302';
$key1 = '066x';
if (!empty($_POST)) { //we had a submit in the form

	if (!empty($_POST["slice"])) { //slice selected
	  $key1 = '999y';
	}
}

?>

<html>
  <head>
    <title>OfMaT</title>
    <link rel="stylesheet" type="text/css" href="ofmat.css"/>
  </head>
  <body class="Gray_background">

  <div class="Full_page">
    <div class="Panel_top">
      <div class="Page_title">
         <div class="Background_title"></div>
         <div class="Lbl_ofmat_title"></div>
         <div class="Ofmat_simbol"><img src="images/ofmat_simbol.gif" height="100" width="100"></div>
         <div class="Lbl_version">version 0.1</div>
      </div>
      <div class="Main_menu">
         <div class="Main_menu_top"></div>
         <a style="left:9px;" class="menu" href="index.html" ><center>Home</center></a>
         <a style="left:155px;" class="menu" href="switchView.php" ><center>Switch View</center></a>
         <a style="left:301px;" class="actual_menu" href="sliceView.php" ><center>Slice View</center></a>
         <a style="left:447px;" class="menu" href="documentation.html" ><center>Documentation</center></a>
         <a style="left:593px;" class="menu" href="about.html" ><center>About OfMaT</center></a>
      </div>
      <div class="Menu_gray_shadow"></div>
    </div>
    <div class="Panel_middle">
        
    <?php
    
	//check config file...
	$OFversion = ''; $fvURL = ''; $fvAuth = ''; $swURL = '';
	$answer = loadConfigFile($OFversion,$fvURL,$fvAuth,$swURL); 
	if ($answer == 'OK') { //file and its values are OK	
	
      if ($key1 == '066x') { //list all slices
   	    $output = '';
	    $query1 = '{"method":"list-slices", "id":"x", "jsonrpc":"2.0"}';
	    $msg = fvQueryCurl($fvURL,$fvAuth,$query1,$output);
    	
	    if ($msg != "OK") { 	
	      echo '<div class="Message1"><br>ERROR: '.$msg.'</div>';  
	      echo '<div style="height:'.($initialHeight+46).'px;"></div>';
	    }
	    else {
	  	  $arrayJson = json_decode($output, true);
   		  $arrayResult = $arrayJson['result'];

		  if (count($arrayResult)>0) { //we have some slice defined
		    echo '<form name="sliceView" method="POST" action="sliceView.php">';
			loadSlicesList($arrayResult);
		    echo '<input type="submit" name="Proceed" value="Proceed">';
            echo '</div></form>';
			echo '<div style="height:'.$initialHeight.'px;"></div>';            
		  }
		  else { //no slices defined
	        echo '<div class="Message1"><br>No slices defined in FlowVisor or authentication error.</div>';  		  
    	    echo '<div style="height:'.($initialHeight+46).'px;"></div>';
		  } 
	    }
      }  
      else { //show info of selected slice
      
        if (showSliceInformation($_POST["slice"])==0) { //no errors
		   showSliceSwitches($_POST["slice"]);
		   if (showSliceFlowSpace($_POST["slice"])==0) { //no errors
		  	 $endHeight = 1;
		   }
		   else
		     $endHeight = 1;
		   showSliceStats($_POST["slice"]);
		}
		else
		  $endHeight = 292;
		
		echo '<div style="height: '.$endHeight.'px;"></div>';      		
        echo '<div class="centered_link"><a class="internal2" href="sliceView.php">New Query</a></div>';  
      }
    }
	else { // some problem when tried to load config file and its values
	  echo '<div class="Message1"><br>ERROR: '.$answer.'</div>';  
	  echo '<div style="height:'.($initialHeight+46).'px;"></div>';
	}    
    ?>

	</div>
    <div class="Panel_base">
      <div class="Green_bar">
         <div class="Lbl_copyright">Copyright &#xA9; 2015</div>
      </div>
    </div>
  </div>
  </body>
</html>
