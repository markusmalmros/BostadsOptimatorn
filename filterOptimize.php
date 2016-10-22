<?php	  	  function getAndFilterListings(){		// Get e-mail adress and save to file	$email = htmlspecialchars($_POST['mail']);	file_put_contents("emails.txt", $email . "\n", FILE_APPEND);		// Print all of the values of the preferences	$reqMaxPrice = htmlspecialchars($_POST['pris']); 	$reqMaxAvgift = htmlspecialchars($_POST['avgift']);	$prefMaxPrice = (int)$_POST['slider_price']/100;	$prefMaxAvgift = (int)$_POST['slider_avgift']/100; 	echo "Pris: ",$reqMaxPrice," Avgift: ",$reqMaxAvgift," Pref Pris: ",$prefMaxPrice," Pref Avgift: ",$prefMaxAvgift,"<br>";		$reqRooms = htmlspecialchars($_POST['size-rooms']);	$prefRooms =  (int)$_POST['slider_rooms']/100; 	$reqSize = htmlspecialchars($_POST['size-squarem']);	$prefSize = (int)$_POST['slider_kvm']/100;	$reqYard = htmlspecialchars($_POST['size-yard']);	$prefYard = (int)$_POST['slider_yard']/100;	echo "Rum: ",$reqRooms," Storlek: ",$reqSize," Tomt: ",$reqYard,"Pref rum: ",$prefRooms," Pref storlek: ",$prefSize," Pref tomt ",$prefYard,"<br>";	 	$reqAge = htmlspecialchars($_POST['age']);	$prefAge = (int)$_POST['slider_age']/100;		$reqTransport = $_POST['transportmetod'];	$prefLocation = htmlspecialchars($_POST['slider_location']);	$location = htmlspecialchars($_POST['LongLat']); // Marker position		// Rectangle stuff	$rectangle = htmlspecialchars($_POST['rectangle']);	$rectangle_bounds = explode(",", $rectangle, 4); // North lat, east long, south lat, west long	echo "RECTANGLE: ".$rectangle."<br>";			echo "Ålder: ",$reqAge," Pref Ålder: ",$prefAge," Transport: ",$reqTransport," Plats: ",$location," Pref plats: ",$prefLocation,"<br>";		$reqTyp = htmlspecialchars($_POST['boTyp']);	//echo "Bostadstyp: ",$reqTyp,"<br><br>";			// Get latlong for setting the map to the required starting location		setLocationToMap($rectangle_bounds);			//$listings is an array with each entry being a JSON object	$listings = getListings($location, $rectangle_bounds);   		$json_string = json_encode($aListing, JSON_PRETTY_PRINT);	//echo $json_string;		///////////////////////////////////// FILTER ////////////////////////////////////////////////////////////////		// Listings within distance will be kept	//$distanceLimit = 101000-sqrt($prefLocation*pow(10,8));	$distanceLimit = 150000-sqrt($prefLocation*pow(10,8));	echo "Distance Limit: ".$distanceLimit."<br>";	//$reqMaxPrice = 2000000;	//$reqMaxAvgift = 5000;	//$reqRooms = 1;	//$reqSize = 20;		$priceLimit = $reqMaxPrice * 1.5;	$rentLimit = $reqMaxAvgift * 1.5;	$roomLimit = $reqRooms - 1;	$areaLimit = $reqSize * 0.6;		//echo "<br>".$priceLimit."<br>";	//echo "<br>".$rentLimit."<br>";	//echo "<br>".$roomLimit."<br>";	//echo "<br>".$areaLimit."<br>";		$filteredListings = array();	foreach($listings as $listItem){		$objectType = $listItem->objectType;		$listPrice = $listItem->listPrice;		$listRooms = $listItem->rooms;		$listArea = $listItem->livingArea;				$listRent = $listItem->rent;		$listfloor = $listItem->floor;		$listYear = $listItem->constructionYear;		$listYard = $listItem->plotArea;		if($location != "empty"){			$distance = $listItem->distance;		}		$listLatitude = $listItem->location->position->latitude;		$listLongitude = $listItem->location->position->longitude;			// filter non-allowed listing types		if($objectType == $reqTyp || ($reqTyp == Villa && ($objectType == 'Parhus' || $objectType == 'Radhus'))){			// Filter listings without list price or house area			if(!is_null($listArea) && !is_null($listPrice) && !is_null($listRooms)){				// Filter based on distance				if($listRooms > $roomLimit && $listPrice < $priceLimit && $listArea > $areaLimit){								// Add item to array					//echo $objectType."<br>";					$filteredListings[] = $listItem;				}			}		}	}		$echo_listins = true;		// OPTIMIZE	foreach($filteredListings as $listing){		$objectType = $listing->objectType;		$score = 0;				$listPrice = $listing->listPrice;		$priceDiff = ($reqMaxPrice - $listPrice)/$reqMaxPrice;		$priceWeight = 1.2;		$priceScore = $priceWeight * $priceDiff * $prefMaxPrice;		$score = $score + $priceScore;				$listRooms = $listing->rooms;		$roomDiff = -($reqRooms - $listRooms)/$reqRooms;		$roomScore = $roomDiff * $prefRooms;		$score = $score + $roomScore;				$listArea = $listing->livingArea;		$areaDiff = -($reqSize - $listArea)/$reqSize;		$areaScore = $areaDiff * $prefSize;		$score = $score + $areaScore;					$listRent = $listing->rent;		$rentDiff = ($reqMaxAvgift - $listRent)/$reqMaxAvgift;		$rentScore = $rentDiff * $prefMaxAvgift;		$score = $score + $rentScore;					//$listfloor = $listing->floor;				$ageDiff = 0;		$listYear = $listing->constructionYear;		if(!is_null($listYear)){			//TODO: Change to current year			$listAge = 2016 - $listYear;			$ageWeight = 0.5;			$ageDiff = $ageWeight*($reqAge - $listAge)/$reqAge;			$ageScore = $ageDiff * $prefAge;			$score = $score + $ageScore;					}				$listYard = $listing->plotArea;		if(!is_null($listYard)){			$yardDiff = -($reqYard - $listYard)/$reqYard;			$yardScore = $yardDiff * $prefYard;			$score = $score + $yardScore;					}		// Make distance scoring non linear		if($location != "empty"){			$distance = $listing->distance;			$distanceDiff = 1 - $distance/$distanceLimit;			$distanceScore = $distanceDiff * $prefLocation / 100;			$score = $score + $distanceScore;		}				$listing->score = $score;				// echo listings		if($echo_listings){			echo $objectType."<br>";			echo "Pris:".$listPrice."<br>";			echo "Pris score:".$priceScore."<br>";			echo "Rum: ".$listRooms."<br>";			echo "Rum score: ".$roomScore."<br>";			echo "Area: ".$listArea."<br>";				echo "Area score: ".$areaScore."<br>";			echo "Hyra: ".$listRent."<br>";			echo "Hyra score: ".$rentScore."<br>";			echo "Våning: ".$listfloor."<br>";			echo "År: ".$listYear."<br>";			echo "År score: ".$ageScore."<br>";			echo "Trädgård: ".$listYard."<br>";			echo "Trädgård score: ".$yardScore."<br>";			if($location != "empty"){				echo "Distance: ".$distance."<br>";			}			echo "Distance score: ".$distanceScore."<br>";							echo "SCORE: ".$score."<br><br>";		}	}			// Pick the best listings	$nbrListings = 20;	$orderedListings = getBestListings($filteredListings,$nbrListings);		// Build the arrays for finding travel time	//$nListingsToKeep = 20;	//$opt_listings = array_slice($filteredListings, 0, $nListingsToKeep);	if($location != "empty"){		$listingsTravelTime = getTravelTime($location,$orderedListings,$reqTransport);		//Add a second scoring and sort again		foreach($listingsTravelTime as $listing){			$score = $listing->score;			$travelTime = $listing->travel_time;			if(!is_null($travelTime)){				$travelDiff = (60-$travelTime) * $prefLocation /10000;				//echo "Traveltime score: ".$travelDiff;				$score = $score + $travelDiff; 			}			$listing->score = $score;		}		$nbrFinalListings = 10;		$bestListings = getBestListings($listingsTravelTime, $nbrFinalListings);		}else{		$bestListings = getBestListings($orderedListings, 10);;	}			return $bestListings;	  }	  //////////////////////// GET BEST LISTINGS ///////////////////////////////////// Input: $nbrListings - Decides how many listings to keep// Input: $filteredListings - Array of listings// Output: The best listings determined by score value	  	function getBestListings($listings,$nbrListings){  	  // Pick the best listings		$orderedListings = array();	for ($i = 1; $i <= $nbrListings; $i++) {		$maxScore = 0;		$maxIndex = NULL;    	foreach($listings as $key => $listing){			if($listing->score > $maxScore){				$maxIndex = $key;				$maxScore = $listing->score;			}		}		//echo "SCOre: ".$maxScore."<br>";		$orderedListings[] = $listings[$maxIndex];		unset($listings[$maxIndex]); // remove item at index 0		$listings = array_values($listings); // 'reindex' array	}	return $orderedListings;	}	////////////////////// GET TRAVEL TIMES ///////////////////////////////////////////////////////////////////////////function getTravelTime($origin,$listings,$reqTransport){// Build url for HTTP$url ="https://maps.googleapis.com/maps/api/distancematrix/json?origins=";	foreach($listings as $listing){	$url = $url . $listing->location->position->latitude . ",";	$url = $url . $listing->location->position->longitude . "|";}$url = rtrim( $url, "|");$origin = str_replace(array(" ","(",")"), "", $origin);$arr = explode(",", $origin, 2);echo "<script>var destinationLatLong = {lat: ".$arr[0].", lng: ".$arr[1]."};</script>";$url = $url . "&destinations=" . $origin;$url = $url . "&mode=";if($reqTransport == "bil"){	$url = $url . "driving";}elseif($reqTransport == "lokaltrafik"){	$url = $url . "transit";}elseif($reqTransport == "cykla"){	$url = $url . "bicycling";}else{	$url = $url . "walking";}$url = $url . "&key=AIzaSyAA99PM_Xfqfhc8KTHSF3LwO6iXMGx0TrM";/*echo "<script type='text/javascript'>alert('$url');</script>";*///$url="https://maps.googleapis.com/maps/api/distancematrix/json?origins=Vancouver+BC|Seattle&destinations=San+Francisco|Victoria+BC&key=AIzaSyAA99PM_Xfqfhc8KTHSF3LwO6iXMGx0TrM";$curl = curl_init($url);	  curl_setopt_array($curl, array(	CURLOPT_RETURNTRANSFER => true));	 	  // $response is a JSON string$response = curl_exec($curl); 	  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);	  curl_close($curl);if ($httpCode != 200) {	/*echo "<script type='text/javascript'>alert('fail');</script>";*/}else{	/*echo "<script type='text/javascript'>alert('succses');</script>";*/}// $result is a JSON object  $result = json_decode($response);//$stat = $result->status;$rows = $result->rows;// Add travel times to listings array$index = 0;foreach($listings as $listing){	$row = $rows[$index];	$elements = $row->elements;	$element = $elements[0];	$travel_time = $element->duration->value;	$travel_time = round($travel_time/60);	$listing->travel_time = $travel_time;	$index = $index + 1;}return $listings; }?>