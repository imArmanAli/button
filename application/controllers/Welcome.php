<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('HomeModel');
        $this->load->helper('url');
        $this->load->library("Obfuscator_library");
        
    }
    public function removespecialchar($str){
        $res = preg_replace('/[0-9\@\.\;\" "]%+/', '', $str);
        return $res;
    }
    
	public function get_only_digit($length_of_strings){ 
	    $str_result = '0123456789'; 
	    return substr(str_shuffle($str_result), 0, $length_of_strings); 
	}
	
	public function get_os_version(){
        $user_agent=$_SERVER['HTTP_USER_AGENT'];
        $os_platform=$_SERVER['HTTP_USER_AGENT'];
        $os_array=array(
                '/windows/'             =>  'windows',
                '/windows nt 10.0/i'    =>  'windows',
                '/windows nt 6.3/i'     =>  'windows',
                '/windows nt 6.2/i'     =>  'windows',
                '/windows nt 6.1/i'     =>  'windows',
                '/windows nt 6.0/i'     =>  'windows',
                '/windows nt 5.2/i'     =>  'windows',
                '/windows nt 5.1/i'     =>  'windows',
                '/windows xp/i'         =>  'windows',
                '/windows nt 5.0/i'     =>  'windows',
                '/windows me/i'         =>  'windows',
                '/win98/i'              =>  'windows',
                '/win95/i'              =>  'windows',
                '/win16/i'              =>  'windows',
                '/iphone/i'             =>  'iphone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'android',
                '/blackberry/i'         =>  'blackberry',
                '/macintosh|mac os x/i' =>  'mac',
                '/mac_powerpc/i'        =>  'mac',
                '/linux/i'              =>  'linux',
                '/ubuntu/i'             =>  'ubuntu',
                '/webos/i'              =>  'mobile',
                '/NOKIA/i'              =>  'mobile',
                '/Lumia/i'              =>  'mobile',
        );
        foreach ($os_array as $regex => $value) {
            if(preg_match($regex,$user_agent)) 
            return $os_platform=$value;
        }
        return $os_platform;
    }
    // get user client ip
    public function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipAddresses = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ipaddress = trim($ipAddresses[0]); // The first IP is the client's IP
		}
		
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		
		else
			$ipaddress = 'UNKNOWN';
		
		return $ipaddress;
    }

    public function string_sanitize($s) {
        $result = htmlentities($s);
        $result = preg_replace('/^(&quot;)(.*)(&quot;)$/', "$2", $result);
        $result = preg_replace('/^(&laquo;)(.*)(&raquo;)$/', "$2", $result);
        $result = preg_replace('/^(&#8220;)(.*)(&#8221;)$/', "$2", $result);
        $result = preg_replace('/^(&#39;)(.*)(&#39;)$/', "$2", $result);
        $result = html_entity_decode($result);
        $result = str_replace("'","",$result);
        $result = str_replace('"',"",$result);
        $result = str_replace('.',"",$result);
        $result = str_replace('/',"",$result);
        return $result;
    }

	public function getBrowser(){
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}
		elseif(preg_match('/Explorer/i',$u_agent))
		{
			$bname = 'Explorer';
			$ub = "Explorer";
		}
		return $ub;
	}

	function encode_with_key($data, $key) {
		$base64 = base64_encode($data);
		$encoded = strtr($base64, '+/=', $key);
		return $encoded;
	  }
	  
	  function decode_with_key($data, $key) {
		$decoded = strtr($data, $key, '+/=');
		$base64 = base64_decode($decoded);
		return $base64;
	  }

	function getCurrentUrl() {
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'];
		$uri = $_SERVER['REQUEST_URI'];
	
		return $protocol . '://' . $host . $uri;
	}

	function buildUrl($url, $queryParams){
		// Parse the URL into its components
		$parsedUrl = parse_url($url);

		// Get the base URL without the query string
		$baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

		// Build the new query string using http_build_query()
		$newQueryString = http_build_query($queryParams);

		// Reconstruct the full URL with the new query string
		return $baseUrl . '?' . $newQueryString;
	}

	public function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		// $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
		$purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), '', strtolower(trim($purpose))); //this is for php 8.1

		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}
		return $output;
	}

	public function index(){
		header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/javascript');
         
		//$uri = $_SERVER['REQUEST_URI'];
// 		$uri = $this->getCurrentUrl(); 
// 		if (strpos($uri, '&file=') !== false) { 
// 			$explode_file = explode('&file=',$uri);
// 			$file_var = '&file='.$explode_file[1];
// 			$explode_url = explode('?',$explode_file[0]);
// 		}else{
// 			$file_var = '';
// 			$explode_url = explode('?',$uri);
// 		}
// 		$base_encoded_data = $explode_url[1];
// 		$decode_data = base64_decode($base_encoded_data);


        $uri = $this->getCurrentUrl(); 
        
        // Parse the URL and return its components
        $parts = parse_url($uri);
        // Initialize $decode_data
        $decode_data = '';
        
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['file'])) {
                // Sanitize the 'file' parameter
                $file_keyword = filter_var($query['file'], FILTER_SANITIZE_STRING);
                $exploded_file_url = explode('&',$parts['query']);
                $file_var = $exploded_file_url[0];
                if (base64_encode(base64_decode($file_var, true)) === $file_var) {
                    $decode_data = base64_decode($file_var);
                }
            }else{
				header('Content-Type: text/javascript');
                $file_var = filter_var($parts['query'], FILTER_SANITIZE_STRING);
                if (base64_encode(base64_decode($file_var, true)) === $file_var) {
                    $decode_data = base64_decode($file_var);
                }   
            }
        }else{
            echo 'No direct access allowed.';exit;
        }
        if(empty($decode_data)){
            echo 'Something went wrong';exit;
        }		
        
		// Parse the query string into an associative array
		parse_str($decode_data, $queryParams);
		$opver  = $this->get_os_version();
		$browser = $this->getBrowser();
		// Get the value of 'adcode'
		$adcode = $queryParams['adcode'];
		$simplifiedUrl = str_replace('&amp;', '&', $decode_data);
		
		if($adcode == 1){
			$typeof = 'wordpress_index';
			$full_parse_url = base_url().'welcome/'.$typeof .'?'.$simplifiedUrl.'&opver='.$opver.'&browser='.$browser;
			$context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
            
            echo file_get_contents($full_parse_url, false, $context);
// 			echo file_get_contents("$full_parse_url");
		}
		if($adcode == 2){
			$full_parse_url = base_url().'welcome/smartlink?'.$simplifiedUrl.'&opver='.$opver.'&browser='.$browser.'&file='.$file_keyword;
			if($file_var == ''){
				echo 'something went wrong';
				exit;
			}
			$context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
            
            echo file_get_contents($full_parse_url, false, $context);
		}
		if($adcode == 3){
			$full_parse_url = base_url().'welcome/popup?'.$simplifiedUrl.'&opver='.$opver.'&browser='.$browser.'&file='.$file_keyword;
			if($file_var == ''){
				echo 'something went wrong';
				exit;
			}
			$context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
            
            echo file_get_contents($full_parse_url, false, $context);
		}
		if($adcode == 4){
			$full_parse_url = base_url().'welcome/popup_inline?'.$simplifiedUrl.'&opver='.$opver.'&browser='.$browser.'&file='.$file_keyword;
			$context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
            
            echo file_get_contents($full_parse_url, false, $context);
		}
	}

	public function wordpress_index(){
	    header('Access-Control-Allow-Origin: *');
		
        if(empty($_GET)){
            echo "Some error occured!!";
            exit;
        }
		
        $user = $this->security->xss_clean($_GET['user']);
        $user = $this->string_sanitize($user);
        $user = htmlspecialchars($user);
        $h    = $this->security->xss_clean($_GET['h']);
        $h    = $this->string_sanitize($h);
        $h    = htmlspecialchars($h);
		$tmp    = $this->security->xss_clean($_GET['tmp']);
        $tmp    = $this->string_sanitize($tmp);
        $tmp    = htmlspecialchars($tmp);
        $user_esc = $this->db->escape($user);
		
		$sid = $this->security->xss_clean($_GET['sid']);
        $sid = $this->string_sanitize($sid);
        $sid = htmlspecialchars($sid);
		$sid_esc = $this->db->escape($sid);
		
		$type = $this->security->xss_clean($_GET['type']);
        $type = $this->string_sanitize($type);
        $type = htmlspecialchars($type);
		
		
		if (isset($user) && $user > 0) {
			$new_hash 	= time()+60;
			
			$result = $this->HomeModel->get_active_domain();
			if ($result) {
				$redirecturl = $result->ad_url;
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
				$redirecturl = str_replace("{sid}", $sid, $redirecturl);
			}
			
			$adcode_no = '&adcode=1&tmp='.$tmp.'&h='.$h.'&type='.$type.'&sid='.$sid;
			
			 $todaydate = date('Y-m-d H:i:s');
            $userip = $this->get_client_ip();
           // $opver  = $this->get_os_version();
            $opver  = $_GET['opver'];
			$browser = $_GET['browser'];
// 			$xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$userip);
// 			$getcountry = $xml->geoplugin_countryName ;
			// $browser = $this->getBrowser();
			
            $get_country_data = $this->ip_info('', "location");
			if(!empty($get_country_data)){
				$getcountry = $get_country_data['country'];
				$getcountryshort = $get_country_data['country_code'];
				$state = $get_country_data['state'];
			}else{
				$getcountry = 'Unknown';
				$getcountryshort = 'Unknown';
				$state = 'Unknown';
			}
            
			$successResponse = $redirecturl.$adcode_no.'&type='.$type;
			$id = $result->ad_id; 
			$jsCode = '(function () {
			var sitetitle = document.querySelector(\'meta[property="og:title"]\').content;
			var siteurl = document.querySelector(\'meta[property="og:url"]\').content;
			var pubid = '.$user.';
			var fresh_st = sitetitle.replace(/ /g,"+");
			var st = fresh_st.replace(/[^a-zA-Z0-9 ]/g, "-");
			let p = "'.$successResponse.'";
			var parsstring = p.replace("{KEYWORD}", st);';
			if ($user > 0) {
                $opver = $this->db->escape($opver);
                $sql2 = "INSERT INTO tbl_statistics_btn(user_ip, stat_date, pub_id, domain_id,stat_os, stat_status, browser, country, site_id)VALUES('$userip','$todaydate', $user_esc, '0', $opver, '0', '$browser', '$getcountry', $sid_esc)";
                $result2 = $this->db->query($sql2);
				$admarket = "installusd5";
				$md5 = md5($id.'-'.$admarket.'-'.$user);
				$jsCode .=	'var id = '.$user.'; var successResponse = parsstring; var elements = document.getElementsByClassName("buttonPress-"+'.$user.'); var clickFunction = function() { window.open(successResponse, \'_blank\'); return; }; for (var i = 0; i < elements.length; i++) { elements[i].addEventListener(\'click\', clickFunction, false);}';
			}else{
				echo "Invalid user!";
                exit;
			}
			$jsCode .= '})();';
			$hunter = new HunterObfuscator($jsCode);
			$code = $hunter->Obfuscate();
			echo $code;
		}else{
			echo "Some error occured!!";
		}	
	}
	
	
    public function smartlink(){
        if(empty($_GET)){
            echo "Some error occured!!";
            exit;
        }
        $user = $this->security->xss_clean($_GET['user']);
        $user = $this->string_sanitize($user);
        $user = htmlspecialchars($user);
        $file = $this->security->xss_clean($_GET['file']);
        $file = $this->string_sanitize($file);
        $file = htmlspecialchars($file);
        $h    = $this->security->xss_clean($_GET['h']);
        $h    = $this->string_sanitize($h);
        $h    = htmlspecialchars($h);
		$tmp    = $this->security->xss_clean($_GET['tmp']);
        $tmp    = $this->string_sanitize($tmp);
        $tmp    = htmlspecialchars($tmp);

		$sid = $this->security->xss_clean($_GET['sid']);
        $sid = $this->string_sanitize($sid);
        $sid = htmlspecialchars($sid);

	   $type   = $this->security->xss_clean($_GET['type']);
       $type   = $this->string_sanitize($type);
       $type   = htmlspecialchars($type);


		if (isset($user) && $user > 0 && isset($file) && $file != '') {
			$new_hash 	= time()+60;
			$user 		= $_GET['user'];
			
			$keyword 	= $this->removespecialchar($file);
			$f_keyword = str_replace(" ", "+", $keyword);
			$f_keyword = str_replace("/", "_", $f_keyword);

			$result = $this->HomeModel->get_active_domain();
			if ($result) {
				$redirecturl = $result->ad_url; 
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
				$redirecturl = str_replace("{sid}", $sid, $redirecturl);
				$redirecturl = str_replace("{KEYWORD}", $f_keyword, $redirecturl);
				$redirecturl = str_replace("{hash}", $new_hash, $redirecturl);
				$redirecturl = str_replace("{13_hash}", $this->get_only_digit(13), $redirecturl);
				$redirecturl = str_replace("{17_hash}", $this->get_only_digit(17), $redirecturl);
				$redirecturl = str_replace("{32_hash}", $this->get_only_digit(32), $redirecturl);

			}
		//	print_r($result);exit;
			$adcode_no = '&adcode=2&tmp='.$tmp.'&h='.$h;
			$successResponse = $redirecturl.$adcode_no.'&type='.$type.'&sid='.$sid;
			$id = $result->ad_id; 

			?>
			<script type="text/javascript">
				window.open("<?php echo $successResponse;?>", "_self");
			</script>
			<?php
		}else{
			echo "Some error occured!!";
		}
	}

	public function popup(){
	   header('Access-Control-Allow-Origin: *');
	   header('Content-Type: text/javascript');
	   if(empty($_GET)){
		   echo "Some error occured!!";
		   exit;
	   }
	   $user = $this->security->xss_clean($_GET['user']);
	   $user = $this->string_sanitize($user);
	   $user = htmlspecialchars($user);
	   $file = $this->security->xss_clean($_GET['file']);
	   $file = $this->string_sanitize($file);
	   $file = htmlspecialchars($file);
	   $h    = $this->security->xss_clean($_GET['h']);
	   $h    = $this->string_sanitize($h);
	   $h    = htmlspecialchars($h);
	   $tmp    = $this->security->xss_clean($_GET['tmp']);
       $tmp    = $this->string_sanitize($tmp);
       $tmp    = htmlspecialchars($tmp);
	   $sid = $this->security->xss_clean($_GET['sid']);
       $sid = $this->string_sanitize($sid);
       $sid = htmlspecialchars($sid);

	   $type = $this->security->xss_clean($_GET['type']);
       $type = $this->string_sanitize($type);
       $type = htmlspecialchars($type);

	   if (isset($user) && $user > 0 && isset($file) && $file != '') {
		   $new_hash 	= time()+60;
		   
		   $keyword 	= $this->removespecialchar($file);
		   $f_keyword = str_replace(" ", "+", $keyword);
		   $f_keyword = str_replace("/", "_", $f_keyword);

		   $result = $this->HomeModel->get_active_domain('tbl_ads_popup');
		   if ($result) {
			   $redirecturl = $result->ad_url; 
			   $redirecturl = str_replace("{pubid}", $user, $redirecturl);
			   $redirecturl = str_replace("{KEYWORD}", $f_keyword, $redirecturl);
			   $redirecturl = str_replace("{hash}", $new_hash, $redirecturl);
			   $redirecturl = str_replace("{13_hash}", $this->get_only_digit(13), $redirecturl);
			   $redirecturl = str_replace("{17_hash}", $this->get_only_digit(17), $redirecturl);
			   $redirecturl = str_replace("{32_hash}", $this->get_only_digit(32), $redirecturl);
			   $successResponse = $redirecturl.'&tmp='.$tmp.'&sid='.$sid.'&type='.$type.'&adcode=3&h='.$h;
		   }else{
			 $result_tpl = $this->HomeModel->get_active_tpl_domain('tbl_redirect_domain_popup');
			 if ($result_tpl) {
				$redirecturl = $result_tpl->rd_url; 
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
				$redirecturl = str_replace("{sid}", $sid, $redirecturl);
				$redirecturl = str_replace("{KEYWORD}", $f_keyword, $redirecturl);
				$redirecturl = str_replace("{hash}", $this->get_only_digit(rand(10,60)), $redirecturl);
				$redirecturl = str_replace("{11_hash}", $this->get_only_digit(11), $redirecturl);
				$redirecturl = str_replace("{40_hash}", $this->get_only_digit(40), $redirecturl);
				$redirecturl = str_replace("{17_hash}", $this->get_only_digit(17), $redirecturl);
				$successResponse = $redirecturl.'&tmp='.$tmp.'&type='.$type.'&adcode=3&sid='.$sid;
			}else{
			  echo 'No Domain found';exit;
			}
		   }
		   $todaydate = date('Y-m-d H:i:s');
		   $userip = $this->get_client_ip();
		   $opver  = $_GET['opver'];
		   $browser = $_GET['browser'];
		//    $opver  = $this->get_os_version();
		   $user_esc = $this->db->escape($user);
		   $opver = $this->db->escape($opver);
		   $get_country_data = $this->ip_info('', "location");
			if(!empty($get_country_data)){
				$getcountry = $get_country_data['country'];
				$getcountryshort = $get_country_data['country_code'];
				$state = $get_country_data['state'];
			}else{
				$getcountry = 'Unknown';
				$getcountryshort = 'Unknown';
				$state = 'Unknown';
			}
		//    $browser = $this->getBrowser();
		  
		   $sql2 = "INSERT INTO tbl_statistics_btn(user_ip, stat_date, pub_id, domain_id,stat_os, stat_status, `site_id`, `browser`, `country`)VALUES('$userip','$todaydate', $user_esc, '0', $opver, '0','$sid','$browser','$getcountry')";
		   $result2 = $this->db->query($sql2);


		   $jsCode = '(function () {';
		   $jsCode .= 'const navbar = document.querySelector("body");
					   document.addEventListener("click", function() {
					   window.open("'. $successResponse.'", "_blank");
				   }, {once : true});';
		   $jsCode .= '})();';
		   $hunter = new HunterObfuscator($jsCode);
		   $code = $hunter->Obfuscate();
		   echo $code;
		   
	   }else{
		   echo "Some error occured!!";
	   }
   }
   
   public function popup_inline(){
	   header('Access-Control-Allow-Origin: *');
	   header('Content-Type: text/javascript');
	   if(empty($_GET)){
		   echo "Some error occured!!";
		   exit;
	   }
	   $user   = $this->security->xss_clean($_GET['user']);
	   $user   = $this->string_sanitize($user);
	   $user   = htmlspecialchars($user);
	   $adcode = $this->security->xss_clean($_GET['adcode']);
	   $adcode = $this->string_sanitize($adcode);
	   $adcode = htmlspecialchars($adcode);
	   $h      = $this->security->xss_clean($_GET['h']);
	   $h      = $this->string_sanitize($h);
	   $h      = htmlspecialchars($h);
	   $tmp    = $this->security->xss_clean($_GET['tmp']);
	   $tmp    = $this->string_sanitize($tmp);
	   $tmp    = htmlspecialchars($tmp);
	   $sid    = $this->security->xss_clean($_GET['sid']);
       $sid    = $this->string_sanitize($sid);
       $sid    = htmlspecialchars($sid);
	   $type   = $this->security->xss_clean($_GET['type']);
       $type   = $this->string_sanitize($type);
       $type   = htmlspecialchars($type);

	   $adcode_detail = $this->HomeModel->get_ads_detail($user,$sid,'4');
	   if(empty($adcode_detail)){
		echo 'No Banner image found';
		exit;
	   }
	   $banner_image = $adcode_detail[0]['banner_image'];
		if(!empty($adcode_detail)){
			$banner_type 		= $adcode_detail[0]['banner_type'];
			$interval_time 		= $adcode_detail[0]['interval_time'];
			$isUqiue 			= $adcode_detail[0]['isUnique'];
			$banner_text 		= $adcode_detail[0]['banner_text'];
			$banner_width 		= $adcode_detail[0]['banner_width'].'px';
			$banner_height 		= $adcode_detail[0]['banner_height'].'px';
		}else{
			$banner_type 	= '1';
			$interval_time 	= '';
			$isUqiue 		= '1';
			$banner_width 	= '100px';
			$banner_height 	= '100px';
			$banner_text 	= '1';
		}
	   $jsCode = 'var newScript = document.createElement(\'script\');
		 newScript.src = "https://code.jquery.com/jquery-3.6.0.min.js";
		 newScript.defer = true;
		 document.head.appendChild(newScript);';
			if($banner_type == '1'){
			    $jsCode .= '
		document.getElementsByTagName(\'body\')[0].insertAdjacentHTML(\'afterbegin\',\'<iframe id="iframe" style="width:'.$banner_width.';height:'.$banner_height.'; margin: 0px; padding: 0px; border: none; outline: none; box-sizing: border-box;';
	
			$jsCode .= 'position: fixed; inset: auto 10px 10px auto !important; overflow: hidden; z-index: 2147483640; display: block !important; "></iframe>\');';
		}else{
		    $jsCode .= '
			var popup_inline = document.querySelector("#popup_inline");
			var iframe = document.createElement("iframe");
			iframe.id = "iframe";
			iframe.style.cssText = "width:'.$banner_width.';height:'.$banner_height.'; margin: 0px; padding: 0px; border: none; outline: none; box-sizing: border-box;overflow: hidden; z-index: 2147483640; display: block !important; ";
			popup_inline.parentNode.insertBefore(iframe, popup_inline.nextSibling);';
		}
		
		$jsCode .= '
		 setTimeout(function() {
		   var iframe = document.getElementById(\'iframe\');
		   var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
		   if (  iframeDoc.readyState  == \'complete\' ) {
			   var sitetitle = parent.document.querySelector(\'meta[property="og:title"]\').content;
			   var body = document.querySelector("#iframe").contentDocument.querySelector("body");
			   body.insertAdjacentHTML(\'beforeend\',`';
			   
		   if ($user > 0) {
		   $new_hash 	= time()+60;
		   
		   $result = $this->HomeModel->get_active_domain('tbl_ads_popup_inline');
		   if ($result) {
			   $redirecturl = $result->ad_url;
			   $redirecturl = str_replace("{pubid}", $user, $redirecturl);
			   if ($adcode != ''){
					$adcode_no = '&adcode='.$adcode.'&tmp='.$tmp.'&h='.$h.'&sid='.$sid.'&type='.$type;
				}else{
					$adcode_no = '';
				}
		   }else{
			$result_tpl = $this->HomeModel->get_active_tpl_domain('tbl_redirect_domain_popup_inline');
			if ($result_tpl) {
			   $redirecturl = $result_tpl->rd_url; 
			   $redirecturl = str_replace("{pubid}", $user, $redirecturl);
			   $redirecturl = str_replace("{sid}", $sid, $redirecturl);
			//    $redirecturl = str_replace("{KEYWORD}", $f_keyword, $redirecturl);
			   $redirecturl = str_replace("{hash}", $this->get_only_digit(rand(10,60)), $redirecturl);
			   $redirecturl = str_replace("{11_hash}", $this->get_only_digit(11), $redirecturl);
			   $redirecturl = str_replace("{40_hash}", $this->get_only_digit(40), $redirecturl);
			   $redirecturl = str_replace("{17_hash}", $this->get_only_digit(17), $redirecturl);
			   $successResponse = $redirecturl;
			   $adcode_no = '&adcode='.$adcode.'&tmp='.$tmp.'&h='.$user.'&type='.$type.'&sid='.$sid;
		   }else{
			 echo 'No Domain found';exit;
		   }
		  }
		   
		   
		   
		   $todaydate = date('Y-m-d H:i:s');
		   $todaydateOnly = date('Y-m-d');
		   $userip = $this->get_client_ip();
		   $opver  = $_GET['opver'];
		   $browser = $_GET['browser'];
		//    $opver  = $this->get_os_version();
		   $user_esc = $this->db->escape($user);
		   $opver = $this->db->escape($opver);
		   $get_country_data = $this->ip_info('', "location");
			if(!empty($get_country_data)){
				$getcountry = $get_country_data['country'];
				$getcountryshort = $get_country_data['country_code'];
				$state = $get_country_data['state'];
			}else{
				$getcountry = 'Unknown';
				$getcountryshort = 'Unknown';
				$state = 'Unknown';
			}
		//    $browser = $this->getBrowser();

		   if($isUqiue == '1'){
			$check_exist = "Select * FROM tbl_statistics_btn WHERE user_ip = '$userip' AND DATE(stat_date) = '$todaydateOnly' AND pub_id = $user_esc AND site_id = '$sid'";
			$result_exist = $this->db->query($check_exist);
			if($result_exist){
					if($result_exist->num_rows() > 0){
						exit;
					}
			}
			}

		   $sql2 = "INSERT INTO tbl_statistics_btn(user_ip, stat_date, pub_id, domain_id,stat_os, stat_status, `site_id`, `browser`, `country`)VALUES('$userip','$todaydate', $user_esc, '0', $opver, '0','$sid','$browser','$getcountry')";
		   $result2 = $this->db->query($sql2);


		   
		   
		   $successResponse = $redirecturl.$adcode_no;
		//    $id = $result->ad_id; 
		   if($banner_type == '1'){

		   $jsCode .='<html dir="ltr">
<head>
   <style>
   ._1BqZY {
	   display: flex;
	   flex-grow: 1;
	   align-items: center;
	   justify-content: center;
	   padding: 4px 12px;
	   border: none;
	   border-left: 1px solid #e3e3e3;
	   background: transparent;
	   color: #434343;
	   font-size: 24px;
	   font-family: monospace;
	   line-height: 0;
	   text-transform: uppercase;
	   opacity: 0;
	   cursor: pointer;
	   transition: opacity 0.25s ease-out, color 0.25s ease-out, background-color 0.25s ease-out;
   }
   
   ._2hazA,
   .c8aoP {
	   width: auto;
   }
   
   ._1BqZY:first-of-type {
	   border-bottom: 1px solid #e3e3e3;
   }
   
   .Vk7z3 ._1BqZY {
	   border-left: none;
   }
   
   ._1BqZY:hover {
	   background-color: #f1f1f1;
   }
   
   html[dir=\'rtl\'] ._1BqZY {
	   border-right: 1px solid #e3e3e3;
	   border-left: none;
   }
   
   ._11OQ6[contrast=\'dark\'] ._1BqZY {
	   color: #fff;
   }
   
   ._11OQ6:hover ._1BqZY {
	   opacity: 1;
   }
   
   .Vk7z3 ._1BqZY:first-of-type {
	   border-bottom: none;
   }
   
   html[dir=\'rtl\'] .Vk7z3 ._1BqZY {
	   border-right: none;
   }
   
   ._1BqZY._2g7AB {
	   opacity: 1;
   }
   
   .pDqek {
	   font-size: 12px;
   }
   
   ._11OQ6[contrast=\'dark\'] ._1BqZY:hover {
	   background-color: #1f1f1f;
   }
   
   ._3I8o4 {
	   display: flex;
	   flex-grow: 1;
	   flex-shrink: 0;
	   flex-flow: nowrap column;
	   min-width: 58px;
	   max-width: 58px;
	   margin: -6px -6px -6px 0;
   }
   
   ._3I8o4.Vk7z3 {
	   min-width: 42px;
	   max-width: 42px;
   }
   
   html[dir=\'rtl\'] ._3I8o4 {
	   margin: -6px 0 -6px -6px;
   }
   
   ._2lQqA[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._2lQqA[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   ._2bOcO {
	   font-size: 15px;
   }
   
   ._1nxSd {
	   font-size: 13px;
   }
   
   @media screen and (min-width: 361px) {
	   .ZzOEz {
		   flex: 0 0 20%;
		   width: 20%;
		   padding-top: 20%;
	   }
   }
   
   .BFqZQ {
	   position: relative;
	   display: flex;
	   overflow: hidden;
	   width: 100%;
	   margin: 8px 0;
	   padding: 6px;
	   border-radius: 12px;
	   background-color: #fafafa;
	   box-shadow: 0.6px 0.6px 1.9px rgba(0, 0, 0, 0.024), 2.2px 2.2px 4.9px rgba(0, 0, 0, 0.035), 6.6px 6.6px 12px rgba(0, 0, 0, 0.046), 12px 12px 24px rgba(0, 0, 0, 0.07);
	   visibility: hidden;
	   user-select: none;
	   transform: translateX(100%);
   }
   
   .BFqZQ[contrast=\'dark\'] {
	   background-color: #282C34;
	   box-shadow: 0.6px 0.6px 1.9px rgba(0, 0, 0, 0.024), 2.2px 2.2px 4.9px rgba(0, 0, 0, 0.035), 6.6px 6.6px 12px rgba(0, 0, 0, 0.046), 24px 24px 48px rgba(0, 0, 0, 0.07);
   }
   
   ._20Tkq {
	   position: relative;
	   flex: 0 0 18%;
	   width: 18%;
	   height: 0;
	   padding-top: 18%;
   }
   
   ._1cE6- {
	   position: absolute;
	   top: 0;
	   right: 0;
	   bottom: 0;
	   left: 0;
	   object-fit: cover;
	   width: 100%;
	   height: 100%;
	   border-radius: 2px;
   }
   
   ._3jMUQ {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   justify-content: center;
	   overflow: hidden;
	   margin: 0 4px 0 6px;
   }
   
   html[dir=\'rtl\'] ._3jMUQ {
	   margin: 0 6px 0 4px;
   }
   
   ._3jMUQ > div {
	   color: #222;
   }
   
   .BFqZQ[contrast=\'dark\'] ._3jMUQ > div {
	   color: #fff;
   }
   
   ._3dioT {
	   font-size: 14px;
	   line-height: 1.5;
   }
   
   ._1b3BD {
	   font-size: 12px;
	   line-height: 1.2;
   }
   
   .BFqZQ._266Sd {
	   visibility: visible;
	   transition: transform 0.25s ease-out;
	   transform: translateX(0);
   }
   
   .RMi3h {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(110%);
   }
   
   ._1ajCt {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(-110%);
   }
   
   .BFqZQ:hover ._2DPyL {
	   opacity: 1;
   }
   
   ._1xsUy::before {
	   content: \'\';
	   position: absolute;
	   top: 0;
	   left: 0;
	   width: 100%;
	   height: 100%;
	   background: #e5e5e5;
   }
   
   ._3MQ8_ {
	   display: flex;
	   flex-direction: column;
	   width: 100%;
   }
   
   .aQ4uP {
	   display: flex;
	   padding: 10px;
   }
   
   ._3vRPn {
	   display: flex;
	   justify-content: space-between;
	   margin-top: 5px;
   }
   
   @media screen and (min-width: 361px) {
	   ._20Tkq {
		   flex: 0 0 15%;
		   width: 15%;
		   padding-top: 15%;
	   }
   }
   
   ._3XvdU {
	   width: 49%;
	   padding: 7px;
	   border-radius: 5px;
	   text-align: center;
	   text-transform: capitalize;
	   cursor: pointer;
   }
   
   .fnO3Y {
	   background-color: white;
	   color: #2172e7;
   }
   
   ._2Rf9t[contrast=\'light\'] .fnO3Y,
   ._2Rf9t[contrast=\'dark\'] .fnO3Y {
	   background-color: #e1e1e1;
   }
   
   ._2Rf9t[contrast=\'light\'] .fnO3Y.YWIBa,
   ._2Rf9t[contrast=\'dark\'] .fnO3Y.YWIBa,
   ._2Rf9t[contrast=\'colored\'] .fnO3Y.YWIBa,
   ._2Rf9t[contrast=\'default\'] .fnO3Y.YWIBa {
	   background-color: #2172e7;
	   color: white;
   }
   
   ._1oP97 {
	   background-color: #2172e7;
	   color: white;
   }
   
   .YWIBa {
	   justify-content: flex-end;
   }
   
   ._1JPVX > div:nth-child(1) {
	   margin: 0 0 -8px -8px;
	   border-left: 0;
   }
   
   ._1JPVX > div:nth-child(2) {
	   margin: 0 -8px -8px 0;
	   border-left: 1px solid #9f9f9f;
   }
   
   ._2pb9N {
	   box-sizing: border-box;
	   width: calc(50% + 8px);
	   padding: 10px;
	   border-top: 1px solid #9f9f9f;
	   font-size: 16px;
	   text-align: center;
	   text-transform: capitalize;
	   cursor: pointer;
   }
   
   ._2zBbB[contrast=\'colored\'] ._3cK_h._2FixE {
	   color: #2172e7;
   }
   
   ._3cK_h {
	   margin: 0 0 -8px -8px;
	   color: #9f9f9f;
   }
   
   html[dir=\'rtl\'] ._3cK_h {
	   margin: 0 -8px -8px 0;
	   border-left: 1px solid #9f9f9f;
   }
   
   .qvuZl {
	   margin: 0 -8px -8px 0;
	   border-left: 1px solid #9f9f9f;
	   color: #2172e7;
   }
   
   html[dir=\'rtl\'] .qvuZl {
	   margin: 0 0 -8px -8px;
	   border-left: none;
   }
   
   ._2FixE {
	   width: calc(100% + 16px);
	   margin: 0 -8px -8px -8px;
   }
   
   ._3RwRP {
	   width: calc(100% + 16px);
	   margin: 0 -8px -8px -8px;
	   border-top: 1px solid #9f9f9f;
	   border-left: none;
   }
   /* for 2_5_1 */
   
   ._2zBbB[contrast=\'colored\'] ._3RwRP {
	   border-top: 1px solid white;
	   color: white;
   }
   
   html[dir=\'rtl\'] ._2FixE {
	   margin: 0 -8px -8px -8px;
   }
   
   .jRPym {
	   color: #ff9a00;
	   font-size: 22px;
   }
   
   ._2GEGV {
	   margin: 3px 0;
	   border-radius: 24px;
   }
   
   ._2GEGV[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._2GEGV[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   .D6Cy3 {
	   border-radius: 50%;
   }
   
   ._20NXT {
	   object-fit: cover;
	   width: 100%;
	   height: 180px;
   }
   
   ._1-lCg {
	   margin: 0 -6px -6px -6px;
   }
   
   .OzYBy > div {
	   border-radius: 10px;
	   font-weight: bold;
   }
   
   ._2o_Mt {
	   font: inherit;
   }
   
   ._2GEGV[contrast] .OzYBy > div:nth-child(2) {
	   border: 2px solid #666666;
	   background-color: transparent;
	   color: #666666;
   }
   
   ._3PsAv {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   height: 40px;
	   padding: 5px;
	   background-color: inherit;
	   color: #2172e7;
	   font-weight: bold;
	   font-size: 15px;
   }
   
   .czlgw {
	   text-align: start;
   }
   
   ._1vhXZ {
	   text-align: end;
   }
   
   ._1vhXZ,
   .czlgw {
	   width: 50%;
   }
   
   ._36anl {
	   font-weight: bold;
	   font-size: 15px;
   }
   
   ._1CAJt {
	   font-size: 13px;
   }
   
   ._2GEGV:not([contrast=\'dark\']) ._1CAJt {
	   color: #686868;
   }
   
   ._3oPGc {
	   display: flex;
	   padding: 5px;
   }
   
   ._3jVIv {
	   padding: 5px 2px 1px 6px !important;
   }
   
   ._22NoY[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._22NoY[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   ._22NoY[contrast=\'dark\'] {
	   background-color: #282C34;
   }
   
   .-MbDu {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   justify-content: center;
	   overflow: hidden;
	   margin: 0 4px 0 6px;
   }
   
   ._2J8r2 {
	   font-weight: bold;
	   font-size: 16px;
   }
   
   ._2bc-S {
	   padding: 5px;
	   border-radius: 8px;
	   background-color: #e1e1e1;
	   box-shadow: 0 3px 6px #00000029;
   }
   
   ._22NoY[contrast=\'light\'] ._2bc-S {
	   background-color: white;
   }
   
   ._2bc-S::before {
	   content: \' \';
	   position: absolute;
	   top: 47%;
	   left: 16px;
	   border-width: 0 0 16px 16px;
	   border-style: solid;
	   border-color: transparent transparent #e1e1e1 transparent;
   }
   
   ._22NoY[contrast=\'light\'] ._2bc-S::before {
	   border-color: transparent transparent white transparent;
   }
   
   ._2_95t {
	   margin: 5px 0;
	   padding: 10px 10px 25px 25px;
	   background-color: white;
   }
   
   ._22NoY[contrast=\'light\'] ._2_95t {
	   background-color: #e1e1e1;
   }
   
   .nCIj8 {
	   font-size: 13px;
   }
   
   ._22NoY[contrast=\'dark\'] .-MbDu ._2J8r2 {
	   color: #fff;
   }
   
   ._2LwZC {
	   border-radius: 50%;
   }
   
   ._1RglM::after {
	   right: 0;
	   bottom: 0;
   }
   
   ._3S9TS {
	   display: flex;
   }
   
   body > div:last-child {
	   border-bottom: none;
   }
   
   ._1ca6D {
	   margin: 0;
	   border-bottom: solid 2px #9f9f9f;
	   border-radius: 0;
	   box-shadow: none;
	   opacity: 0;
	   user-select: none;
	   transition: all 0.25s ease-out;
	   transform: scale(0, 0);
   }
   
   ._1ca6D._3eMNc {
	   opacity: 1;
	   visibility: visible;
	   transform: scale(1, 1);
   }
   
   ._1ca6D[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._1ca6D[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   ._2Vhqc::before {
	   top: 0;
   }
   
   ._2Vhqc:not(._3YiUb)::before {
	   background-color: #2172e7;
   }
   
   ._2Vhqc::after {
	   right: 0;
	   bottom: 0;
   }
   
   ._1BNKA {
	   border-radius: 50%;
   }
   
   .leuOP {
	   font-size: 15px;
   }
   
   .sAqGN {
	   font-size: 13px;
   }
   
   .KEhS2 {
	   line-height: 1.5;
   }
   
   ._3lNd3 {
	   margin-top: 2px !important;
	   padding: 0 3px 1px 2px !important;
	   border-top-left-radius: 0 !important;
	   border-top-right-radius: 4px !important;
   }
   
   ._1upld {
	   width: 100%;
	   padding: 10px;
	   background-color: #2172e7;
	   color: white;
	   font-weight: bold;
	   visibility: hidden;
	   user-select: none;
	   transform: translateY(-100%);
   }
   
   ._1upld._3YiUb {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   height: 50px;
	   padding: 0;
	   border-radius: 8px 8px 0 0;
   }
   
   ._1upld._3eMNc {
	   visibility: visible;
	   transition: transform 0.1s ease-out;
	   transform: translateY(0);
   }
   
   ._1SJ8P {
	   display: flex;
	   align-content: stretch;
	   width: 100%;
	   height: 56px;
	   margin-top: 8px;
	   visibility: hidden;
	   user-select: none;
	   transform: translateX(100%);
   }
   
   ._1SJ8P._3Q0M6 {
	   height: 72px;
   }
   
   ._1SJ8P._1deq7 {
	   visibility: visible;
	   transition: transform 0.25s ease-out;
	   transform: translateX(0);
   }
   
   .XBM2t {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(110%);
   }
   
   ._1rWuW {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(-110%);
   }
   
   ._1UyhH {
	   position: relative;
	   display: flex;
	   flex-shrink: 0;
	   align-items: flex-end;
	   justify-content: center;
	   width: 56px;
   }
   
   ._3Q0M6 ._1UyhH {
	   width: 72px;
   }
   
   ._1UyhH::before {
	   top: 6px;
	   left: 2px;
   }
   
   ._1UyhH::after {
	   right: 2px;
	   bottom: 0;
   }
   
   ._3Q0M6 ._1UyhH::before {
	   top: 16px;
	   left: 4px;
   }
   
   ._3Q0M6 ._1UyhH::after {
	   right: 4px;
	   bottom: 0;
   }
   
   ._12jfy {
	   object-fit: cover;
	   border-radius: 50%;
	   box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
   }
   
   ._12jfy,
   ._3YzY5,
   ._2BWsY {
	   width: 48px;
	   height: 48px;
   }
   
   ._2uLiv {
	   position: initial;
   }
   
   ._2BWsY {
	   border-radius: 50%;
   }
   
   ._3Q0M6 ._12jfy,
   ._3Q0M6 ._3YzY5,
   ._3Q0M6 ._2BWsY {
	   width: 56px;
	   height: 56px;
   }
   
   ._2TAf2 {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   align-items: flex-start;
	   padding: 10px;
	   border-radius: 13px 20px 0 20px;
	   background-color: #e1e1e1;
	   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
   }
   
   ._2TAf2.duS1W {
	   border-radius: 13px 20px 20px 0;
   }
   
   ._1SJ8P ._3_Hh_ {
	   left: 56px;
   }
   
   ._1SJ8P._3Q0M6 ._3_Hh_ {
	   left: 72px;
   }
   
   ._1SJ8P[contrast=\'light\'] ._2TAf2 {
	   background-color: #fafafa;
   }
   
   ._1SJ8P[contrast=\'dark\'] ._2TAf2 {
	   background-color: #282C34;
	   color: white;
   }
   
   ._1SJ8P[contrast=\'colored\'] ._2TAf2 {
	   background-color: #2172e7;
	   color: white;
   }
   
   ._1FlJQ {
	   font-weight: 400;
	   font-size: 14px;
	   line-height: 1.5;
   }
   
   ._2x4yJ {
	   font-weight: 300;
	   font-size: 12px;
	   line-height: 1.2;
   }
   
   ._1dtn9 {
	   display: flex;
	   flex-shrink: 0;
	   justify-content: flex-end;
	   width: 30px;
   }
   
   .oUVIt {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 25px;
	   height: 25px;
	   border-radius: 50%;
	   background-color: #f44336;
	   color: white;
	   font-weight: 300;
	   font-size: 14px;
	   cursor: pointer;
   }
   /** 2-5-1 */
   
   ._2TAf2._6pyrP {
	   border-top-right-radius: 0;
	   border-bottom-right-radius: 0;
   }
   
   ._1dtn9._6pyrP {
	   width: 50px;
	   border-radius: 0 15px 15px 0;
	   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
   }
   
   ._1dtn9._6pyrP .oUVIt {
	   width: 100%;
	   height: 100%;
	   border-radius: 0 15px 15px 0;
	   font-size: 48px;
   }
   
   ._3srSV {
	   display: flex;
	   width: 100%;
	   margin-top: 8px;
	   visibility: hidden;
	   user-select: none;
	   transform: translateX(100%);
   }
   
   ._3srSV._284Pw {
	   visibility: visible;
	   transition: transform 0.25s ease-out;
	   transform: translateX(0);
   }
   
   ._1a2qa {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(110%);
   }
   
   .ORNtv {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(-110%);
   }
   
   ._2N6y4 {
	   display: flex;
	   flex-grow: 1;
	   border-radius: 0;
	   background-color: #e1e1e1;
	   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
   }
   
   ._3srSV[contrast=\'dark\'] ._2N6y4 {
	   background-color: #282C34;
	   color: white;
   }
   
   ._3srSV[contrast=\'light\'] ._2N6y4 {
	   background-color: #fafafa;
   }
   
   ._2N6y4._2B-KQ,
   ._2N6y4._2N6Ml {
	   flex-wrap: wrap;
   }
   
   ._2N6y4._2N6Ml {
	   border-radius: 5px;
   }
   
   ._3QdQj {
	   display: flex;
	   flex-grow: 1;
	   flex-shrink: 0;
	   align-items: center;
	   width: 100%;
	   height: 28px;
	   padding: 0 10px;
   }
   
   ._3j59g {
	   height: 20px;
	   margin: 8px 8px 0 0;
   }
   
   ._3j59g._2N6Ml {
	   height: 16px;
   }
   
   ._1ayqZ,
   ._dOEv,
   ._2Pf3p {
	   margin-top: 8px;
	   color: #9e9e9e;
	   font-weight: 300;
	   font-size: 12px;
   }
   
   ._1ayqZ {
	   text-transform: capitalize;
   }
   
   ._1ayqZ._2B-KQ {
	   color: #2a56c6;
   }
   
   ._2Pf3p {
	   margin: 8px 4px 0 4px;
	   font-size: 7px;
   }
   
   ._1E320 {
	   height: 12px;
	   margin: 7px 0 0 5px;
   }
   
   ._3srSV[contrast=\'colored\'] ._2N6y4 {
	   background-color: #2172e7;
	   color: white;
   }
   
   ._2IC42 {
	   position: relative;
	   display: flex;
	   flex-shrink: 0;
	   align-items: center;
	   justify-content: center;
	   width: 72px;
	   height: 72px;
   }
   
   ._2IC42._2B-KQ {
	   order: 2;
   }
   
   ._2IC42._2N6Ml::after {
	   right: 8px;
	   bottom: 8px;
   }
   
   ._2b_ix {
	   object-fit: cover;
   }
   
   ._2b_ix,
   .lpD7c,
   ._3DwbK {
	   width: 100%;
	   height: 100%;
   }
   
   ._2b_ix.KIkJS,
   .lpD7c.KIkJS,
   ._3DwbK.KIkJS,
   ._2b_ix._2B-KQ,
   .lpD7c._2B-KQ,
   ._3DwbK._2B-KQ,
   ._2b_ix._2N6Ml,
   .lpD7c._2N6Ml,
   ._3DwbK._2N6Ml {
	   width: 56px;
	   height: 56px;
	   border-radius: 50%;
   }
   
   ._1mddF {
	   position: initial;
   }
   
   ._3SPBW {
	   content: \'\';
	   position: absolute;
	   right: 4px;
	   bottom: 4px;
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 19px;
	   height: 19px;
	   border-radius: 50%;
	   background-color: #2a56c6;
   }
   
   ._2AZzg {
	   width: 12px;
	   height: 10px;
   }
   
   ._3DnS5 {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   align-items: flex-start;
	   max-width: calc(100% - 72px);
	   padding: 10px;
   }
   
   ._3SJZn._2B-KQ {
	   order: 1;
   }
   
   ._2Elxu {
	   font-weight: 400;
	   font-size: 14px;
	   line-height: 1.5;
   }
   
   ._1HFJB {
	   font-weight: 300;
	   font-size: 12px;
	   line-height: 1.2;
   }
   
   ._29H6w {
	   display: flex;
	   flex-shrink: 0;
	   justify-content: flex-end;
	   width: 30px;
   }
   
   ._1V4Vc {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 25px;
	   height: 25px;
	   border-radius: 50%;
	   background-color: #f44336;
	   color: white;
	   font-weight: 300;
	   font-size: 14px;
	   cursor: pointer;
   }
   
   ._375yP._2B-KQ,
   ._375yP._2N6Ml {
	   right: 30px;
	   left: auto;
   }
   
   ._375yP._2N6Ml {
	   border-top-right-radius: 5px;
   }
   
   ._3jGB7 {
	   width: 32px;
	   height: 32px;
   }
   
   ._2zMcM {
	   margin: 3px 0;
	   padding: 6px 20px;
	   border-radius: 24px;
   }
   
   ._23iYh {
	   display: flex;
	   align-items: center;
   }
   
   ._3ECmZ {
	   margin: 0 15px;
	   color: red;
	   font-weight: bold;
   }
   
   ._1C4rG {
	   color: #686868;
   }
   
   ._2zMcM[contrast=\'dark\'] ._1C4rG {
	   color: white;
   }
   
   ._2zMcM[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._2zMcM[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   .C3CkY {
	   border-radius: 50%;
   }
   
   ._39CA2 {
	   font-weight: bold;
	   font-size: 15px;
   }
   
   .CmAcP {
	   font-size: 13px;
   }
   
   ._3FP1I {
	   display: flex;
	   padding: 5px;
   }
   
   ._26j18 {
	   padding: 5px 2px 1px 6px !important;
   }
   
   ._1SDch {
	   margin: 0 0 -6px 15%;
   }
   
   .eDPfN > div {
	   border-radius: 10px;
	   font-weight: bold;
   }
   
   ._2zMcM[contrast] .eDPfN > div:nth-child(2) {
	   border: 2px solid red;
	   background-color: transparent;
	   color: red;
   }
   
   ._3jGB7 {
	   width: 32px;
	   height: 32px;
   }
   
   ._2zMcM {
	   margin: 3px 0;
	   padding: 6px 20px;
	   border-radius: 24px;
   }
   
   ._23iYh {
	   display: flex;
	   align-items: center;
   }
   
   ._3ECmZ {
	   margin: 0 15px;
	   color: red;
	   font-weight: bold;
   }
   
   ._1C4rG {
	   color: #686868;
   }
   
   ._2zMcM[contrast=\'dark\'] ._1C4rG {
	   color: white;
   }
   
   ._2zMcM[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._2zMcM[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   .C3CkY {
	   border-radius: 50%;
   }
   
   ._39CA2 {
	   font-weight: bold;
	   font-size: 15px;
   }
   
   .CmAcP {
	   font-size: 13px;
   }
   
   ._3FP1I {
	   display: flex;
	   padding: 5px;
   }
   
   ._26j18 {
	   padding: 5px 2px 1px 6px !important;
   }
   
   ._1SDch {
	   margin: 0 0 -6px 15%;
   }
   
   .eDPfN > div {
	   border-radius: 10px;
	   font-weight: bold;
   }
   
   ._2zMcM[contrast] .eDPfN > div:nth-child(2) {
	   border: 2px solid red;
	   background-color: transparent;
	   color: red;
   }
   
   ._1_hgp {
	   padding: 8px;
	   visibility: visible;
	   transform: none;
   }
   
   ._1_hgp[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._1CTGu {
	   width: 100%;
	   opacity: 0;
	   visibility: hidden;
	   user-select: none;
	   transform: translateX(100%);
   }
   
   ._1CTGu._2vsUb {
	   opacity: 1;
	   visibility: visible;
	   transition: all 0.25s ease-out;
	   transform: translateX(0);
   }
   
   ._2U3DJ,
   ._3KbJ6 {
	   opacity: 0;
	   visibility: visible;
	   transition: all 0.25s ease-in;
   }
   
   ._2U3DJ {
	   transform: translateX(110%);
   }
   
   ._3KbJ6 {
	   transform: translateX(-110%);
   }
   
   ._2GWok {
	   position: absolute;
	   left: -7px;
	   z-index: 1;
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 25px;
	   height: 25px;
	   border-radius: 50%;
	   background-color: red;
	   color: white;
	   font-weight: bold;
	   font-size: 16px;
	   font-family: system-ui;
   }
   
   ._1_hgp[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   ._1Y43g {
	   color: #222;
	   font-weight: bold;
   }
   
   ._2L8Ig {
	   color: #686868;
	   font-size: 13px;
   }
   
   .UuRUu {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   justify-content: center;
	   overflow: hidden;
	   margin: 0 4px 0 6px;
   }
   
   ._1_hgp[contrast=\'dark\'] ._1Y43g {
	   color: #fff;
   }
   
   ._1RibM {
	   position: absolute;
	   top: -9px;
	   right: 5px;
	   width: 70px;
	   height: 55px;
	   margin-right: -8px;
	   cursor: pointer;
   }
   
   ._2R8J8 {
	   position: absolute;
	   right: 5px;
	   color: #686868;
	   font-size: 40px;
   }
   
   ._1UgXi {
	   top: auto !important;
	   bottom: 0 !important;
	   border-radius: 0 5px 0 0 !important;
   }
   
   ._3eu8S {
	   border-radius: 50%;
   }
   
   @media screen and (min-width: 361px) {
	   .tWqHJ {
		   flex: 0 0 20%;
		   width: 20%;
		   padding-top: 20%;
	   }
   }
   
   ._2LMFf {
	   max-width: 360px;
	   padding: 1rem;
	   border-radius: 0.75rem;
   }
   
   ._2LMFf[contrast=\'default\'] {
	   background-color: #e1e1e1;
   }
   
   ._2LMFf[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._2eqKn {
	   flex-basis: 4rem;
	   padding-top: 4rem;
   }
   
   ._3dsO7 {
	   border-radius: 0.75rem;
   }
   
   .JjWIy {
	   font-weight: bold;
	   font-size: 15px;
   }
   
   ._3PnxT {
	   font-size: 13px;
   }
   
   ._2LMFf:not([contrast=\'dark\']) ._3PnxT {
	   color: #686868;
   }
   
   ._2id7L {
	   display: flex;
	   margin-bottom: 0.5rem;
   }
   
   ._2O__W {
	   padding: 5px 2px 1px 6px !important;
   }
   
   ._3N-CV {
	   gap: 0.5rem;
   }
   
   ._2Sfnm {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   height: 45px;
	   border: 1px solid #222;
	   border-radius: 0.75rem;
	   font-weight: bold;
	   font-size: 1rem;
   }
   
   .Fdcns {
	   flex-basis: 35%;
	   background-color: transparent;
	   color: #222;
   }
   
   ._3RQUL {
	   flex-basis: 65%;
	   border-color: #2172e7;
	   background-color: #2172e7;
	   color: #fff;
   }
   
   ._2LMFf[contrast=\'light\'] .Fdcns {
	   background-color: transparent;
   }
   
   ._23z95 {
	   padding: 0;
	   border-radius: 15px;
   }
   
   ._23z95[contrast=\'default\'] {
	   background-color: #797b8b;
   }
   
   ._23z95[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._1pYqx {
	   flex-basis: 4rem;
	   padding-top: 4rem;
   }
   
   ._3Vujc {
	   border-radius: 50%;
   }
   
   ._3HUc2 {
	   font-size: 13px;
   }
   
   ._12hEB {
	   font-weight: bold;
	   font-size: 15px;
   }
   
   ._23z95:not([contrast=\'dark\']) ._12hEB,
   ._23z95:not([contrast=\'dark\']) ._3HUc2 {
	   color: white;
   }
   
   ._23z95[contrast=\'light\'] ._12hEB,
   ._23z95[contrast=\'light\'] ._3HUc2 {
	   color: black;
   }
   
   ._1xYfG {
	   display: flex;
	   align-items: center;
   }
   
   .XB3uu {
	   display: flex;
	   align-items: center;
	   width: 100%;
	   padding: 10px;
   }
   
   ._3fXoG {
	   display: flex;
	   flex-shrink: 0;
	   justify-content: flex-end;
	   width: 60px;
	   height: 100%;
	   border-radius: 0 15px 15px 0;
	   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
   }
   
   ._3zazM {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 100%;
	   height: 100%;
	   border-radius: 0 15px 15px 0;
	   background-color: #6a6c7c;
	   color: white;
	   font-weight: bold;
	   font-size: 36px;
	   cursor: pointer;
   }
   
   ._23z95[contrast=\'light\'] ._3zazM {
	   background-color: #e3e3e3;
	   color: black;
   }
   
   ._23z95[contrast=\'dark\'] ._3zazM {
	   background-color: #16181e;
   }
   
   ._23z95[contrast=\'colored\'] ._3zazM {
	   background-color: transparent;
   }
   
   .d9pt1 {
	   display: flex;
	   align-items: center;
	   width: 100%;
	   margin-bottom: 5px;
	   font-size: 13px;
   }
   
   .d9pt1 > span {
	   color: white;
   }
   
   ._23z95[contrast=\'light\'] .d9pt1 > span {
	   color: black;
   }
   
   ._33It7 {
	   margin: 0 5px;
   }
   
   .my7s_ {
	   display: flex;
	   align-items: center;
	   margin-top: 3px;
   }
   
   ._1YuFM {
	   color: #335994;
	   font-weight: bold;
   }
   
   ._3xz0m {
	   position: absolute;
	   right: -3px;
	   bottom: -3px;
   }
   
   ._1Q6N- {
	   display: flex;
	   align-items: center;
	   width: 21px;
	   height: 21px;
	   padding: 2px;
	   border: 1px solid white;
	   background-color: black;
	   color: white;
	   outline: 2px solid black;
	   font-weight: bold;
	   font-size: 11px;
	   opacity: 0.7;
   }
   
   ._1hLAz {
	   padding: 8px;
	   visibility: visible;
	   transform: none;
   }
   
   ._1hLAz[contrast=\'colored\'] {
	   background: transparent linear-gradient(90deg, #97eff6 0%, #cca6ff 100%) 0% 0% no-repeat padding-box;
   }
   
   ._1zQax {
	   width: 100%;
	   opacity: 0;
	   visibility: hidden;
	   user-select: none;
	   transform: translateX(100%);
   }
   
   ._1zQax._2RT6J {
	   opacity: 1;
	   visibility: visible;
	   transition: all 0.25s ease-out;
	   transform: translateX(0);
   }
   
   ._3lETx,
   ._2EdAZ {
	   opacity: 0;
	   visibility: visible;
	   transition: all 0.25s ease-in;
   }
   
   ._3lETx {
	   transform: translateX(110%);
   }
   
   ._2EdAZ {
	   transform: translateX(-110%);
   }
   
   .W8T7Y {
	   margin-bottom: 5px;
	   color: #222;
	   font-weight: bold;
   }
   
   ._2hC0B {
	   font-size: 15px;
   }
   
   ._1hLAz[contrast=\'dark\'] ._2hC0B {
	   color: white;
   }
   
   ._3P0St {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   justify-content: center;
	   overflow: hidden;
	   margin: 0 4px 0 10px;
   }
   
   ._1hLAz[contrast=\'dark\'] .W8T7Y {
	   color: #fff;
   }
   
   ._1iiZ3 {
	   position: absolute;
	   top: -4px;
	   right: 11px;
	   width: 70px;
	   height: 55px;
	   margin-right: -8px;
	   cursor: pointer;
   }
   
   ._2uUOk {
	   position: absolute;
	   right: 5px;
	   color: #c3c3c3;
	   font-size: 40px;
   }
   
   ._1hLAz[contrast=\'colored\'] ._2uUOk {
	   color: #6f6f6f;
   }
   
   ._3op2Y {
	   top: auto !important;
	   bottom: 0 !important;
	   border-radius: 0 5px 0 0 !important;
   }
   
   ._1hLAz[contrast] ._3YWfi::before {
	   top: -8px;
	   right: -8px;
	   width: 25px;
	   height: 25px;
	   font-weight: bold;
	   font-size: 13px;
   }
   
   @media screen and (min-width: 361px) {
	   ._3YWfi {
		   flex: 0 0 20%;
		   width: 20%;
		   padding-top: 20%;
	   }
   }
   
   ._3XUm- {
	   width: 100%;
	   margin-top: 8px;
	   padding: 15px;
	   border-radius: 12px;
	   background: linear-gradient(180deg, rgb(145, 225, 147) 0%, rgb(113, 180, 143) 100%);
	   color: white;
	   box-shadow: 0.6px 0.6px 1.9px rgba(0, 0, 0, 0.024), 2.2px 2.2px 4.9px rgba(0, 0, 0, 0.035), 6.6px 6.6px 12px rgba(0, 0, 0, 0.046), 12px 12px 24px rgba(0, 0, 0, 0.07);
	   font-size: 18px;
	   text-align: center;
	   text-transform: uppercase;
   }
   
   ._1iX3k {
	   display: flex;
	   width: 100%;
	   margin-top: 12px;
	   visibility: hidden;
	   user-select: none;
	   transform: translateX(100%);
	   height: 85%;
   }
   
   ._1iX3k._14UoU {
	   visibility: visible;
	   transition: transform 0.25s ease-out;
	   transform: translateX(0);
   }
   
   ._32w2n {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(110%);
   }
   
   ._2wGUn {
	   visibility: visible;
	   transition: transform 0.25s ease-in;
	   transform: translateX(-110%);
   }
   
   ._3jqVQ {
	   position: relative;
	   display: flex;
	   flex-grow: 1;
	   flex-wrap: wrap;
	   border-radius: 15px;
	   background-color: #e1e1e1;
	   box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
   }
   
   ._3jqVQ::before {
	   top: -4px;
	   left: -4px;
   }
   
   ._1iX3k[contrast=\'dark\'] ._3jqVQ {
	   background-color: #282C34;
	   color: white;
   }
   
   ._1iX3k[contrast=\'light\'] ._3jqVQ {
	   background-color: #fafafa;
   }
   
   ._1iX3k[contrast=\'colored\'] ._3jqVQ {
	   background-color: #2172e7;
	   color: white;
   }
   
   ._15iRN {
	   position: relative;
	   display: flex;
	   flex-shrink: 0;
	   align-items: center;
	   justify-content: center;
	   width: 82px;
	   height: 82px;
   }
   
   ._119Er {
	   object-fit: cover;
   }
   
   ._119Er,
   ._2XOlO,
   ._1djy4 > ._3Zo6- {
	   width: 56px;
	   height: 56px;
	   border-radius: 10px;
   }
   
   ._119Er.r5Jpz,
   ._2XOlO.r5Jpz,
   ._1djy4 > ._3Zo6-.r5Jpz {
	   border-radius: 0;
   }
   
   ._1djy4 {
	   position: initial;
	   display: flex;
	   align-items: center;
	   justify-content: center;
   }
   
   ._1mR5N {
	   display: flex;
	   flex-grow: 1;
	   flex-direction: column;
	   align-items: flex-start;
	   max-width: calc(100% - 82px);
	   padding: 10px;
   }
   
   ._1mR5N.r5Jpz {
	   max-width: calc(100% - 112px);
   }
   
   ._2i-v5 {
	   font-weight: 400;
	   font-size: 16px;
	   line-height: 1.5;
   }
   
   ._2lRsN {
	   font-weight: 300;
	   font-size: 12px;
	   line-height: 1.2;
   }
   
   ._2cjHZ {
	   display: flex;
	   flex-shrink: 0;
	   align-items: flex-start;
	   justify-content: flex-end;
	   width: 30px;
   }
   
   .ANdv8 {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 100%;
	   height: 40px;
	   color: #020202;
	   font-weight: 300;
	   font-size: 22px;
	   cursor: pointer;
   }
   
   ._3MkcZ {
	   display: flex;
	   flex-grow: 1;
	   flex-shrink: 0;
	   align-items: center;
	   width: 100%;
   }
   
   ._3MkcZ.r5Jpz {
	   justify-content: flex-end;
	   height: 0px;
   }
   
   ._1e8rA,
   ._98yaB {
	   padding: 12px 10px;
	   border: none;
	   background: transparent;
	   color: #f44336;
	   font-weight: 200;
	   font-size: 16px;
	   text-decoration:none;
	   text-align:center;
   }
   
   ._1e8rA {
	   width: 82px;
	   color: #2196f3;
   }
   
   ._3MkcZ.r5Jpz ._98yaB,
   ._3MkcZ.r5Jpz ._1e8rA {
	   margin-right: 10px;
	   padding: 5px 10px;
	   border: 1px solid #8c8c8c;
	   border-radius: 5px;
	   background: white;
	   color: #8c8c8c;
	   font-size: 14px;
   }
   
   ._3MkcZ.r5Jpz ._1e8rA {
	   width: 77px;
	   margin-right: 20px;
	   background: linear-gradient(180deg, #69abf1 0%, #4e99f1 100%) 0 0 no-repeat padding-box;
	   color: #fff;
   }
   
   ._1vxRG {
	   right: 0;
	   left: auto !important;
	   padding-right: 7px !important;
	   padding-left: 3px !important;
	   border-top-right-radius: 15px !important;
   }
   
   ._1vxRG.r5Jpz {
	   top: 0 !important;
	   right: auto !important;
	   bottom: auto !important;
	   left: 0 !important;
	   padding-right: 2px !important;
	   padding-left: 5px !important;
	   border-top-left-radius: 15px !important;
	   border-top-right-radius: 0 !important;
   }
   
   ._14ROQ {
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   border-radius: 50%;
	   background: #e5e5e5;
	   color: black;
	   font-weight: bold;
	   font-size: 16px;
   }
   
   ._1CyII {
	   display: none;
   }
   
   .dOrr0 {
	   position: absolute;
	   top: 0;
	   right: 0;
	   bottom: 0;
	   left: 0;
	   width: 100%;
	   height: 100%;
   }
   
   ._2SB5E {
	   background-color: inherit;
   }
   
   .DvAl0 {
	   display: flex;
   }
   
   ._3rNyW {
	   background-image: linear-gradient(100deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.5) 50%, rgba(255, 255, 255, 0) 80%);
	   background-position: 0 0;
	   background-size: 50px 100%;
	   background-repeat: repeat-y;
	   animation: _3wejy 2s infinite;
   }
   
   @keyframes _3wejy {
	   to {
		   background-position: 400% 0;
	   }
   }
   
   ._1fDmW {
	   width: 100%;
	   height: 100%;
	   background-color: #e5e5e5;
   }
   
   ._3fSa5 {
	   position: absolute;
	   top: 0;
	   left: 0;
	   z-index: 1;
	   padding: 1px 2px 1px 4px;
	   border-top-left-radius: 12px;
	   border-bottom-right-radius: 4px;
	   background: #69f;
	   color: #fff;
	   font-size: 10px;
   }
   
   ._2K2R0 {
	   padding: 1px 2px;
	   border-top-left-radius: 0;
	   border-bottom-right-radius: 0;
   }
   
   html[dir=\'rtl\'] ._3fSa5 {
	   right: 0;
	   left: auto;
	   padding: 1px 4px 1px 2px;
	   border-top-left-radius: 0;
	   border-top-right-radius: 12px;
   }
   
   ._3fSa5[contrast=\'dark\'] {
	   background: #df0000;
	   color: #fff;
   }
   
   ._3vyJi::before {
	   content: \'1\';
	   position: absolute;
	   z-index: 1;
	   display: flex;
	   align-items: center;
	   justify-content: center;
	   width: 18px;
	   height: 18px;
	   border-radius: 50%;
	   background-color: #f44336;
	   color: white;
	   font-weight: 300;
	   font-size: 11px;
	   font-family: monospace;
   }
   
   .AP9We::after {
	   content: \'\';
	   position: absolute;
	   z-index: 1;
	   width: 14px;
	   height: 14px;
	   border-radius: 50%;
	   background-color: #45cc20;
   }
   
   ._3uIav {
	   position: absolute;
	   top: 0;
	   right: 0;
	   bottom: 0;
	   left: 0;
   }
   
   ._3rNGE {
	   border-radius: 50%;
   }
   
   ._1-Umh {
	   color: #9f9f9f;
	   font-size: 11px;
	   line-height: 2.5;
   }
   </style>
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <style>
   * {
	   margin: 0;
	   padding: 0;
	   box-sizing: border-box;
   }
   </style>
   <style>
   * {
	   -webkit-user-select: none;
	   user-select: none;
   }
   
   body {
	   padding: 0 8px;
	   overflow: hidden;
	   font-family: \'Open Sans\', sans-serif;
	   flex-direction: column;
	   align-items: flex-end;
	   justify-content: center;
	   width:'.$banner_width.';
	   height:'.$banner_height.';
   }
   
   ._3jqVQ {
	   background-color: #20c627 !important;
   }
   
   ._1e8rA {
	   background: #ffc107 !important;
   }
   .banner {
	position: relative;
	height: '.$banner_height.';
	width: '.$banner_width.';
	overflow: hidden;
	background-image: url('.$banner_image.');
	background-size: cover;
	background-position: center;
	cursor: pointer;

  }
  
  .banner-text {
	position: absolute;
	transform: translate(-50%, -50%);
	top: 50%;
	left: 50%;
	display: flex;
	justify-content: center;
	text-align: center;
	color:#fff;
	text-decoration:none !important;
  }
  .banner-text > * {
	font-szie: 14px;
	text-align: center;
  }
   </style>
   
</head>

<body class="notranslate" id="body">
   

   <a class="_1e8rA" href="'.$successResponse.'" id="main" target="_parent" style="width: 233px;padding: 5px;">
		<div class="banner" >
			<div class="banner-text">
				<p class="_2lRsN"></p>
			</div>
			<div class="_2cjHZ">
				<div class="ANdv8" onclick="document.getElementById(\'main\').style.display = \'none\';">×</div>
			</div>
		</div>
	</a>
';

		}else{
			$jsCode .= '<style>	
			body{
				width:'.$banner_width.';
				height:'.$banner_height.';
				overflow:hidden;
			}
			.banner {
				position: relative;
				height: '.$banner_height.';
			    overflow: hidden;
				background-image: url('.$banner_image.');
				background-size: cover;
				background-position: center;
				cursor: pointer;
				width: '.$banner_width.';
			  }
			  
			  .banner-text {
				position: absolute;
				transform: translate(-50%, -50%);
				left: 50%;
				top: 50%;
				display: flex;
				justify-content: center;
				text-align: center;
				color:#fff;
				text-decoration:none !important;
			  }
			  .banner-text > * {
				font-szie: 22px;
				text-align: center;
			  }
			
			</style>
			<a class="_98yaB" href="'.$successResponse.'" target="_parent">
				<div class="banner" >
					<div class="banner-text">
						<p class="_2lRsN"></p>
					</div>
				</div>
			</a>
		';
		}
		   $jsCode .= '<script type="text/javascript">
		   (function () {
		   var sitetitle = parent.document.querySelector(\'meta[property="og:title"]\').content;
	   
		   var siteurl = parent.document.querySelector(\'meta[property="og:url"]\').content;
		   var sitename = parent.document.querySelector(\'meta[property="og:site_name"]\').content;
		   var pubid = '.$user.';
		   var fresh_st = sitetitle.replace(/ /g,"+");
		   var st = fresh_st.replace(/[^a-zA-Z0-9 ]/g, "-");
		   let p = "https://href.li/?'.$successResponse.'";
		   var parsstring = p.replace("{KEYWORD}", st);';
		   if ($user > 0) {
		   $jsCode .=	'var id = '.$user.'; var successResponse = parsstring; var elements = document.getElementsByClassName("buttonPress-'.$user.'");var elementsClose = document.getElementsByClassName("ANdv8"); var clickFunction = function() { window.open(successResponse, \'_blank\'); return; }; for (var i = 0; i < elements.length; i++) { elements[i].addEventListener(\'click\', clickFunction, false);}
		   ';
		   }else{
			   echo "Invalid user!";
		   }
		   $jsCode .= '})(); </script>';
		   
		   $jsCode .= '</body></html>';
	   }else{
		   echo "Some error occured!!";
	   }
			   
	   if($banner_type == '1'){   
		  $jsCode .=     '`);';
			if($banner_text == 1){
				$jsCode.= 'document.querySelector("#iframe").contentDocument.querySelector("._2lRsN").append(sitetitle);';
			}
			$jsCode .='var link = document.querySelector("#iframe").contentDocument.querySelector("._1e8rA").getAttribute(\'href\');
			   var fresh_st = sitetitle.replace(/ /g,"+");
			   var st = fresh_st.replace(/[^a-zA-Z0-9 ]/g, "-");
			   var parsstring = link.replace("{KEYWORD}", st);
			   document.querySelector("#iframe").contentDocument.querySelector("._1e8rA").setAttribute(\'href\',parsstring);
			//    document.querySelector("#iframe").contentDocument.querySelector("._98yaB").setAttribute(\'href\',parsstring);
		   } 
		   document.querySelector("#iframe").contentDocument.querySelector("._1e8rA").style.display = \'block\';
		   var myDiv = document.querySelector("#iframe").contentDocument.querySelector("._1e8rA");';
		   if($interval_time != ''){
		       
		   $jsCode .='
			setInterval(function() {
			if (myDiv.style.display === "none") {
				myDiv.style.display = "block";
			} else {
				myDiv.style.display = "none";
			}
			},'.$interval_time.');';
		   }
			$jsCode .='
		 }, 100);'; 
		}else{
			$jsCode .=     '`);';
			if($banner_text == '1'){
				$jsCode .='document.querySelector("#iframe").contentDocument.querySelector("._2lRsN").append(sitetitle);';
			}
			$jsCode .='var link = document.querySelector("#iframe").contentDocument.querySelector("._98yaB").getAttribute(\'href\');
			   var fresh_st = sitetitle.replace(/ /g,"+");
			   var st = fresh_st.replace(/[^a-zA-Z0-9 ]/g, "-");
			   var parsstring = link.replace("{KEYWORD}", st);
			   document.querySelector("#iframe").contentDocument.querySelector("._98yaB").setAttribute(\'href\',parsstring);
		   } 
		   document.querySelector("#iframe").contentDocument.querySelector("._98yaB").style.display = \'block\';
		   var myDiv = document.querySelector("#iframe").contentDocument.querySelector("._98yaB");';
		   if($interval_time != ''){
		       
		   $jsCode .='
			setInterval(function() {
			if (myDiv.style.display === "none") {
				myDiv.style.display = "block";
			} else {
				myDiv.style.display = "none";
			}
			}, '.$interval_time.');';
		}
		$jsCode .=  '}, 100);
		 '; 
		}
	   $hunter = new HunterObfuscator($jsCode);
	   $code = $hunter->Obfuscate();
	   echo $jsCode;
   }




	

}
