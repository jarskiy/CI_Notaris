<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Dokumen extends CI_Controller {

	/**
	 * Controller class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		//if(!$this->session->username && $this->uri->segment(2)!='login' && $this->uri->segment(2)!='gologin') {
			//redirect('/home/login', 'refresh');
		//}
	}

	/**
	 * Default route for Home controller
	 * internal instance redirect to 'search' method
	 *
	 */
	//public function index()
	//{
		//$this->detail();
        //$data['title'] = '404';
        //$data['set'] = $this->crud->get('pengaturan',array('id_pengaturan' => '1'))->row();
        //$this->load->view('dokumen/index',$data);
	//}

    /**
	 * Showing login page
	 *
	 */
	public function detail($id)
	{
		//$data=[];
		//if(isset($_SERVER['HTTP_REFERER'])) {
			//$previous = $_SERVER['HTTP_REFERER'];
			//$data['previous'] = $previous;
		//}
        if (is_numeric($id)) {
			$this->session->set_flashdata('error', 'Url Hanya Bisa Diakses Setelah Dienkripsi');
			  redirect('/404', 'refresh');
		}


		$id= decrypt_url($id);

		$q="SELECT a.*,p.nama_pencipta,k.nama,k.kode nama_kode,m.nama_media,
			DATE_ADD(a.tanggal,INTERVAL k.retensi YEAR) AS b,
			(IF(DATE_ADD(a.tanggal,INTERVAL k.retensi YEAR)<CURDATE(),'sudah','belum')) AS f
			FROM data_akta a
			LEFT JOIN master_pencipta p ON p.id=a.pencipta
			LEFT JOIN master_kode k ON k.id=a.kode
			LEFT JOIN master_media m ON m.id=a.media
			WHERE a.id=$id";


		$data=$this->db->query($q)->row_array();
		$data['title'] = 'Halaman Validasi Dokumen';
        $data['set'] = $this->crud->get('pengaturan',array('id_pengaturan' => '1'))->row();
		$this->load->view('dokumen/detail',$data);
	}


}






