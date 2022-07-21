<?php 

class Admin_model extends CI_Model {

    public function count_dataakta()
	{
		return $this->db->count_all('data_akta');
    }

    public function count_sirkulasi()
	{
		return $this->db->count_all('sirkulasi');
    }

    public function count_user()
	{
		return $this->db->count_all('master_user');
    }
    
}