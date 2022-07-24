<?php

//UHP API Call//
function uhpapi($section,$action, $id){
	$data= array("object"=>"$section", "action"=>"$action", "id"=>$id);
	$postdata = json_encode($data);
	$url="http://192.168.168.162/jsonapi/?token=3RzM2jnm7s32wKOtXm9pLGQhdLhCxwkpaWx9tQMvjjfIWhzkNf2u94U9ZKig2h0K";
	$crl = curl_init($url);
	curl_setopt($crl, CURLOPT_POST, 1);
	curl_setopt($crl, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($crl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($crl);
	return($result);
}
//UHP API Call//

?>