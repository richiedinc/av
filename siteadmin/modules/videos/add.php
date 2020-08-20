<?php
defined('_VALID') or die('Restricted Access!');

Auth::checkAdmin();

if ( !function_exists('curl_init') ) {
	$errors[] = 'You need php-curl installed to use this module. See: <a href="http://www.php.net/curl">http://www.php.net/curl</a>!';
}

require $config['BASE_DIR']. '/include/config.grabber.php';
require $config['BASE_DIR']. '/classes/curl.class.php';
require $config['BASE_DIR']. '/include/function_video.php';

$baseURL    = $config['BASE_URL'];
$categories = get_categories();
$video      = array('site' => '', 'title' => '', 'category' => '', 'tags' => '', 'description' => '',
                    'username' => 'anonymous', 'url' => '', 'id' => '', 'size' => '', 'type' => 'public');
$grabbing = FALSE;

$vdo_path = '';
$filesize = 0;
$videos = array();

asort($sites);
					
if ( isset($_POST['grab_video']) ) {

    $url = trim($_POST['url']);
    if ( $url == '' ) {
        $errors[] = 'Please enter video url!';
    }

	if (!$errors ) {
		foreach ($sites as $k => $v) {
			if (strpos($url, $v['name']) !== false) {
				$site = (explode('.', $v['name']));				
				$site = $site[0];
				$cut = $config['cut_'.$site];
				break;
			}
		}
		if ($site == 'pornhub' || $site == 'tube8' || $site == 'youporn')	{
			require $config['BASE_DIR']. '/classes/aes.class.php';
			require $config['BASE_DIR']. '/classes/aesctr.class.php';
		}
	}

	if($site == '') {
		$errors[] = 'Video URL is not valid!';
    }
    
	if (!$errors ) {
        require $config['BASE_DIR']. '/classes/grabbers/' .$site. '.class.php';
        $class  = 'VGrab_' .$site;
        $graber = new $class;
	}
	
    if ( !$errors ) {
        $graber->getPage($url);
        $video['site']        = $site;
		$video['id']          = $graber->getVideoID();
        $video['title']       = $graber->getVideoTitle();
        $video['description'] = $graber->getVideoDescription();
        $video['category']    = $graber->getVideoCategory();
        $tags                 = $graber->getVideoTags();
        $video['cut'] 	 	  = $cut;		
		if ($tags == '') {
			$tags = prepare_tags(get_tags($video['title']));
		}

        $video['tags']        = $tags;

        foreach ( $categories as $category ) {
            if ( category_match($category['name'], $video['category']) ) {

                $video['category'] = $category['CHID'];
                break;
            }
        }
	
		$video['url']       = $graber->getVideoUrl();
		$curl              = new VCurl();


		if ( empty($video['url'] )) {
			$errors[] = 'Failed to get video URL! Are you sure the URL is correct?';
		} else {
			foreach ($video['url'] as $key => $val) {
				if ($val != '') {
					$fsize = '';
					$ftype = '';
					$fsize = $curl->getRemoteSize($val);
					$ftype = $curl->getVideoFiletype($val);
					if ($fsize != '' && $ftype != '') {
						$videos[$key]['url'] = $val;
						$videos[$key]['filesize'] = $fsize;
						$videos[$key]['filetype'] = $ftype;
					}
				}
			}
			if (count($videos)<1) {
				$errors[] = 'Failed to get video URL! Are you sure the URL is correct?';
			}
		}
    }
}

