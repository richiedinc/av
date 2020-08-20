<?php

class VGrab_spankwire
{
    var $url;
    var $page;
    var $curl;
	
    function __construct() {
        $this->curl = new VCurl();
    }

    function VGrab_spankwire() {
        $this->__construct();
    }

    function getPage($url) {
        $this->url  = $url;
        if ( $this->page = $this->curl->saveToString($url) ) {
            $this->page = trim($this->page);
            $this->page = str_replace("\n", '', $this->page);
            $this->page = str_replace("\r", '', $this->page);
            $this->page = preg_replace('/\s\s+/', ' ', $this->page);
            return true;
        }
        return false;
    }

	function getVideoID() {
		$id_arr = explode('/',rtrim($this->url,'/'));
		$vid = end($id_arr);
		$vid = ltrim($vid,'video');
		$vid = current(explode('&',$vid));
		$vid = current(explode('?',$vid));
		return $vid;
	}
    
    function getVideoTitle() {
        preg_match('/<title>(.*?)<\/title>/', $this->page, $matches);
        if ( isset($matches['1']) ) {
			$title = str_replace(" - Spankwire.com", "", $matches['1']);
            return htmlspecialchars_decode(strip_tags(stripslashes($title)), ENT_QUOTES);
        }
    }
	
	function getVideoDescription() {
        preg_match('/"description":"(.*?)"/', $this->page, $matches);		
        if ( isset($matches['1']) ) {			
            return $matches['1'];
        }
	}
	
	function getVideoTags() {
        preg_match('/keywords" content="spankwire,spankwire.com,spankwire,spank video,porn,video,videos,(.*?),amateur/', $this->page, $matches);		
        if ( isset($matches['1']) ) {			
            $category_string = $matches['1'];
                return str_replace(',',', ',$category_string);
        }
	}

    function getVideoCategory() {
        preg_match('/keywords" content="spankwire,spankwire.com,spankwire,spank video,porn,video,videos,(.*?),amateur/', $this->page, $matches);		
        if ( isset($matches['1']) ) {			
            $category_string = $matches['1'];
                return $category_string;
        }
    }
 
    function getVideoUrl() {
		if (preg_match ('/data-desktop-url="(.*?)"/', $this->page, $matches_url)) {
			$videos['Default'] = $matches_url[1];
			return $videos;				
		} else {
			return false;
		}
    }
}

?>