<?

require("function.php");


$file = uniqid().".txt";

//Remtoe State List Generate
$remote_state_list_decode=json_decode(uhpapi("remotestate","list",""), true);
$state_list=$remote_state_list_decode["data"];

$children = array();

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

?>