if ( isset($_POST['save_video']) ) {
    $v_site_id    = trim($_POST['video_id']);
    $title        = trim($_POST['title']);
    $category     = intval(trim($_POST['category']));
    $tags         = prepare_tags(trim($_POST['tags']));
    $description  = trim($_POST['description']);
	$url          = $_POST['url'];
	$filesize     = $_POST['filesize'];
	$filetype     = $_POST['filetype'];
	$selected_url = $_POST['selected_url'];
	$cut_intro    = $_POST['cut_intro'];
	$cut	      = $_POST['cut'];
	$video['cut_intro'] = $cut_intro;
	$video['cut'] = floatval($cut);
	if ($cut_intro != '1') {
		$cut = NULL;
	}
	
	foreach ($url as $key => $val) {
		$videos[$key]['url'] = $val;
		$videos[$key]['filesize'] = $filesize[$key];
		$videos[$key]['filetype'] = $filetype[$key];
	}
    $video['site']          = trim($_POST['site']);
	$video['selected_url']  = $selected_url;

	$filetype = $filetype[$selected_url];
	$filesize = $filesize[$selected_url];
	
    $vurl        = (isset($url[$selected_url])) ? trim($url[$selected_url]) : '';

    $username   = trim($_POST['username']);	
    $type       = ( isset($_POST['type']) && $_POST['type'] == 'private' ) ? 'private' : 'public';

    if ( $username == '' ) {
        $errors[] = 'Please enter a username!';
    } else {
        $sql = "SELECT UID FROM signup WHERE username = " .$conn->qStr($username). " LIMIT 1";
        $rs  = $conn->execute($sql);
        if ( $conn->Affected_Rows() == 1 ) {
            $uid                = intval($rs->fields['UID']);
            $video['username']  = $username;
        } else {
            $errors[] = 'Username: ' .htmlspecialchars($username, ENT_QUOTES, 'UTF-8'). 'does not exist!';
        }
    }
    
    if ( $title == '' ) {
        $errors[] = 'Please enter video title!';
    } else {
        $video['title']     = $title;
    }
    
    if ( $category == 0 ) {
        $errors[] = 'Please select video category!';
    } else {
        $video['category']  = $category;
    }
    
    if ( $tags == '' ) {
        $errors[] = 'Please enter video tags!';
    } else {
        $video['tags'] = $tags;
    }
    
	if ( $vurl == '' ) {
		$errors[] = 'Please enter/select a URL for the video source!';
	} else {
		$video['vurl'] = $vurl;
	}

    if ( !$errors ) {
		if ($cut != $config['cut_'.$video['site']] && $cut) {
			$config['cut_'.$video['site']] = $cut;
			update_config($config);
			update_smarty();
			$info[] = '<b>'.ucfirst($video['site']).'</b> cut intro default value has been updated to '.$cut.'s.';
		}
		
		if ($cut) {
			$cut_info = ' (Cut Intro: '.$cut.'s)';
		} else {
			$cut_info = '';
		}
		
        $sql        = "INSERT INTO video (UID, title, channel, vkey, keyword, description, type, addtime, adddate, cut)
                       VALUES (" .$uid. ", " .$conn->qStr($title). ", '" .$category. "',
                       '" .mt_rand(). "', " .$conn->qStr($tags). ", 
					   ".$conn->qStr($description). ",
                       '" .$type. "', '" .time(). "', '" .date('Y-m-d'). "', '" .$cut. "')";
        $conn->execute($sql);
        $vid            = $conn->insert_Id();
		$sql_add		= '';
        //========================================== Proccessing Grabbed Video ============================================================


		if ($filetype == 'video/mp4') {
			$vdo_path   = $config['VDO_DIR']. '/' .$vid.'.mp4';
			$vdoname = $vid.'.mp4';
		} else {
			$vdo_path   = $config['VDO_DIR']. '/' .$vid.'.flv';
			$vdoname = $vid.'.flv';
		}
		
		$cgi = ( strpos(php_sapi_name(), 'cgi') ) ? 'env -i ' : NULL;
		$cmd = $cgi.$config['phppath']
		." ".$config['BASE_DIR']."/scripts/grab_video.php"
		." ".$vid
		." ".urlencode($vurl)
		." ".$vdo_path
		." ".$vdoname		
		."";
		$lg = $config['LOG_DIR']. '/' .$vid. '.log_g';
		$cmd = $cmd.' > '.$lg;
		shell_exec("nohup $cmd 2> /dev/null & echo $!");
		$info[] = 'The video is now downloading and convertig'.$cut_info.'. You may leave/close this window.';
		$grabbing = TRUE;
		add_tags($tags);
    }
}

function duration_to_seconds($duration)
{
	$dur_arr  = explode(':', $duration);
	if (!isset($dur_arr['1'])) {
		return FALSE;
	}
	$duration = 0;
	if (isset($dur_arr['2'])) {
		$duration = ((int) $dur_arr['2']*3600);
	}
	$duration = $duration + ((int)$dur_arr['0']*60);
	return ($duration + (int)$dur_arr['1']);
}

function get_tags($tags_str) {
	$tags       = array();
	$tags_arr   = array();
	$tags_arr   = explode(' ', strtolower(prepare_string($tags_str, false)));
	foreach ($tags_arr as $tag) {
		if (strlen($tag) >= 5) {
			$tags[] = $tag;
		}
	}
	if (!empty($tags)) {
		return implode(',', $tags);
	} else {
		foreach ($tags_arr as $tag) {
			if (strlen($tag) >= 4) {
				$tags[] = $tag;
			}
		}
		if (!empty($tags)) {
			return implode(',', $tags);
		} else {
			foreach ($tags_arr as $tag) {
				if (strlen($tag) >= 3) {
					$tags[] = $tag;
				}
			}
			return implode(',', $tags);
		}
	}
}

function category_match ($cat1, $cat2) {
	if ($cat1 != '' && $cat2 != '') {
		$cat1 = strtolower($cat1);
		$cat2 = strtolower($cat2);
		if (strpos($cat1, $cat2) !== false || strpos($cat2, $cat1) !== false) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function bsize ($fsize) {
	$size = preg_replace("/[^0-9\,]/", '', $fsize);	
	if (strpos($fsize, "GB") !== FALSE) {
		return $size * 1000 * 1000 * 1000;
	} elseif (strpos($fsize, "MB") !== FALSE) {
		return $size * 1000 * 1000;
	} elseif (strpos($fsize, "kB") !== FALSE) {
		return $size * 1000; 
	} elseif (strpos($fsize, "B") !== FALSE) {
		return $size;
	} else {
		return $size;
	}
}

if (!$sites) {
	$warnings[]='Video Grabber is not installed!';
}

$smarty->assign('grabbing', $grabbing);
$smarty->assign('path', $vdo_path);
$smarty->assign('filesize', bsize($filesize));
$smarty->assign('video', $video);
$smarty->assign('videos', $videos);
$smarty->assign('sites', $sites);
$smarty->assign('categories', get_categories());
?>