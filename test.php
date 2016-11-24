<?php

$subject = $_REQUEST['str1'];
$pattern = $_REQUEST['str2']; 

$array = getMatches($subject,$pattern);

$output['subject'] = getFullString($subject,$array,1);
$output['pattern'] = getFullString($pattern,$array,2);

echo json_encode($output);



function getMatches($subject,$pattern){
	$needle = '';
	$array = [];
	$matched = '';


	for ($i=0; $i < strlen($pattern); $i++) { 
		 
		$prevMatch = '';

		for ($j=$i; $j < strlen($pattern); $j++) { 

			if (!ctype_alnum($pattern[$j]) && $pattern[$j] != ' ') { 
				// escaping special charecters other than space
				$needle .= "\\".$pattern[$j];
			}
			else{
				$needle .= $pattern[$j];
			}

			if(preg_match("/".$needle."/",$subject,$matches,PREG_OFFSET_CAPTURE)){
				
				$matched = $matches[0];
				
				if($prevMatch != '' && $matched[1] != $prevMatch[1] ){
					$array = insertIntoArray($array,$prevMatch,$i);
				}
				
				$prevMatch = $matched;
			}
			else{ 
				break;
			}
		}
		$array = insertIntoArray($array,$matched,$i);
		$matched = '';
		$needle = '';
	}

	return $array;
}




// function for inserting matched strings and positions
// checking pattern already found at same position if found giving priority for larger string
// if pattern found before any of the previously matched substring splice all the matches after that position 
//and adding newly matched
function insertIntoArray($array, $matched,$i){
	$exists = false;
	if($matched == ''){
		return $array;
	}
	
	for ($p=0; $p < count($array); $p++) { 
		if($matched[1] ==  $array[$p][1]){ 

			if(strlen($matched[0]) <= strlen($array[$p][0]) ){
				$exists = true;
				break;
			}
			else{
				array_splice($array, $p); 
				break;
			}
			
		}
		else if ($matched[1] <  $array[$p][1] ) { 
			if(abs($i - $matched[1]) > abs($array[$p][1] - $array[$p][2] )){
				$exists = true;
				break;
			} 
			array_splice($array, $p);
			break;
		}

	} 
	if(!$exists){
		$matched[2] = $i;
		$array[] = $matched;
	}

	return $array;
}



function getFullString($string,$array,$t){ 
	$start = 0;
	$finalString ='';
	$newSub = '';
	foreach ($array as $key => $value) {
		
		if($value[$t] != 0 && $value[$t] < $start){
			continue;
		}

		$newSub = substr($string, $start,$value[$t] -$start);
		
		$finalString .= $newSub.'<span class="css'.$t.'">'.$value[0].'</span>';
		$start = $value[$t]+strlen($value[0]);
		
	}

	if($start < strlen($string)){
		$finalString .= substr($string, $start);
	}

	return $finalString;
}

 ?>
 