<?php
define('_VALID', 1);
define('_ENTER', true);
define('_CLI', true);

// Argvs
$vid = (int) $_SERVER['argv'][1];
$vurl = urldecode($_SERVER['argv'][2]);
$vdo_path = $_SERVER['argv'][3];
$vdoname = $_SERVER['argv'][4];
$cookie = $_SERVER['argv'][5];

// Required
$basedir = dirname(dirname(__FILE__));
require $basedir. '/include/config.php';
require $basedir. '/classes/curl.class.php';
require $basedir. '/include/function_video.php';


echo "\n".$nl."Argv\n".$nl;
echo "Parameters:\n";
echo "vdoname: $vdoname\n";
echo "vid: $vid\n";
echo "vdo_path: $vdo_path\n\n";
echo "vurl: $vurl\n\n";

// Execute
$curl           = new VCurl();

if ( !$curl->saveToFile($vurl, $vdo_path, $cookie) ) {
	$sql        = "DELETE FROM video WHERE VID = " .$vid. " LIMIT 1";
	$conn->execute($sql);
	echo "Failed to download video file!\n\n";
} else {
	function run_in_background($Command, $Priority = 0) {
		if($Priority) $PID = shell_exec("nohup nice -n $Priority $Command 2> /dev/null & echo $!");
		else $PID = shell_exec("nohup $Command 2> /dev/null & echo $!");
		return($PID);
	}
	
	$video_id = $vid;
    $duration   = get_video_duration($vdo_path, $video_id);

	$cgi = ( strpos(php_sapi_name(), 'cgi') ) ? 'env -i ' : NULL;

	$cmd = $cgi.$config['phppath']
	." ".$config['BASE_DIR']."/scripts/convert_videos.php"
	." ".$vdoname
	." ".$video_id
	." ".$vdo_path
	."";                        
	echo "CMD: $cmd\n\n";
	log_conversion($config['LOG_DIR']. '/' .$video_id. '.log', $cmd);
	$lg = $config['LOG_DIR']. '/' .$video_id. '.log2';
	//convert
	if($config['conversion_q']=='1') {
		require_once $config['BASE_DIR'].'/include/function_queue.php'; 
		insert_into_q_fp($video_id, $vdoname, $vdo_path);
	} else {
		run_in_background($cmd.' > '.$lg);
	}
	$size = filesize($vdo_path);
}

if (!$errors) {
	$vkey        = substr(md5($vid),11,20);
	$sql         = "UPDATE video SET duration = " .$conn->qStr($duration). ", 
					vkey = '" .$vkey. "', 
					vdoname = " .$conn->qStr($vdoname). ", 
					space = " .$size. " 
					WHERE VID = " .intval($vid). " LIMIT 1";
	$conn->execute($sql);
	$sql         = "UPDATE channel SET total_videos = total_videos+1 WHERE CHID = " .$category. " LIMIT 1";
	$conn->execute($sql);
	$sql		 = "UPDATE signup SET total_videos = total_videos+1 WHERE UID = ".$uid." LIMIT 1";
	$conn->execute($sql);
	echo "Video was successfully added!\n\n";
}

exit();
?>
