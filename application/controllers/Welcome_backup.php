<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('HomeModel');
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
                '/webos/i'              =>  'mobile'
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
      if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    
      else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    
      else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    
      else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    
      else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    
      else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    
      else
        $ipaddress = 'UNKNOWN';
    
      return $ipaddress;
    }
	public function index(){
	    header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/javascript');
		if (isset($_GET['user']) && $_GET['user'] > 0) {
			$new_hash 	= time()+60;
			$user 		= $_GET['user'];
			
			$result = $this->HomeModel->get_active_domain();
			if ($result) {
				$redirecturl = $result->ad_url;
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
			}
			
			$adcode_no = '&adcode=1&h='.$_GET['h'];
			
			 $todaydate = date('Y-m-d H:i:s');
            $userip = $this->get_client_ip();
            $opver  = $this->get_os_version();
            $sql2 = "INSERT INTO tbl_statistics_btn(user_ip, stat_date, pub_id, domain_id,stat_os, stat_status)VALUES('$userip','$todaydate', $user, 0, '$opver', 0)";
            $result2 = $this->db->query($sql2);
            
			$successResponse = $redirecturl.$adcode_no;
			$id = $result->ad_id; 
			$jsCode = '(function () {
			var sitetitle = document.querySelector(\'meta[property="og:title"]\').content;
			var siteurl = document.querySelector(\'meta[property="og:url"]\').content;
			var pubid = '.$user.';
			var fresh_st = sitetitle.replace(/ /g,"+");
			var st = fresh_st.replace(/[^a-zA-Z0-9 ]/g, "-");
			let p = "https://href.li/?'.$successResponse.'";
			var parsstring = p.replace("{KEYWORD}", st);';
			if ($user > 0) {
				$admarket = "installusd5";
				$md5 = md5($id.'-'.$admarket.'-'.$user);
			$jsCode .=	'var id = '.$user.'; var successResponse = parsstring; var elements = document.getElementsByClassName("buttonPress-"+'.$user.'); var clickFunction = function() { window.open(successResponse, \'_blank\'); return; }; for (var i = 0; i < elements.length; i++) { elements[i].addEventListener(\'click\', clickFunction, false);}';
			}else{
				echo "Invalid user!";
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
		if (isset($_GET['user']) && $_GET['user'] > 0 && isset($_GET['file']) && $_GET['file'] != '') {
			$new_hash 	= time()+60;
			$user 		= $_GET['user'];
			
			$keyword 	= $this->removespecialchar($_GET['file']);
			$f_keyword = str_replace(" ", "+", $keyword);
			$f_keyword = str_replace("/", "_", $f_keyword);

			$result = $this->HomeModel->get_active_domain();
			if ($result) {
				$redirecturl = $result->ad_url; 
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
				$redirecturl = str_replace("{KEYWORD}", $f_keyword, $redirecturl);
				$redirecturl = str_replace("{hash}", $new_hash, $redirecturl);
				$redirecturl = str_replace("{13_hash}", $this->get_only_digit(13), $redirecturl);
				$redirecturl = str_replace("{17_hash}", $this->get_only_digit(17), $redirecturl);
				$redirecturl = str_replace("{32_hash}", $this->get_only_digit(32), $redirecturl);

			}
		//	print_r($result);exit;
			$adcode_no = '&adcode=2&h='.$_GET['h'];
			$successResponse = $redirecturl.$adcode_no;
			$id = $result->ad_id; 

			?>
			<script type="text/javascript">
				window.open("<?php echo 'https://href.li/?'.$successResponse;?>", "_self");
			</script>
			<?php
		}else{
			echo "Some error occured!!";
		}
	}
	
	public function popup(){
	     header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/javascript');
        if (isset($_GET['user']) && $_GET['user'] > 0 && isset($_GET['file']) && $_GET['file'] != '') {
			$new_hash 	= time()+60;
			$user 		= $_GET['user'];
			
			$keyword 	= $this->removespecialchar($_GET['file']);
			$f_keyword = str_replace(" ", "+", $keyword);
			$f_keyword = str_replace("/", "_", $f_keyword);

			$result = $this->HomeModel->get_active_domain();
			if ($result) {
				$redirecturl = $result->ad_url; 
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
				$redirecturl = str_replace("{KEYWORD}", $f_keyword, $redirecturl);
				$redirecturl = str_replace("{hash}", $new_hash, $redirecturl);
				$redirecturl = str_replace("{13_hash}", $this->get_only_digit(13), $redirecturl);
				$redirecturl = str_replace("{17_hash}", $this->get_only_digit(17), $redirecturl);
				$redirecturl = str_replace("{32_hash}", $this->get_only_digit(32), $redirecturl);

			}
			$successResponse = $redirecturl.'&template=popup';
			$id = $result->ad_id; 
            $jsCode = '(function () {';
            $jsCode .= 'const navbar = document.querySelector("body");
                        navbar.addEventListener("click", function() {
                        window.open("https://href.li/?'. $successResponse.'", "_blank");
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
	    $jsCode = 'var newScript = document.createElement(\'script\');
          newScript.src = "https://code.jquery.com/jquery-3.6.0.min.js";
          newScript.defer = true;
          document.head.appendChild(newScript);';
         $jsCode .= '
         document.getElementsByTagName(\'body\')[0].insertAdjacentHTML(\'beforeend\',\'<iframe id="iframe" style="width: 100% !important; height: 149px !important; margin: 0px; padding: 0px; border: none; outline: none; box-sizing: border-box; position: fixed; inset: auto 0px 0px auto !important; overflow: hidden; z-index: 2147483640; min-width: 240px !important; display: block !important; max-width: 400px !important;"></iframe>\');
          setTimeout(function() {
            var iframe = document.getElementById(\'iframe\');
            var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            if (  iframeDoc.readyState  == \'complete\' ) {
                var sitetitle = parent.document.querySelector(\'meta[property="og:title"]\').content;
                var body = document.querySelector("#iframe").contentDocument.querySelector("body");
                body.insertAdjacentHTML(\'beforeend\',`';
                
            $user = $_GET['user'];    
            $h    = $_GET['h'];    
            $adcode = $_GET['adcode'];    
            if ($user > 0) {
			$new_hash 	= time()+60;
			
			$result = $this->HomeModel->get_active_domain();
			if ($result) {
				$redirecturl = $result->ad_url;
				$redirecturl = str_replace("{pubid}", $user, $redirecturl);
			}
			
			if ($adcode != ''){
			    $adcode_no = '&adcode='.$adcode.'&h='.$h;
			}else{
			    $adcode_no = '';
			}
			
			 $todaydate = date('Y-m-d H:i:s');
            $userip = $this->get_client_ip();
            $opver  = $this->get_os_version();
            $sql2 = "INSERT INTO tbl_statistics_btn(user_ip, stat_date, pub_id, domain_id,stat_os, stat_status)VALUES('$userip','$todaydate', $user, 0, '$opver', 0)";
            $result2 = $this->db->query($sql2);
            
			$successResponse = $redirecturl.$adcode_no;
			$id = $result->ad_id; 
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
		font-size: 17px;
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
		display: flex;
		flex-direction: column;
		align-items: flex-end;
		justify-content: center;
	}
	
	._3jqVQ {
		background-color: #20c627 !important;
	}
	
	._1e8rA {
		background: #ffc107 !important;
	}
	</style>
    
</head>

<body class="notranslate" id="body">
	<div class="_1iX3k _14UoU" contrast="dark" id="main">
		<div class="_3jqVQ">
			<div class="_15iRN"><img class="_119Er r5Jpz" src="https://i.ibb.co/LvPfYQm/image.jpg"></div>
			<div class="_1mR5N r5Jpz">
				<div class="_2i-v5">Download</div>
                <div class="_2lRsN"></div>
			</div>
			<div class="_2cjHZ">
				<div class="ANdv8" onclick="document.getElementById(\'main\').style.display = \'none\';">×</div>
			</div>
			<div class="_3MkcZ r5Jpz">
				<a target="_parent" href="'.$successResponse.'" class="_1e8rA buttonPress-'.$user.'">Open</a>
				<a target="_parent" href="'.$successResponse.'" class="_98yaB buttonPress-'.$user.'">See File</a>
			</div>
		</div>
	</div>
';
			$jsCode .= '<script type="text/javascript">
			alert();
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
                
                
           $jsCode .=     '`);
                document.querySelector("#iframe").contentDocument.querySelector("._2lRsN").append(sitetitle);
                var link = document.querySelector("#iframe").contentDocument.querySelector("._1e8rA").getAttribute(\'href\');
                var fresh_st = sitetitle.replace(/ /g,"+");
    			var st = fresh_st.replace(/[^a-zA-Z0-9 ]/g, "-");
    			var parsstring = link.replace("{KEYWORD}", st)
                document.querySelector("#iframe").contentDocument.querySelector("._1e8rA").setAttribute(\'href\',parsstring);
                document.querySelector("#iframe").contentDocument.querySelector("._98yaB ").setAttribute(\'href\',parsstring);
            } 
          }, 100);'; 
	    $hunter = new HunterObfuscator($jsCode);
		$code = $hunter->Obfuscate();
		echo $jsCode;
	}



	

}
