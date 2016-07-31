<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="initial-scale=1.0, user-scalable=no"><title>BostadsOptimatorn</title><link rel="stylesheet" type="text/css" href="listingPage.css" media="screen" /><link rel="stylesheet" type="text/css" href="listStyle.css" media="screen" /><script src="../../../BostadsOptimatorn/WebPage/public_html/chroma.js/chroma.js"></script><script type="text/javascript" src="showListingsPage.js" ></script></head><body><div id="container">  <div id="header"> <img class="logoImg" src="BostadsOptimatornLogo1.png" alt="Logotype" width="260" height="65"> </div>  <div id="form">    <div class="container container-pad brdr" id="property-listings">    <?php 	include 'getListings.php';	include 'getTravelTime.php';		// Multiple color array	//$markerColorArray = array('#00cc00','#00cc00','#66ff33','#66ff33','#99ff33','#99ff33','#ccff33','#ccff33','#ffff00','#ffff00','#ffcc00','#ffcc00','#ff9933','#ff9933','#ff6600','#ff6600','#ff5050','#ff5050','#cc0000','#cc0000');	// Five shades	$markerColorArray = array('#02a9d5','#02a9d5','#02a9d5','#02a9d5','#03c6fc','#03c6fc','#03c6fc','#03c6fc','#9ae8fe','#9ae8fe','#9ae8fe','#9ae8fe','#cdf4fe','#cdf4fe','#cdf4fe','#cdf4fe','#ffffff','#ffffff','#ffffff','#ffffff');		// Print all of the values of the preferences	$reqMaxPrice = htmlspecialchars($_POST['pris']); 	$prefMaxPrice = (int)$_POST['slider_price']; 		$reqRooms = htmlspecialchars($_POST['size-rooms']);	$prefRooms =  (int)$_POST['slider_rooms']; 	 	$reqSize = htmlspecialchars($_POST['size-square']);	$prefSize = (int)$_POST['slider_kvm'];	 	$reqAge = htmlspecialchars($_POST['age']);	$prefAge = (int)$_POST['slider_age'];	 	 $reqObjectType = "Villa"; //Alternativ: Villa : Fritidshus : Lägenhet : Gård : Tomt/Mark	 //Get the location from the POST	$location = htmlspecialchars($_POST['LongLat']);		// Get latlong for setting the map to the required starting location	setLocationToMap($location);			//$listings is an array with each entry being a JSON object	$listings = getListings($location);   	$aListing = $listings[0];	$json_string = json_encode($aListing, JSON_PRETTY_PRINT);	//echo $json_string;		// Filter out unsuitable listings	$filteredListings = array();	foreach($listings as $listItem){		echo $listItem->objectType;		$objectType = $listItem->objectType;			}				// Build the arrays for finding travel time	$nListingsToKeep = 20;	$opt_listings = array_slice($listings, 0, $nListingsToKeep);	$transit = True;	$listings_travel_time = getTravelTime($location,$opt_listings,$transit);			// Get all of the important features 	/*echo "<br>";echo "<br>";	echo $aListing->location->address->streetAddress;	echo "<br>";	echo $aListing->location->region->municipalityName;	echo "<br>";	echo $aListing->booliId;	echo "<br>";	echo $aListing->listPrice;	echo "<br>";	echo $aListing->source->url;	echo "<br>";	echo $aListing->location->position->latitude;	echo " ";	echo $aListing->location->position->longitude;	echo "<br>";	echo $aListing->objectType;	echo "<br>";	echo $aListing->rooms;	echo "<br>";	echo $aListing->livingArea;	echo "<br>";	echo $aListing->rent;	echo "<br>";	echo $aListing->floor;	echo "<br>";	echo $aListing->constructionYear;	echo "<br>";	echo $aListing->url;	echo "<br>";	echo $aListing->plotArea;	*/	 //var_dump($listings); //Will display the structure of data	 		// Create js array to keep all lat-lang locations in	echo "<script>		var latLangArray = [];		</script>";		// Iterate all optimized listings to echo them in the DOM-structure	$rank = 1;	foreach ($listings_travel_time as $listingItem){		// Echo an array in js with lat-lang for listings to be used for positioning of markers		$arrayIndex = $rank - 1;		echo "<script>		var myLatLng = {lat: ".$listingItem->location->position->latitude.", lng: ".$listingItem->location->position->longitude."};		latLangArray[".$arrayIndex."] = myLatLng;		</script>";				if($rank <= 9){			$rankClassNameStr = "rankingSingle";		}else{			$rankClassNameStr = "rankingDouble";		}				// Variable to get printed in the rank circle		$printRank = $rank; 		if($rank > 20){			$printRank = "";		}				$pricePerM2 = round($listingItem->listPrice/$listingItem->livingArea);		$listURL = "<div class=\"bgc-fff box-shad property-listing\" id=\"testRemove".$rank."\" onmouseover=\"startBounce(".$arrayIndex."); \" onmouseout=\"endBounce(".$arrayIndex.")\">                     			<svg height=\"40\" width=\"40\">  								<circle cx=\"20\" cy=\"20\" r=\"14\" stroke=\"black\" stroke-width=\"0\" fill=\"".$markerColorArray[$arrayIndex]."\" />								</svg>                                                                <h3 class=\"".$rankClassNameStr."\">".$printRank."</h3>                                                              <div class=\"left_col\">                               <h2 class=\"road\">".$listingItem->location->address->streetAddress."</h2>                                <h2 class=\"city\">".$listingItem->location->region->municipalityName."</h2>                                <p>".$listingItem->objectType."</p>                                                                                                                          </div>                                                                                    			<div class=\"right_col\">                                                                     <ul class=\"opti_list\">                                    <li>".$listingItem->rooms." rum</li>                                    <li>".$listingItem->livingArea." m<span class=sup>2</span></li>                                                                        <li>".$listingItem->listPrice." kr</li>                                    <li>".$pricePerM2." kr/m<span class=sup>2</span></li>                                    <li>15 min till viktig plats</li>                                </ul>                                                                <div class=\"closeSymbol\">    									 <img src=\"closeSymbol.svg\" width=\"25\" height=\"25\" onclick=\"removeDiv('testRemove".$rank."');\" />								</div>                                                                <a href=\"".$listingItem->url."\">Booli länk till bostad</a>                                </div>                    </div>";		echo $listURL;		$rank = $rank + 1;	}		 ?>                                                                               	<!-- Begin Listing: 609 W GRAVERS LN-->                	 <div class="bgc-fff box-shad property-listing" id="testRemove1" onmouseover="this.style.background='gray';" onmouseout="this.style.background='white';">                     			<svg height="40" width="40">  								<circle cx="20" cy="20" r="12" stroke="black" stroke-width="0" fill="#02A9D5" />								</svg>                                                                <h3 class="rankingSingle">2</h3>                                                              <div class="left_col">                               <h2 class="road">Årevägen 35A</h2>                                <h2 class="city">Åre</h2>                                <p>Bostadsrätt</p>                                                                                                                          </div>                                                                                    			<div class="right_col">                                                                     <ul class="opti_list">                                    <li>4 rum</li>                                    <li>57 m<span class=sup>2</span></li>                                                                        <li>650 000 kr</li>                                    <li>67000 kr/m<span class=sup>2</span></li>                                    <li>15 min till viktig plats</li>                                </ul>                                                                <div class="closeSymbol">    									 <img src="closeSymbol.svg" width="25" height="25" onclick="removeDiv('testRemove1');" />								</div>                                                                <a href="https://www.booli.se/annons/2137934">Booli Länk</a>                                </div>                    </div><!-- End Listing-->                                                       </div><!-- End container -->                                     </div>  <input id="pac-input" class="controls" type="text" placeholder="Search Box">  <div id="mapContainer">    <div id="map"></div>  </div></div><script async        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU8ulu4zkItT-o-pEUYrBs-jpzW0aHDfk&libraries=visualization&libraries=places&callback=initMap">    </script>   </body></html>