<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	private $data_per_page = 25;
	/**
	 * Controller class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->username && $this->uri->segment(2) != 'login' && $this->uri->segment(2) != 'gologin') {
			redirect('/home/login', 'refresh');
		}
	}

	/**
	 * Method to output complete page with header and footer
	 *
	 */
	protected function __output($nview, $data = null)
	{

		$data['set'] = $this->crud->get('pengaturan', array('id_pengaturan' => '1'))->row();

		$this->load->view('header', $data);
		$this->load->view($nview, $data);
		$this->load->view('footer');
	}

	/**
	 * Method to sanitize input data
	 *
	 * @return String
	 *
	 */
	protected function __sanitizeString($str)
	{
		// return filter_var($this->__sanitizeString( $str),FILTER_SANITIZE_STRING);
		//return $this->db->escape($this->__sanitizeString( $str));
		//return $this->db->escape(filter_var($str,FILTER_SANITIZE_STRING));
		return html_purify($str);
	}

	/**
	 * Method to compile SQL query based on search criteria
	 *
	 * @return Array or String
	 *
	 */
	protected function src($srcdata = false)
	{
		// simple search
		$katakunci = $this->__sanitizeString($this->input->get('katakunci'));
		// advanced search
		$noakta = $this->__sanitizeString($this->input->get('noakta'));
		$tanggal = $this->__sanitizeString($this->input->get('tanggal'));
		$uraian = $this->__sanitizeString($this->input->get('uraian'));
		$ket = $this->__sanitizeString($this->input->get('ket'));
		$kode = $this->__sanitizeString($this->input->get('kode'));
		$nama = $this->__sanitizeString($this->input->get('nama_dokumen'));
		$penc = $this->__sanitizeString($this->input->get('penc'));
		$med = $this->__sanitizeString($this->input->get('med'));

		$w = array();
		$klas = array();
		if ($katakunci) {
			// simple search
			$w[] = " noakta like '%" . $katakunci . "%'";
			$w[] = " nama_dokumen like '%" . $katakunci . "%'";
			$w[] = " uraian like '%" . $katakunci . "%'";
		} else {
			// advanced search
			if ($noakta != "") {
				$w[] = " noakta like '%" . $noakta . "%'";
			}
			if ($tanggal != "") {
				$w[] = " tanggal like '%" . $tanggal . "%'";
			}
			if ($kode != "" && $kode != "all") {
				//$w[] = " a.kode like '".$kode."%'";
				$klas[] = $kode;
			}
			if ($ket != "" && $ket != "all") {
				$w[] = " ket='" . $ket . "'";
			}
			if ($uraian != "") {
				$w[] = " uraian like '%" . $uraian . "%'";
			}
			if ($penc != "" && $penc != "all") {
				$w[] = " pencipta ='" . $penc . "'";
			}
			if ($med != "" && $med != "all") {
				$w[] = " media ='" . $med . "'";
			}
		}

		$q = "SELECT a.*, k.retensi, DATE_ADD(a.tanggal,INTERVAL k.retensi YEAR) AS b,k.kode nama_kode,
		  (IF(DATE_ADD(a.tanggal,INTERVAL k.retensi YEAR) < CURDATE(),'sudah','belum')) AS f,nama_media,nama_pencipta
		  FROM data_akta AS a
		  JOIN master_kode AS k ON k.id=a.kode
		  JOIN master_media AS m ON m.id=a.media
		  JOIN master_pencipta AS p ON p.id=a.pencipta
		   ";

		$q_count = "SELECT COUNT(*) AS jmldata
		FROM data_akta AS a
		JOIN master_kode AS k ON k.id=a.kode
		JOIN master_media AS m ON m.id=a.media
		JOIN master_pencipta AS p ON p.id=a.pencipta";
		if ($_SESSION['akses_klas'] != '') {
			$k = explode(',', $_SESSION['akses_klas']);
			$k = array_filter($k);
			sort($k);
			if (count($k) > 0) {
				$klas = array_merge($klas, $k);
			}
		}

		if (count($klas) > 0) {
			$w[] = " k.kode regexp '" . implode('|', $klas) . "'";
		}

		//var_dump($w); die();
		if ($katakunci) {
			$q .= " WHERE" . implode(" OR ", $w);
			$q_count .= " WHERE" . implode(" OR ", $w);
			$src = array("noakta" => $katakunci, "tanggal" => '', "uraian" => $katakunci, "ket" => '', "kode" => '', "nama_dokumen" => '', "penc" => '', "med" => '');
			$qq = array($q, $q_count, $src);
			return $qq;
		} else {
			if (count($w) > 0) {
				$q .= " WHERE" . implode(" AND ", $w);
				$q_count .= " WHERE" . implode(" AND ", $w);
			}
		}

		if (!$katakunci && $srcdata) {
			$src = array("noakta" => $noakta, "nama_dokumen" => $nama, "tanggal" => $tanggal, "uraian" => $uraian, "ket" => $ket, "kode" => $kode, "penc" => $penc, "med" => $med);
			return array($q, $q_count, $src);
		} else {
			$src = array("Kata kunci" => $katakunci);
			return array($q, $q_count, $src);
		}
	}

	/**
	 * Default route for Home controller
	 * internal instance redirect to 'search' method
	 *
	 */
	public function index()
	{
		$this->search();
	}

	/**
	 * Showing list of existing archives and search form
	 *
	 */
	public function search($offset = 0)
	{
		$this->load->model('Admin_model');

		$qq = $this->src(true); //print_r($qq); die();
		$q = $qq[0]; // var_dump($q); die();
		$data['src'] = $qq[2];

		//echo $q;
		$q2 = $qq[1];
		/* berdasarkan username */

		if ($this->session->tipe == "admin") {
			$q .= " order by tgl_input desc LIMIT $this->data_per_page";
		} else if ($this->session->tipe == "user") {
			$q .= " and a.username='" . $_SESSION['username'] . "' order by tgl_input desc LIMIT $this->data_per_page";
		} else {
		}

		$data['current_page'] = 1;
		if ($offset >= $this->data_per_page) {
			$data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
		}
		/*
		if ($page<2) {
			$offset = 0;
		} else {
			$offset = ($page*$this->data_per_page)-$this->data_per_page;
		}
		*/
		if ($offset > 0) $q .= "OFFSET $offset";
		//echo($q); die();

		$hsl = $this->db->query($q);
		$data['data'] = $hsl->result_array();

		$jmldata = $this->db->query($q2)->row()->jmldata;
		$data['jml'] = $jmldata;

		$q = "select distinct ket from data_akta order by ket asc";
		$hsl = $this->db->query($q);
		$data['ket'] = $hsl->result_array();
		$q = "select kode,nama from master_kode order by kode asc";
		$hsl = $this->db->query($q);
		$data['kode'] = $hsl->result_array();
		$q = "select * from master_pencipta order by nama_pencipta asc";
		$hsl = $this->db->query($q);
		$data['penc'] = $hsl->result_array();
		$q = "select * from master_media order by nama_media asc";
		$hsl = $this->db->query($q);
		$data['med'] = $hsl->result_array();

		$this->load->library('pagination');
		$config['base_url'] = site_url('/home/search/');
		$config['reuse_query_string'] = true;
		$config['total_rows'] = $jmldata;
		$config['per_page'] = $this->data_per_page;
		$config['num_tag_open'] = '<li class="page-item page-link">';
		$config['num_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="javascript: void(0)" disabled>';
		$config['cur_tag_close'] = '</a></li>';
		$config['next_tag_open'] = '<li class="page-item page-link">';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li class="page-item page-link">';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li class="page-item page-link">';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li class="page-item page-link">';
		$config['last_tag_close'] = '</li>';
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$this->pagination->initialize($config);
		$data['pages'] = $this->pagination->create_links();

		$data['countakta'] = $this->Admin_model->count_dataakta();
		$data['countSirkulasi'] = $this->Admin_model->count_sirkulasi();
		$data['countUser'] = $this->Admin_model->count_user();
		$data["title"] = "Home";
		$this->__output('main', $data);
	}

	public function jsonDokumenData()
	{

		$q = "select distinct ket from data_akta order by ket asc";
		$hsl = $this->db->query($q);
		$data['ket'] = $hsl->result_array();
		$q = "select kode,nama from master_kode order by kode asc";
		$hsl = $this->db->query($q);
		$data['kode'] = $hsl->result_array();
		$q = "select * from master_pencipta order by nama_pencipta asc";
		$hsl = $this->db->query($q);
		$data['penc'] = $hsl->result_array();
		$q = "select * from master_media order by nama_media asc";
		$hsl = $this->db->query($q);
		$data['med'] = $hsl->result_array();

		echo json_encode($data);
	}

	/**
	 * Download current archives data in Excel format
	 *
	 */
	public function dl()
	{
		$q = $this->src();
		$hsl = $this->db->query($q[0]);
		$data = $hsl->result_array();
		$this->load->library('excel');
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		//$this->excel->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'Data Akta');
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'No.');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 2, 'No Akta');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, 2, 'Nama Dokument');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, 2, 'Tanggal');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, 2, 'Kode');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, 2, 'Uraian');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, 2, 'Pencipta');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, 2, 'Media');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, 2, 'Ket');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, 2, 'File');
		$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, 2, 'Status Aktif');
		$row = 3;
		$redblock = array('fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'FF0000')
		));
		$no = 1;
		foreach ($data as $d) {
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $no);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $d['noakta']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $d['nama_dokumen']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $d['tanggal']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $d['nama_kode']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $d['uraian']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $d['nama_pencipta']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $d['nama_media']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $d['ket']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $d['file']);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $d['status_aktif']);
			$row++;
			$no++;
		}

		$filename = 'Data Akta -' . getdate()[0] . '.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		$objWriter->save('php://output');
	}

	/**
	 * Showing login page
	 *
	 */
	public function login()
	{
		if ($this->session->username) {
			redirect('/home', 'refresh');
		}

		$data = [];
		//if(isset($_SERVER['HTTP_REFERER'])) {
		//$previous = $_SERVER['HTTP_REFERER'];
		//$data['previous'] = $previous;
		//}
		$data['set'] = $this->crud->get('pengaturan', array('id_pengaturan' => '1'))->row();
		$this->load->view('login', $data);
	}

	/**
	 * Login authentication process
	 *
	 */
	public function gologin()
	{
		$username = $this->__sanitizeString($this->input->post('username'));
		//$password=md5($this->input->post('password'));
		$password = $this->input->post('password');
		//$previous=$this->__sanitizeString($this->input->post('previous'));
		// $q = "select * from master_user where username='$username' and password='$password'";
		$q = "SELECT * FROM master_user WHERE username='$username'";
		$user = $this->db->query($q)->row();

		/* $_SESSION['username'] = $username;
		$_SESSION['id_user'] = $user->id;
		$_SESSION['tipe'] = $user->tipe;
		$_SESSION['akses_klas'] = $user->akses_klas;
		$_SESSION['akses_modul'] = json_decode($user->akses_modul,true);
		redirect('/home', 'refresh'); */

		if ($user) {
			// check password
			if (password_verify($password, $user->password)) {
				$_SESSION['username'] = $username;
				$_SESSION['id_user'] = $user->id;
				$_SESSION['tipe'] = $user->tipe;
				$_SESSION['akses_klas'] = $user->akses_klas;
				$_SESSION['akses_modul'] = json_decode($user->akses_modul, true);
				$_SESSION['menu_master'] = false;
				if (count($_SESSION['akses_modul']) > 0) {
					$no = 0;
					foreach ($_SESSION['akses_modul'] as $key => $val) {
						if ($key == 'klasifikasi') $no++;
						if ($key == 'pencipta') $no++;
						if ($key == 'media') $no++;
						if ($key == 'user') $no++;
						if ($key == 'import') $no++;
					}
					if ($no > 0) {
						$_SESSION['menu_master'] = true;
					}
				}
				//if($previous=="") {
				$this->session->set_flashdata('success', "Anda berhasil login");
				redirect('/home', 'refresh');
				//} else {
				//header('Location: ' . $previous);
				//}
			} else {
				$this->session->set_flashdata('error', 'Username atau password yang ada masukkan salah');
				redirect('/home/login', 'refresh');
			}
		} else {
			$this->session->set_flashdata('error', 'Username atau password yang ada masukkan salah');
			redirect('/home/login', 'refresh');
		}
	}

	/**
	 * Logout process
	 *
	 */
	public function logout()
	{
		unset($_SESSION['username']);
		unset($_SESSION['id_user']);
		unset($_SESSION['tipe']);
		unset($_SESSION['akses_klas']);
		unset($_SESSION['akses_modul']);
		unset($_SESSION['menu_master']);
		redirect('/home', 'refresh');
	}

	/**
	 * Archive detail page
	 *
	 */
	public function view($id)
	{
		if (is_numeric($id)) {
			$this->session->set_flashdata('error', 'Url Hanya Bisa Diakses Setelah Dienkripsi');
			redirect('/home');
		}

		$id = decrypt_url($id);

		$q = "SELECT a.*,p.nama_pencipta,k.nama,k.kode nama_kode,m.nama_media,
			DATE_ADD(a.tanggal,INTERVAL k.retensi YEAR) AS b,
			(IF(DATE_ADD(a.tanggal,INTERVAL k.retensi YEAR)<CURDATE(),'sudah','belum')) AS f
			FROM data_akta a
			LEFT JOIN master_pencipta p ON p.id=a.pencipta
			LEFT JOIN master_kode k ON k.id=a.kode
			LEFT JOIN master_media m ON m.id=a.media
			WHERE a.id=$id";

		$data = $this->db->query($q)->row_array();
		$data['title'] = 'Lihat';

		$this->__output('vakta', $data);
	}
}
