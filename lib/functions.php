<?php

function rmkdir($path, $chmod = '777') {
    $exp = explode("/", $path);
    $way = '';
    foreach ($exp as $n) {
        $way .= $n.'/';
        if (!file_exists($way))
            mkdir($way, $chmod);
    }
}

function iif($expression, $true, $false='') {		
	return ($expression ? $true : $false);		
}

function formatdate($timeformat, $timestamp, $replacetoday=0) {
	global $config;

	$summertime = date("I", $timestamp)*3600;
	$timestamp += 3600*intval(1)+$summertime;
	if ($replacetoday == 1) {
		if (gmdate("Ymd", $timestamp) == gmdate("Ymd", time()+3600*intval(1)+$summertime)) {
   			return 'Heute';
  		} elseif (gmdate("Ymd", $timestamp) == gmdate("Ymd",time()-86400+3600*intval(1)+$summertime)) {
  			return 'Gestern';
  		}
	}

	return gmdate($timeformat, $timestamp);
}

function formatnumber($number) {
	if ($number == (int)$number) $i = 0;
	else $i = 2;
	return number_format($number,$i,',','.');
}

function formatsize($size) {
	if ($size >= 1073741824) { return round(($size / 1073741824), 2)." GB"; }
	elseif ($size >= 1048576) { return round(($size / 1048576), 2)." MB"; }
	elseif ($size >= 1024) { return round(($size / 1024), 2)." KB"; }
	else { return $size." Byte"; }
}

function in_string($needle, $haystack, $insensitive = 0) {
	if ($insensitive) {
		return (false !== stristr($haystack, $needle)) ? true : false;
	} else {
		return (false !== strpos($haystack, $needle))  ? true : false;
	}
}

function read_recursiv($path) {	
	$result = array();
	$handle = opendir($path);
	if ($handle) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
                $name = $path . "/" . $file;
                if ($name == "./images") continue;
                if ($name == "./logs") continue;
                if (preg_match("/[A-Za-z0-9]+\.js/isU", $name)) continue;
                if (preg_match("/[A-Za-z0-9]+\.css/isU", $name)) continue;
                if (preg_match("/[A-Za-z0-9]+\.sql/isU", $name)) continue;
                if (is_dir($name)) {
                	$ar = read_recursiv($name);
                	foreach ($ar as $value) {
                		$result[] = $value;
                	}
                } else {
                   $result[] = $name;
                }
			}
		}
	}
	closedir($handle);
	return $result;	
}

function inarray(&$array, $needle) {
	foreach ($array as $key => $value) {
		if ($value == $needle || $key == $needle)
           return true;
     	else
     		if(is_array($value))
     			inarray($value,$needle);
     		else
     			return false;
	}
}

function getdifftime($time) {
	$difftime = (time() - $time);

	$weeks = floor($difftime / (7*(24*3600)) );
	$difftime = $difftime - ($weeks * (7*(24*3600)));
	$days = floor($difftime / (24*3600));
	$difftime = $difftime - ($days * (24*3600));
	$hours = floor($difftime / (3600));
	$difftime = $difftime - ($hours * (3600));
	$minutes = floor($difftime /(60));
	$difftime = $difftime - ($minutes * 60);
	$seconds = $difftime;

	if (!eregi("[0-9]{2}", $seconds)) $seconds = '0'.$seconds;
	if (!eregi("[0-9]{2}", $minutes)) $minutes = '0'.$minutes;
	if (!eregi("[0-9]{2}", $hours)) $hours = '0'.$hours;

	if ($weeks == 1) $weeks = $weeks .' Woche';
	elseif ($weeks == 0) $weeks = '';
	else $weeks = $weeks .' Wochen';
	if ($days == 1) $days = $days .' Tag';
	elseif ($days == 0) $days = '';
	else $days = $days .' Tage';
	if ($hours == 1) $hours = $hours .' Stunde';
	elseif ($hours == 0) $hours = '';
	else $hours = $hours .' Stunden';

	if ($minutes == 1) $minutes = $minutes .' Minute';
	elseif ($minutes == 0) $minutes = '';
	else $minutes = $minutes .' Minuten';

	if ($seconds == 1) $seconds = $seconds .' Sekunde';
	else $seconds = $seconds .' Sekunden';

	return $weeks.' '.$days.' '.$hours.' '.$minutes.' '.$seconds;
}

?>