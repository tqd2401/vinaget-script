<?php

class dl_verzend_be extends Download {
    
    public function CheckAcc($cookie){
        $data = $this->lib->curl("http://verzend.be/?op=my_account", "lang=english;{$cookie}", "");
        if(stristr($data, 'Premium account expire:</TD><TD><b>')) return array(true, "Until ".$this->lib->cut_str($data, 'Premium account expire:</TD><TD><b>','</b>'));
        else if(stristr($data, 'Payment info') && !stristr($data, 'Premium account expire:')) return array(false, "accfree");
		else return array(false, "accinvalid");
    }
    
    public function Login($user, $pass){
        $data = $this->lib->curl("http://verzend.be/", "lang=english", "login={$user}&password={$pass}&op=login&redirect=http://verzend.be/");
        $cookie = "lang=english;{$this->lib->GetCookies($data)}";
		return $cookie;
    }
	
    public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		$data = $this->lib->curl($url, $this->lib->cookie, "");
		if($pass) {
			$post = $this->parseForm($this->lib->cut_str($data, '<Form name="F1"', '</Form>'));
			$post["password"] = $pass;
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if(stristr($data,'Wrong password')) $this->error("reportpass", true, false);
			elseif($this->isredirect($data)) return trim($this->redirect);
		}
        if($this->isredirect($data)) return trim($this->redirect);
		elseif (stristr($data,'You have reached the download-limit'))  $this->error("LimitAcc", true, false);
		elseif (stristr($data,'<br><b>Password:</b> <input type="password"')) 	$this->error("reportpass", true, false);
		elseif (stristr($data, "Create Download Link")){
			$post = $this->parseForm($this->lib->cut_str($data, '<Form name="F1"', '</Form>'));
			$data = $this->lib->curl($url, $this->lib->cookie, $post);
			if($this->isredirect($data)) return trim($this->redirect);
		}
        elseif (stristr($data,'File Not Found')) $this->error("dead", true, false, 2);
		elseif (stristr($data,'No such file with this filename')) $this->error("dead", true, false, 2);
		return false;
    }
	
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Verzend.be Download Plugin by giaythuytinh176 [29.7.2013]
* Downloader Class By [FZ]
*/
?>