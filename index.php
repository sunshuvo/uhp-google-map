<?php /*
	date_default_timezone_set("Asia/Dhaka");
	ini_set('session.gc_maxlifetime', 0);
	require("function/session.php");
	require("function/function.php");
	if(!isset($_SESSION["username"])){redirect_to("login.php");}
	$dbtable="trip";
	$dbtable2="user";
	$connect=mysql_conn();
	*/
	//require("snmp.php");
	require("function.php");
	$page = $_SERVER['PHP_SELF'];
	$sec = "60";


//Remtoe State List Generate
	function loading(){
		$remote_state_list_decode=json_decode(uhpapi("remotestate","list",""), true);
		$state_list=$remote_state_list_decode["data"];

		$children = array();
		$file = uniqid().".txt";

		foreach($state_list as $state_list) {
			$remote_id = $state_list["remote_id"];
			$core_state = $state_list["core_state"];
			usleep(300000);
			$pid = pcntl_fork();
			if ($pid === -1) {}
			else if ($pid === 0) {
				shell_exec("php multi.php '".escapeshellarg($remote_id)."' '".escapeshellarg($core_state)."' '".escapeshellarg($file)."'");
				posix_kill(getmypid(), SIGKILL);
			}
			else {
				$children[] = $pid;
			}
		}
		foreach ($children as $pid) {
			pcntl_waitpid($pid, $status);
		}
		$station_status = file_get_contents("/var/www/html/tmp/".$file."");
		$station_status = "[".rtrim($station_status, ",")."]";
		$station_status = json_decode( $station_status, true );
		shell_exec("rm -f /var/www/html/tmp/".$file."");
		
		return $station_status;
	}
	
//Remtoe State List Generate
?>



<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
<title>UHP VSAT MAP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet" href="css/style.css">
</head>

	

<body>
	<!--Heading Start-->
	<?php //$active=1; require("header.php"); ?>
	<!--Heading End-->

	<main>
		<section class="map">
			<div class="container">
				<div class="my-3 text-center" style="matgin-top:50px;">
					<h2>Remote VSAT installed under UHP HUB</h2>
				</div>
			
				<div class="my-3" id="map" style="width: 100%; height: 400px;"></div>
				
			</div>
		</section>
		
		
	</main>
	
	<!--Footer Start-->
	<?php require("footer.php"); ?>
	<!--Footer End-->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
	<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBoal1P1ZuOv9iMzzhQnXySRzEF1sjHanE&callback=initMap" type="text/javascript"></script>
    <script type="text/javascript">
    var locations = [
		<?php
			$station_status = loading();
			
			$j = 0;
			$lat=0;
			$lon=0;
			foreach($station_status as $i => $item){
				if($item["core_state"]==7){ if($item["station_tx"]>50 or $item["station_rx"]>50){$icon = "img/marker/dot_glow.gif";} else{$icon = "img/marker/dot_green.svg"; }}
				if($item["core_state"]==5){ $icon = "img/marker/dot_red.svg"; }
				if($item["core_state"]==1){ $icon = "img/marker/dot_blue.svg"; }
				
				if($item["station_lat"]>0 and $item["station_lon"]>0){
					echo "[".$item["remote_id"].", '".$item["station_name"]."', ".$i.", '".$icon."', ".$item["station_lat"].", ".$item["station_lon"].", ".$item["station_rx"].", ".$item["station_tx"].", 'http://192.168.168.162/#/remote_dashboard/".$item["remote_id"]."/'],";
					$lat = $lat + $item["station_lat"];
					$lon = $lon + $item["station_lon"];
					$j++;
				}
				
			}
			$lat_avg = $lat/$j;
			$lon_avg = $lon/$j;
		?>
    ];

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(<?php echo $lat_avg.",".$lon_avg;?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
	
	var bounds = new google.maps.LatLngBounds(); //For Auto Zoom

    var infowindow = new google.maps.InfoWindow(); 

    var marker, i;
	
    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][4], locations[i][5]),
        map: map,
        icon: locations[i][3],
		url: locations[i][6]
      });
	  
		loc = new google.maps.LatLng(locations[i][4], locations[i][5]); //For Auto Zoom
		bounds.extend(loc); //For Auto Zoom

      google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][1]);
          infowindow.open(map, marker);
        }
      })(marker, i));
	  
		google.maps.event.addListener(marker, 'click', function() {
			window.location.href = this.url;
		});
    }
	map.fitBounds(bounds); //For Auto Zoom
    map.panToBounds(bounds); //For Auto Zoom
  </script>
</body>
</html>