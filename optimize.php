<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="initial-scale=1.0, user-scalable=no"><title>BostadsOptimatorn</title><link rel="stylesheet" type="text/css" href="main.css" media="screen" /><script src="../../../BostadsOptimatorn/WebPage/public_html/chroma.js/chroma.js"></script><script type="text/javascript" src="main.js" ></script></head><body><div id="container">  <div id="header"> <img class="logoImg" src="BostadsOptimatornLogo1.png" alt="Logotype" width="260" height="65"> </div>  <div id="form">        <?php 		// Print all of the values of the preferences	echo htmlspecialchars($_POST['pris']); 	echo (int)$_POST['slider_price']; 		echo htmlspecialchars($_POST['size-rooms']);	 echo (int)$_POST['slider_rooms']; 	 	 echo htmlspecialchars($_POST['size-square']);	 echo (int)$_POST['slider_kvm'];	 	 echo htmlspecialchars($_POST['age']);	 echo (int)$_POST['slider_age'];	 	$location = htmlspecialchars($_POST['LongLat']);		//Remove parenthesis from string	$locationClean = str_replace('(', '', $location);	$locationClean = str_replace(')', '', $locationClean);	$locationClean = str_replace(' ', '', $locationClean);	echo $locationClean;		 echo (int)$_POST['slider_location'];	  	 // Build the url string for http communication	 $auth = array();	  $auth['callerId'] = "PriceVisualization";	  $auth['time'] = time();	  $auth['unique'] = rand(0, PHP_INT_MAX);	  $auth['hash'] = sha1($auth['callerId'] . $auth['time'] . "0LJN0MwJ8issGAkXVgsQOdscWhRjARy2eyzyqkKO" . $auth['unique']);	$url = "http://api.booli.se/listings/?center=".$locationClean."&dim=200000,200000&" . http_build_query($auth);	 /* 	  $message = "yoo";	 echo "<script type='text/javascript'>alert('$message');</script>";	 */			   echo "<script type='text/javascript'>alert('$url');</script>";		  // Use curl to communicate with Booli API	  $curl = curl_init($url);	  	  curl_setopt_array($curl, array(		  CURLOPT_RETURNTRANSFER => true	  ));	 	  	  // $response is a JSON string	  $response = curl_exec($curl); 	  	  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);	  	  curl_close($curl);	  	  if ($httpCode != 200) {		  echo "Fail!";	  }else{		  echo "Success!";	  }	   	  // $result is a JSON object  	  $result = json_decode($response);	 	 echo $response;		 ?>         </div>  <input id="pac-input" class="controls" type="text" placeholder="Search Box">  <div id="mapContainer">    <div id="map"></div>  </div></div><script></script> <script async defer        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWHxPyeMmIKG2h3_BaW3X3PngdwaLE2q8&libraries=visualization&libraries=places&callback=initMap">    </script></body></html>