<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="initial-scale=1.0, user-scalable=no"><title>BostadsOptimatorn</title><link rel="stylesheet" type="text/css" href="listingPage.css" media="screen" /><link rel="stylesheet" type="text/css" href="listStyle.css" media="screen" /><script src="chroma.js/chroma.js"></script><script type="text/javascript" src="showListingsPage.js" ></script></head><body><div id="container">  <div id="header"> <img class="logoImg" src="BostadsOptimatornLogo1.png" alt="Logotype" width="260" height="65"> </div>  <div id="form">    <div class="container container-pad brdr" id="property-listings">    <?php 	include 'getListings.php';	include 'filterOptimize.php';		//Returns random HEX color	function rand_color() {    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));	}		//Return gradient in array	function lineargradient($ra,$ga,$ba,$rz,$gz,$bz,$iterationnr) {	  $colorindex = array();	  for($iterationc=1; $iterationc<=$iterationnr; $iterationc++) {		 $iterationdiff = $iterationnr-$iterationc;		 $colorindex[] = '#'.			dechex(intval((($ra*$iterationc)+($rz*$iterationdiff))/$iterationnr)).			dechex(intval((($ga*$iterationc)+($gz*$iterationdiff))/$iterationnr)).			dechex(intval((($ba*$iterationc)+($bz*$iterationdiff))/$iterationnr));	  }	  return $colorindex;	}	//Returns string of 7 letters	function stringHEXLength($inString){		while(strlen($inString) < 7){			$inString = $inString."0";		}		return $inString;	}		// Multiple color array	//$markerColorArray = array('#00cc00','#00cc00','#66ff33','#66ff33','#99ff33','#99ff33','#ccff33','#ccff33','#ffff00','#ffff00','#ffcc00','#ffcc00','#ff9933','#ff9933','#ff6600','#ff6600','#ff5050','#ff5050','#cc0000','#cc0000');	// Five shades	$markerColorArray = array('#02a9d5','#02a9d5','#02a9d5','#02a9d5','#03c6fc','#03c6fc','#03c6fc','#03c6fc','#9ae8fe','#9ae8fe','#9ae8fe','#9ae8fe','#cdf4fe','#cdf4fe','#cdf4fe','#cdf4fe','#ffffff','#ffffff','#ffffff','#ffffff');				 	 $filteredListings = getAndFilterListings();	 	 	 	 // Find max and min values for the preference values	$roomsMax = 0;	$areaMax = 0;	$priceMax = 0;	$priceSquareMax = 0;	$travelTimeMax = 0;	$rentMax = 0;	$roomsMin = 20;	$areaMin = 100000;	$priceMin = 1000000000000;	$priceSquareMin = 10000000000;	$travelTimeMin = 10000000000000;	$rentMin = 100000000;	 	 foreach ($filteredListings as $listingItem){		 $pricePerM2 = round($listingItem->listPrice/$listingItem->livingArea);		 		 if($listingItem->rooms > $roomsMax){			 $roomsMax = $listingItem->rooms;		 }		 if($listingItem->rooms < $roomsMin){			 $roomsMin = $listingItem->rooms;		 }		 if($listingItem->livingArea > $areaMax){			 $areaMax = $listingItem->livingArea;		 }		 if($listingItem->livingArea < $areaMin){			 $areaMin = $listingItem->livingArea;		 }		 if($listingItem->listPrice > $priceMax){			 $priceMax = $listingItem->listPrice;		 }		 if($listingItem->listPrice < $priceMin){			 $priceMin = $listingItem->listPrice;		 }		 if($pricePerM2 > $priceSquareMax){			 $priceSquareMax = $pricePerM2;		 }		 if($pricePerM2 < $priceSquareMin){			 $priceSquareMin = $pricePerM2;		 }		 if(!is_null($listingItem->travel_time)){			 if($listingItem->travel_time > $travelTimeMax){				 $travelTimeMax = $listingItem->travel_time;			 }			 if($listingItem->travel_time < $travelTimeMin){				 $travelTimeMin = $listingItem->travel_time;		 } 		 }		  if($listingItem->rent > $rentMax){			 $rentMax = $listingItem->rent;		 }		 if($listingItem->rent < $rentMin){			 $rentMin = $listingItem->rent;		 }	 }	 	 // Preference color gradient	 $colorIndexLow = lineargradient(		  255, 255, 0,   // rgb of the start color		  255, 0, 0, // rgb of the end color		  51          // number of colors in your linear gradient		);	$colorIndexHigh = lineargradient(		  0, 255, 0,   // rgb of the start color		  255, 255, 0, // rgb of the end color		  51          // number of colors in your linear gradient		);	//$colorIndex = array_merge($colorindexLow, $colorindexHig);	// Merge the color ranges	foreach ($colorIndexHigh as $v) {		$colorIndexLow[] = $v;	}	$colorIndex = $colorIndexLow;		// Create js array to keep all lat-lang locations in	echo "<script>		var latLangArray = [];		</script>";		// Iterate all optimized listings to echo them in the DOM-structure	$rank = 1;	foreach ($filteredListings as $listingItem){		// Echo an array in js with lat-lang for listings to be used for positioning of markers		$arrayIndex = $rank - 1;		echo "<script>		var myLatLng = {lat: ".$listingItem->location->position->latitude.", lng: ".$listingItem->location->position->longitude."};		latLangArray[".$arrayIndex."] = myLatLng;		</script>";				if($rank <= 9){			$rankClassNameStr = "rankingSingle";		}else{			$rankClassNameStr = "rankingDouble";		}				// Get rent variable		$rent = $listingItem->rent;		$rentString = "";		if(!is_null($rent)){			$rent = $rent." kr/månad";			$rentString = "<li class=\"li_rent\">".$rent."</li>";		}				// Get travel time variable		$travelTime = $listingItem->travel_time;		$travelTimeString = "";		if(!is_null($travelTime)){			$travelTime = $travelTime." min restid";			$travelTimeString = "<li class=\"li_dist\">".$travelTime."</li>";		}				// Variable to get printed in the rank circle		$printRank = $rank; 		if($rank > 20){			$printRank = "";		}				// Preference accuracy colors				$roomsColorIndex = ($listingItem->rooms - $roomsMin)/($roomsMax - $roomsMin);		//echo $roomsColorIndex."<br>";		$areaColorIndex = ($listingItem->livingArea - $areaMin)/($areaMax - $areaMin);		//echo $areaColorIndex."<br>";		$priceColorIndex = ($listingItem->listPrice - $priceMin)/($priceMax - $priceMin);		//echo $priceColorIndex."<br>";		$priceSquareColorIndex = (round($listingItem->listPrice/$listingItem->livingArea) - $priceSquareMin)/($priceSquareMax - $priceSquareMin);		//echo $priceSquareColorIndex."<br>";		$distanceColorIndex = ($listingItem->travel_time - $travelTimeMin)/($travelTimeMax - $travelTimeMin);		//echo $distanceColorIndex."<br>";		$rentColorIndex = ($listingItem->rent - $rentMin)/($rentMax - $rentMin);				$roomsColor = (string)stringHEXLength($colorIndex[round($roomsColorIndex*100)]);		//echo $roomsColor;		$areaColor = (string)stringHEXLength($colorIndex[round($areaColorIndex*100)]);		//echo $areaColor;		$priceColor = (string)stringHEXLength($colorIndex[round((1-$priceColorIndex)*100)]);		//echo $priceColor;		$priceSquareColor = (string)stringHEXLength($colorIndex[round($priceSquareColorIndex*100)]);		//echo $priceSquareColor;		$distanceColor = (string)stringHEXLength($colorIndex[round((1-$distanceColorIndex)*100)]);		//echo $distanceColor;		$rentColor = (string)stringHEXLength($colorIndex[round((1-$rentColorIndex)*100)]);		//echo $rentColor;				$pricePerM2 = round($listingItem->listPrice/$listingItem->livingArea);		$listURL = "<div class=\"bgc-fff box-shad property-listing\" id=\"testRemove".$rank."\" onmouseover=\"startBounce(".$arrayIndex."); \" onmouseout=\"endBounce(".$arrayIndex.")\">                     			<svg height=\"40\" width=\"40\">  								<circle cx=\"20\" cy=\"20\" r=\"14\" stroke=\"black\" stroke-width=\"0\" fill=\"".$markerColorArray[$arrayIndex]."\" />								</svg>                                                                <h3 class=\"".$rankClassNameStr."\">".$printRank."</h3>                                                              <div class=\"left_col\">                               <h2 class=\"road\">".$listingItem->location->address->streetAddress."</h2>                                <h2 class=\"city\">".$listingItem->location->region->municipalityName."</h2>                                <p>".$listingItem->objectType."</p>                               </div>                                                  			<div class=\"right_col\">                                      <ul class=\"opti_list".$rank."\">                                    <li class=\"li_rooms\">".$listingItem->rooms." rum</li>                                    <li class=\"li_living\">".$listingItem->livingArea." m<span class=sup>2</span></li>                                                                        <li class=\"li_price\">".$listingItem->listPrice." kr</li>                                    <li class=\"li_m2\">".$pricePerM2." kr/m<span class=sup>2</span></li>                                    ".$travelTimeString."									".$rentString."                                </ul>                                                                <div class=\"closeSymbol\">    									 <img src=\"closeSymbol.svg\" width=\"25\" height=\"25\" onclick=\"removeDiv('testRemove".$rank."');\" />								</div>                                </div>								<a href=\"".$listingItem->url."\"><span class=\"clickable\"></span></a>																                    </div>";		echo $listURL;				// Color the bullets according to the preference accuracy		echo "<style>ul.opti_list".$rank." li.li_rooms:before {   			 background-color: ".$roomsColor.";			}</style>";		echo "<style>ul.opti_list".$rank." li.li_living:before {   			 background-color: ".$areaColor.";			}</style>";		echo "<style>ul.opti_list".$rank." li.li_price:before {   			 background-color: ".$priceColor.";			}</style>";		echo "<style>ul.opti_list".$rank." li.li_m2:before {   			 background-color: ".$priceSquareColor.";			}</style>";		if(!is_null($listingItem->travel_time)){			echo "<style>ul.opti_list".$rank." li.li_dist:before {				 background-color: ".$distanceColor.";				}</style>";		}		if(!is_null($listingItem->rent)){			echo "<style>ul.opti_list".$rank." li.li_rent:before {				 background-color: ".$rentColor.";				}</style>";						$rank = $rank + 1;		}	}				 ?>                        </div><!-- End container -->                                     </div>  <input id="pac-input" class="controls" type="text" placeholder="Search Box">  <div id="mapContainer">    <div id="map"></div>  </div></div><script async        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU8ulu4zkItT-o-pEUYrBs-jpzW0aHDfk&libraries=visualization&libraries=places&callback=initMap">    </script>   </body></html>