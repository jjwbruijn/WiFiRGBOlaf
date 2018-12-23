
<html>
<body style="font-family:arial;font-size:150%">
<?php

$topic = "olaf/value"; // EDIT ME!
$mqtthost = "127.0.0.1"; // EDIT ME!

require_once("phpMQTT/phpMQTT.php");

if(file_exists("olaf.json")){
	$data = file_get_contents("olaf.json");
	$arr = json_decode($data, true);
}


foreach($_POST as $name => $value){
	$row = substr($name,-1,1);
	if(substr($name,0,5)=="hours"){
		$arr[$row]['hours'] = $value;
	}
        if(substr($name,0,7)=="minutes"){
                $arr[$row]['mins'] = $value;
		$arr[$row]['checked'] = "";
        }
        if(substr($name,0,5)=="blink"){
                $arr[$row]['checked'] = "checked";
        }
        if(substr($name,0,5)=="speed"){
                $arr[$row]['speed'] = $value;
        }
        if(substr($name,0,3)=="red"){
                $arr[$row]['red'] = $value;
        }
        if(substr($name,0,5)=="green"){
                $arr[$row]['green'] = $value;
        }
        if(substr($name,0,4)=="blue"){
                $arr[$row]['blue'] = $value;
        }
	if(substr($name,0,4)=="test"){
		if(isset($arr[$row]['hours'])){ $hours = $arr[$row]['hours']; } else { $hours = "0"; }
		if(isset($arr[$row]['mins'])){ $minutes = $arr[$row]['mins']; } else { $minutes = "00"; }
		if(isset($arr[$row]['checked'])){ $checked = $arr[$row]['checked']; } else { $checked = ""; }
		if(isset($arr[$row]['speed'])){ $speed = $arr[$row]['speed']; } else { $speed = "0"; }
		if(isset($arr[$row]['red'])){ $red = $arr[$row]['red']; } else { $red = 0; }
		if(isset($arr[$row]['green'])){ $green = $arr[$row]['green']; } else { $green = 0; }
		if(isset($arr[$row]['blue'])){ $blue = $arr[$row]['blue']; } else { $blue = 0; }
		if($checked== "checked"){ $blink = "1"; } else { $blink = 0; };
	        setOlaf("$blink,$red,$green,$blue,$speed");
	}
}

$json = json_encode($arr);
file_put_contents("olaf.json", $json);


echo "<form method=\"POST\">\n";
for($row = 0;$row<5;$row++){
if(isset($arr[$row]['hours'])){ $hours = $arr[$row]['hours']; } else { $hours = "0"; }
if(isset($arr[$row]['mins'])){ $minutes = $arr[$row]['mins']; } else { $minutes = "00"; }
if(isset($arr[$row]['checked'])){ $checked = $arr[$row]['checked']; } else { $checked = ""; }
if(isset($arr[$row]['speed'])){ $speed = $arr[$row]['speed']; } else { $speed = "0"; }
if(isset($arr[$row]['red'])){ $red = $arr[$row]['red']; } else { $red = 0; }
if(isset($arr[$row]['green'])){ $green = $arr[$row]['green']; } else { $green = 0; }
if(isset($arr[$row]['blue'])){ $blue = $arr[$row]['blue']; } else { $blue = 0; }


echo "<input type=\"text\" name=\"hours$row\" value=\"$hours\" size=\"2\">:";
echo "<input type=\"text\" name=\"minutes$row\" value=\"$minutes\" size=\"2\"> ";
echo "<input type=\"checkbox\" name=\"blink$row\" value=\"true\" $checked> Blink  ";
echo "Speed:<input type=\"text\" name=\"speed$row\" value=\"$speed\" size=\"3\"> ";
echo "R:<input type=\"text\" name=\"red$row\" value=\"$red\" size=\"3\"> ";
echo "G:<input type=\"text\" name=\"green$row\" value=\"$green\" size=\"3\"> ";
echo "B:<input type=\"text\" name=\"blue$row\" value=\"$blue\" size=\"3\"> ";
echo "<input type=\"submit\" name=\"test$row\" value=\"Test\"><br/>";
}

echo "<br/><input type=\"SUBMIT\" name=\"Save\" value=\"Save\">\n";
echo "</form>";

function setOlaf($string){
        global $mqtthost, $topic;
        $mqtt = new phpMQTT($mqtthost, 1883, "olafchanger");
        if ($mqtt->connect()) {
                $mqtt->publish($topic,$string,0,1); //retain is on, to ensure olaf returns to the last state in case a reconnect occurs
                $mqtt->close();
        }
}
