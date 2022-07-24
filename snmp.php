<?php
	//$router  = "10.10.16.81";
	
	//$time = 1;
	
function pull_value($router){
	$oid_in  = ".1.3.6.1.2.1.2.2.1.10.1";
	$oid_out = ".1.3.6.1.2.1.2.2.1.16.1";
	$pull["in"]  = (int) str_replace("Counter32: ", "", snmpget($router, "public", $oid_in));
	$pull["out"] = (int) str_replace("Counter32: ", "", snmpget($router, "public", $oid_out));
	
	return $pull;
}

function calculate($router){
	$first_pull = pull_value($router);
	sleep(1);
	$second_pull = pull_value($router);
	
	$res["LAN_RX"] = round((($second_pull["in"] - $first_pull["in"]) / 1) * 8 / 1024 , 2);
	$res["LAN_TX"] = round((($second_pull["out"] - $first_pull["out"]) / 1) * 8 / 1024 , 2);
	
	return $res;
}


	//$out = calculate($router, $time);

	//print("LAN_IN: ".$out["LAN_RX"]);
	//echo "<br>";
	//print("LAN_OUT: ".$out["LAN_TX"]);
?>