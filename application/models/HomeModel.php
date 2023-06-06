<?php

if ( ! defined('BASEPATH')) 

	exit('No direct script access allowed'); 
	

class HomeModel extends CI_Model{ 
	public $table; 
	public function __construct() 
	{ 
		parent::__construct(); 
		$this->table = get_Class($this); 
		$this->load->database(); 
	} 
	// GET THE ACTIVE DOMAIN
	function get_active_domain($table = 'tbl_ads'){
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where('ad_status', 1);
		$this->db->order_by('rand()');
		$this->db->limit(1);
		$query = $this->db->get();	    
	    if ( $query->num_rows() > 0 ){
			$row = $query->row();
			return $row;
		}else{
			return false;
		}
	}

	// GET THE ACTIVE DOMAIN
	function get_active_tpl_domain($table = 'tbl_redirect_domain_popup'){
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where('rd_status', 1);
		$this->db->order_by('rand()');
		$this->db->limit(1);
		$query = $this->db->get();	    
	    if ( $query->num_rows() > 0 ){
			$row = $query->row();
			return $row;
		}else{
			return false;
		}
	}

	function get_ads_detail($pub_id,$site_id,$adcode_link){
		$this->db->select("*");
		$this->db->from('tbl_adcode_detail');
		$this->db->where('pub_id', $pub_id);
		$this->db->where('site_id', $site_id);
		$this->db->where('adcode_link', $adcode_link);
		$query = $this->db->get();	    	
	    if ( $query->num_rows() > 0 ){
			$row = $query->result_array();
			return $row;
		}else{
			return false;
		}
	}


	function get_capping_details($table, $user, $sid, $opver, $getcountry, $state){
		
		if($opver == 'windows'){
		    $opver = 'window';
		}
		
		$this->db->select("*");
		$this->db->from('tbl_countries');
		$this->db->where('name', $getcountry);	

		try {
			$query1 = $this->db->get();	
			if ( $query1->num_rows() == 1 ){
				$country = $query1->row();

				$this->db->select("*");
				$this->db->from($table);
				$this->db->where('pub_id', $user);
				$this->db->where('site_id', $sid);
				$this->db->like('os', $opver);
				$this->db->where('country', $country->code);
				$this->db->like('states', $state);

				$query = $this->db->get();
				if ( $query->num_rows() == 1 ){
					$row = $query->row();
					return $row;
				}else{
					return false;
				}
					
			}
				
		} catch (\Throwable $th) {
			print_r($th);
		}
		
		
	}


}

?>