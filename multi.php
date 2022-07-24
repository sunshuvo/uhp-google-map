<?php
	require("snmp.php");
	require("function.php");
	
	
	$remote_id = $argv[1];
	$core_state = $argv[2];
	$file = $argv[3];

	$station_info_decode=json_decode(uhpapi("station","select",$remote_id), true);
	$station_info=$station_info_decode["data"];
	
	$station_name = $station_info["name"];
	$station_lat = $station_info["latitude"];
	$station_lon = $station_info["longitude"];
	$station_ip = $station_info["dhcp_gw"];
	
	$out["LAN_TX"] = 0;
	$out["LAN_RX"] = 0;
	if($core_state==7){
		//$out = calculate($station_ip);
	}
	
	$station_status = array("remote_id"=>$remote_id, "core_state"=>$core_state, "station_name"=>$station_name, "station_lat"=>$station_lat, "station_lon"=>$station_lon, "station_rx"=>$out["LAN_TX"], "station_tx"=>$out["LAN_RX"]);
	file_put_contents('/var/www/html/tmp/'.$file, json_encode($station_status , JSON_NUMERIC_CHECK )."," , FILE_APPEND | LOCK_EX);
?>