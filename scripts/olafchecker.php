#!/usr/bin/php
<?php
require_once("phpMQTT/phpMQTT.php");

$topic = "olaf/value"; // EDIT ME!
$mqtthost = "127.0.0.1";	// EDIT ME!

if(file_exists("olaf.json")){
	$data = file_get_contents("olaf.json");
	$arr = json_decode($data, true);
} else {
	die();
}

for($row = 0;$row<count($arr);$row++){
if(isset($arr[$row]['hours'])){ $hours = $arr[$row]['hours']; } else { $hours = "0"; }
if(isset($arr[$row]['mins'])){ $minutes = $arr[$row]['mins']; } else { $minutes = "00"; }
if(isset($arr[$row]['checked'])){ $checked = $arr[$row]['checked']; } else { $checked = ""; }
if(isset($arr[$row]['speed'])){ $speed = $arr[$row]['speed']; } else { $speed = "0"; }
if(isset($arr[$row]['red'])){ $red = $arr[$row]['red']; } else { $red = 0; }
if(isset($arr[$row]['green'])){ $green = $arr[$row]['green']; } else { $green = 0; }
if(isset($arr[$row]['blue'])){ $blue = $arr[$row]['blue']; } else { $blue = 0; }

$time = $hours.":".$minutes;
if($time == strftime("%R")){
	if($checked== "checked"){ $blink = "1"; } else { $blink = 0; };
	setOlaf("$blink,$red,$green,$blue,$speed");
}


}

function setOlaf($string){
	global $mqtthost, $topic;
	$mqtt = new phpMQTT($mqtthost, 1883, "olafchanger");
        if ($mqtt->connect()) {
                $mqtt->publish($topic,$string,0,1); //retain is on, to ensure olaf returns to the last state in case a reconnect occurs
                $mqtt->close();
        }
}
