<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    private $data_per_page = 5;

    /**
     * Controller class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Backup_model', 'backup');
        if (!$this->session->tipe == 'admin') {
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
        // return filter_var($this->__sanitizeString($str),FILTER_SANITIZE_STRING);
        // return $this->db->escape($this->__sanitizeString($str));
        // return $this->db->escape(filter_var($str,FILTER_SANITIZE_STRING));
        return html_purify($str);
    }

    /**
     * Method to compile SQL query for master data
     * and return data in array format
     *
     * @return Array
     *
     */
    protected function masterlist($tipe)
    {
        $data;
        switch ($tipe) {
            case "kode":
                $q = "SELECT * FROM master_kode ORDER BY kode ASC";
                $hsl = $this->db->query($q);
                $data = $hsl->result_array();
                break;
            case "pencipta":
                $q = "SELECT * FROM master_pencipta ORDER BY nama_pencipta ASC";
                $hsl = $this->db->query($q);
                $data = $hsl->result_array();
                break;
            case "media":
                $q = "SELECT * FROM master_media ORDER BY nama_media ASC";
                $hsl = $this->db->query($q);
                $data = $hsl->result_array();
                break;
        }

        return $data;
    }

    /**
     * Show archive entry form
     *
     */
    public function entr()
    {
        $data["kode"] = $this->masterlist("kode");
        $data["pencipta"] = $this->masterlist("pencipta");
        $data["media"] = $this->masterlist("media");
        $data["title"] = "Tambah";

        $this->__output('entri', $data);
    }

    /**
     * Process input data from archive entry form
     *
     */
    public function gentr()
    {
        $query = $this->db->query("select max(id) as last from data_akta");
        $data = $query->row_array();
        $last = $data['last'];
        $nextNoUrut = $last + 1;
        $id = $nextNoUrut;
        $noakta = $this->__sanitizeString($this->input->post('noakta'));
        $namaakta = $this->__sanitizeString(strtoupper($this->input->post('nama_dokumen')));
        $tanggal = $this->__sanitizeString($this->input->post('tanggal'));
        $uraian = $this->__sanitizeString($this->input->post('uraian'));
        $kode = $this->__sanitizeString($this->input->post('kode'));
        $pencipta = '1';
        $media = $this->__sanitizeString($this->input->post('media'));
        $ket = $this->__sanitizeString($this->input->post('ket'));
        $status = $this->__sanitizeString($this->input->post('status_aktif'));
        $file = "";
        $date = date('Y-m-d H:i:s');
        $config['upload_path'] = 'files/';
        $config['allowed_types'] = 'pdf|docx|doc|jpeg|jpg|png|bmp|tiff|gif';
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file')) {
            $datafile = $this->upload->data();
            //$file = $datafile['full_path'];
            $file = $datafile['file_name'];
        } else {
            echo $this->upload->display_errors();
            //echo $config['upload_path'];
            //die();
        }

        $q = sprintf(
            "INSERT INTO data_akta (id,idakta,noakta,nama_dokumen,tanggal,uraian,kode,ket,file,pencipta,media,status_aktif,tgl_input,username)
			VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s',%d,%d,%d,'%s','%s')",
            $id,
            encrypt_url($id),
            $noakta,
            $namaakta,
            $tanggal,
            $uraian,
            $kode,
            $ket,
            $file,
            $pencipta,
            $media,
            $status,
            $date,
            $_SESSION['username']
        );

        $this->load->library('ciqrcode'); //pemanggilan library QR CODE
        $config['cacheable']    = true; //boolean, the default is true
        //$config['cachedir']		= './assets/'; //string, the default is application/cache/
        //$config['errorlog']		= './assets/'; //string, the default is application/logs/
        $config['imagedir']        = './files/qrcode/'; //direktori penyimpanan qr code
        $config['quality']        = true; //boolean, the default is true
        $config['size']            = '1024'; //interger, the default is 1024
        $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);

        $image_name = encrypt_url($id) . '.png'; //buat name dari qr code sesuai dengan nim

        $params['data'] = base_url() . 'dokumen/detail/' . encrypt_url($id); //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 25;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

        $hsl = $this->db->query($q);
        if ($hsl) {
            $this->session->set_flashdata('success', "Data berhasil disimpan");
            redirect('/home/view/' . encrypt_url($id), 'refresh');
        }
    }

    /**
     * Edit archive data form
     *
     * @param $id The ID of archive
     *
     */
    public function vedit($id)
    {
        if (is_numeric($id)) {
            $this->session->set_flashdata('error', 'Url Hanya Bisa Diakses Setelah Dienkripsi');
            redirect('/home');
        }
        $id = decrypt_url($id);

        if ($id != "") {
            $q = sprintf("SELECT * FROM data_akta WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            $row = $hsl->row_array();
            //$previous = "";
            //if (isset($_SERVER['HTTP_REFERER'])) {
                //$previous = $_SERVER['HTTP_REFERER'];
                //$row['previous'] = $previous;
            //}
            $row["kode2"] = $this->masterlist("kode");
            $row["pencipta2"] = $this->masterlist("pencipta");
            $row["media2"] = $this->masterlist("media");
            $row["title"] = "Ubah";
            if (count($row) > 0) {
                $this->__output('edit', $row);
            } else {
                redirect('/home', 'refresh');
            }
        } else {
            redirect('/home', 'refresh');
        }
    }

    /**
     * Process input data from archive edit form
     *
     */
    public function edit()
    {
        $noakta = $this->__sanitizeString($this->input->post('noakta'));
        $namaakta = $this->__sanitizeString(strtoupper($this->input->post('nama_dokumen')));
        $tanggal = $this->__sanitizeString($this->input->post('tanggal'));
        $uraian = $this->__sanitizeString($this->input->post('uraian'));
        $kode = $this->__sanitizeString($this->input->post('kode'));
        $ket = $this->__sanitizeString($this->input->post('ket'));
        $pencipta = '1';
        $media = $this->__sanitizeString($this->input->post('media'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $status = $this->__sanitizeString($this->input->post('status_aktif'));
        //$previous = $this->__sanitizeString($this->input->post('previous'));
        $file = "";
        $config['upload_path'] = 'files/';
        $config['allowed_types'] = 'pdf|docx|doc';
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file')) {
            $datafile = $this->upload->data();
            //$file = $datafile['full_path'];
            $file = $datafile['file_name'];
        } else {
            $q = "SELECT file FROM data_akta WHERE id=$id";
            $d = $this->db->query($q)->row_array()['file'];
            $file = $d;
        }

        if (isset($_POST)) {
            $q = sprintf(
                "UPDATE data_akta SET noakta='%s', nama_dokumen='%s', tanggal='%s',uraian='%s',kode='%s',
							ket='%s',file='%s',pencipta=%d, media=%d, status_aktif=%d WHERE id=$id",
                $noakta,
                $namaakta,
                $tanggal,
                $uraian,
                $kode,
                $ket,
                $file,
                $pencipta,
                $media,
                $status
            );

            $hsl = $this->db->query($q);
        }
        $this->session->set_flashdata('success', "Data berhasil disimpan");
        redirect('/home');
        //redirect('/home/view/' . $id, 'refresh');
        /* if($previous=="") {
    redirect('/home/view/'.$id, 'refresh');
    }else {
    header('Location: ' . $previous);
    } */
    }

    /**
     * Delete archive file value in archive record
     *
     */
    public function delfile()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = "SELECT file FROM data_akta WHERE id=$id";
        $hsl = $this->db->query($q);
        $row = $hsl->row_array()['file'];
        if ($row != "") {
            $alamat = ROOTPATH . "/files/" . $row;
            unlink($alamat);
        }
        $q = sprintf("UPDATE data_akta SET file=NULL WHERE id=%d", $id);
        $hsl = $this->db->query($q);
    }

    /**
     * Delete archive file
     *
     */
    public function del1()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("SELECT count(id) jml FROM sirkulasi WHERE noakta=%d", $id);
        $jml = $this->db->query($q)->row_array()['jml'];
        if ($jml == 0) { //kalau tidak data arsip yang menggunakan, boleh dihapus
            $q = sprintf("SELECT idakta,file FROM data_akta WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            $row = $hsl->row_array()['file'];
            $row2 = $hsl->row_array()['idakta'];
            if ($row != "") {
                $alamat = ROOTPATH . "/files/" . $row;
                unlink($alamat);
            }
            if ($row2 != "") {
                $alamat2 = ROOTPATH . "/files/qrcode/" . $row2 . '.png';
                unlink($alamat2);
            }
            $q = sprintf("DELETE FROM data_akta WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            if ($hsl) {
                echo json_encode(array('msg' => 'ok'));
            }
            exit();
        } else {
            echo json_encode(array('msg' => 'error'));
        }
    }

    /**
     * Show classification data page
     *
     */
    public function klas($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_kode ";
        if ($katakunci) {
            $q .= ' WHERE kode LIKE \'%' . $katakunci . '%\' OR nama LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_kode";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $data['user'] = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/klas/');
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

        $data["title"] = "Master Klasifikasi";
        $this->__output('master/klasifikasi', $data);
    }

    /**
     * Add classification data and respond in JSON format
     *
     */
    public function addkode()
    {
        $kode = $this->__sanitizeString($this->input->post('kode'));
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $retensi = $this->__sanitizeString($this->input->post('retensi'));
        $q = sprintf(
            "INSERT INTO master_kode (kode,nama,retensi) VALUES ('%s','%s','%s')",
            $kode,
            $nama,
            $retensi
        );
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Update classification data and respond in JSON format
     *
     */
    public function edkode()
    {
        $kode = $this->__sanitizeString($this->input->post('kode'));
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $retensi = $this->__sanitizeString($this->input->post('retensi'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf(
            "UPDATE master_kode SET kode='%s',nama='%s',retensi='%s' WHERE id=%d",
            $kode,
            $nama,
            $retensi,
            $id
        );
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Delete classification data and respond in JSON format
     *
     */
    public function delkode()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        //cek dulu apakah ada akta yang menggunakan klasifikasi ini
        $q = sprintf("SELECT count(id) jml FROM data_akta WHERE kode=%d", $id);
        $jml = $this->db->query($q)->row_array()['jml'];
        if ($jml == 0) { //kalau tidak data akta yang menggunakan, boleh dihapus
            $q = sprintf("DELETE FROM master_kode WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            if ($hsl) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo '[]';
            }
            exit();
        } else { //ada akta yng menggunakan, klasifikasi jangan dihapus dulu

        }
    }

    /**
     * Get classification data and respond in JSON format
     *
     */
    public function akode()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("SELECT * FROM master_kode WHERE id=%d", $id);
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if ($row) {
            echo json_encode($row);
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * AJAX reload for classification data
     *
     */
    public function reloadkode($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_kode ";
        if ($katakunci) {
            $q .= ' WHERE kode LIKE \'%' . $katakunci . '%\' OR nama LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_kode";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $row = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/klas/');
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
        $pages = $this->pagination->create_links();

        if ($row) {
            echo "<div class='table-responsive'><table class='table table-bordered' name='vkode' id='vkode'>
			<thead>
				<th>Kode</th>
				<th>Nama</th>
				<th class='width-sm'></th>
				<th class='width-sm'></th>
			</thead>";
            $no = 1;
            foreach ($row as $u) {
                echo "<tr>";
                echo "<td>" . $u['kode'] . "</td>";
                echo "<td>" . $u['nama'] . "</td>";
                //echo "<td>" . $u['retensi'] . " Tahun</td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#editkode\" class='edkode text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i></a></td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#delkode\" class='delkode text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i></a></td>";
                echo "</tr>";
                $no++;
            }
            echo "</table></div>";
            echo "<div class=\"mt-2\">$pages</div>";
        }
    }

    /**
     * Show archive author/creator data page
     *
     */
    public function penc($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_pencipta ";
        if ($katakunci) {
            $q .= ' WHERE nama_pencipta LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_pencipta";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $data['penc'] = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/penc/');
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

        $data["title"] = "Master Pencipta Arsip";
        $this->__output('master/pencipta', $data);
    }

    /**
     * Add archive creator data and respond in JSON format
     *
     */
    public function addpenc()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $q = sprintf("INSERT INTO master_pencipta (nama_pencipta) VALUES ('%s')", $nama);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Update archive creator data and respond in JSON format
     *
     */
    public function edpenc()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("UPDATE master_pencipta SET nama_pencipta='%s' WHERE id=%d", $nama, $id);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Delete archive creator data and respond in JSON format
     *
     */
    public function delpenc()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        //cek dulu apakah ada akta yang menggunakan pencipta ini
        $q = sprintf("SELECT count(id) jml FROM data_akta WHERE pencipta=%d", $id);
        $jml = $this->db->query($q)->row_array()['jml'];
        if ($jml == 0) { //kalau tidak data akta yang menggunakan, boleh dihapus
            $q = sprintf("DELETE FROM master_pencipta WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            if ($hsl) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo '[]';
            }
            exit();
        } else {
        }
    }

    /**
     * Get archive creator data and respond in JSON format
     *
     */
    public function apenc()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("SELECT * FROM master_pencipta WHERE id=%d", $id);
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if ($row) {
            echo json_encode($row);
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * AJAX reload for archive creator
     *
     */
    public function reloadpenc($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_pencipta ";
        if ($katakunci) {
            $q .= ' WHERE nama_pencipta LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_pencipta";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $row = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/penc/');
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
        $pages = $this->pagination->create_links();

        if ($row) {
            echo "<div class='table-responsive'><table class='table table-bordered' name='vpenc' id='vpenc'>
			<thead>
				<th class='width-sm'>No</th>
				<th>Nama</th>
				<th class='width-sm'></th>
			</thead>";
            $no = 1;
            foreach ($row as $u) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $u['nama_pencipta'] . "</td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#editpenc\" class='edpenc text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i></a></td>";
                //echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#delpenc\" class='delpenc text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i></a></td>";
                echo "</tr>";
                echo "</tr>";
                $no++;
            }
            echo "</table></div>";
            echo "<div class=\"mt-2\">$pages</div>";
        }
    }

    /**
     * Show archival unit/manager data page
     *
     */
    public function pengolah($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_pengolah ";
        if ($katakunci) {
            $q .= ' WHERE nama_pengolah LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_pengolah";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $data['peng'] = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/pengolah/');
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

        $data["title"] = "Master Unit Pengolah";
        $this->__output('master/pengolah', $data);
    }

    /**
     * Add archival unit data and respond in JSON format
     *
     */
    public function addpeng()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $q = sprintf("INSERT INTO master_pengolah (nama_pengolah) VALUES ('%s')", $nama);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Update archival unit data and respond in JSON format
     *
     */
    public function edpeng()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("UPDATE master_pengolah SET nama_pengolah='%s'", $nama);
        $q .= " WHERE id=$id";
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Delete archival unit data and respond in JSON format
     *
     */
    public function delpeng()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        //cek dulu apakah ada akta yang menggunakan unit pengolah ini
        $q = sprintf("SELECT count(id) jml FROM data_akta WHERE unit_pengolah=%d", $id);
        $jml = $this->db->query($q)->row_array()['jml'];
        if ($jml == 0) { //kalau tidak data akta yang menggunakan, boleh dihapus
            $q = sprintf("DELETE FROM master_pengolah WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            if ($hsl) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo '[]';
            }
            exit();
        } else {
        }
    }

    /**
     * Get archival unit data and respond in JSON format
     *
     */
    public function apeng()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("SELECT * FROM master_pengolah WHERE id=%d", $id);
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if ($row) {
            echo json_encode($row);
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * AJAX reload for archival unit data
     *
     */
    public function reloadpeng($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_pengolah ";
        if ($katakunci) {
            $q .= ' WHERE nama_pengolah LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_pengolah";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $row = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/pengolah/');
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
        $pages = $this->pagination->create_links();

        if ($row) {
            echo "<div class='table-responsive'><table class='table table-bordered' name='vpeng' id='vpeng'>
			<thead>
				<th class='width-sm'>No</th>
				<th>Nama</th>
				<th class='width-sm'></th>
				<th class='width-sm'></th>
			</thead>";
            $no = 1;
            foreach ($row as $u) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $u['nama_pengolah'] . "</td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#editpeng\" class='edpeng text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i> </a></td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#delpeng\" class='delpeng text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i> </a></td>";
                echo "</tr>";
                $no++;
            }
            echo "</table></div>";
            echo "<div class=\"mt-2\">$pages</div>";
        }
    }

    /**
     * Show archive location data page
     *
     */
    public function lokasi($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_lokasi ";
        if ($katakunci) {
            $q .= ' WHERE nama_lokasi LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_lokasi";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $data['lok'] = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/lokasi/');
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

        $data["title"] = "Master Lokasi";

        $this->__output('master/lokasi', $data);
    }

    /**
     * Add archive location data and respond in JSON format
     *
     */
    public function addlok()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $q = sprintf("INSERT INTO master_lokasi (nama_lokasi) VALUES ('%s')", $nama);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Update archive location data and respond in JSON format
     *
     */
    public function edlok()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("UPDATE master_lokasi SET nama_lokasi='%s' WHERE id=%d", $nama, $id);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Delete archive location data and respond in JSON format
     *
     */
    public function dellok()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        //cek dulu apakah ada akta yang menggunakan lokasi ini
        $q = sprintf("SELECT count(id) jml FROM data_akta WHERE lokasi=%d", $id);
        $jml = $this->db->query($q)->row_array()['jml'];
        if ($jml == 0) { //kalau tidak data akta yang menggunakan, boleh dihapus
            $q = sprintf("DELETE FROM master_lokasi WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            if ($hsl) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo '[]';
            }
            exit();
        } else {
        }
    }

    /**
     * Get archive location data and respond in JSON format
     *
     */
    public function alok()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = "SELECT * FROM master_lokasi WHERE id=$id";
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if ($row) {
            echo json_encode($row);
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * AJAX reload for location data
     *
     */
    public function reloadlok($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_lokasi ";
        if ($katakunci) {
            $q .= ' WHERE nama_lokasi LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_lokasi";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $row = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/lokasi/');
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
        $pages = $this->pagination->create_links();

        if ($row) {
            echo "<div class='table-responsive'><table class='table table-bordered' name='vlok' id='vlok'>
			<thead>
				<th class='width-sm'>No</th>
				<th>Nama</th>
				<th class='width-sm'></th>
				<th class='width-sm'></th>
			</thead>";
            $no = 1;
            foreach ($row as $u) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $u['nama_lokasi'] . "</td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#editlok\" class='edlok text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i> </a></td>";
                echo "<td align=\"center\"><a data-bs-toggle=\"modal\" data-bs-target=\"#dellok\" class='dellok text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i> </a></td>";
                echo "</tr>";
                $no++;
            }
            echo "</table></div>";
            echo "<div class=\"mt-2\">$pages</div>";
        }
    }

    /**
     * Show media data page
     *
     */
    public function media($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_media ";
        if ($katakunci) {
            $q .= ' WHERE nama_media LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_media";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $data['med'] = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/media/');
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

        $data["title"] = "Master Media";
        $this->__output('master/media', $data);
    }

    /**
     * Add media data and respond in JSON format
     *
     */
    public function addmed()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $q = sprintf("INSERT INTO master_media (nama_media) VALUES ('%s')", $nama);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Update media data and respond in JSON format
     *
     */
    public function edmed()
    {
        $nama = $this->__sanitizeString($this->input->post('nama'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("UPDATE master_media SET nama_media='%s' WHERE id=%d", $nama, $id);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Delete media data and respond in JSON format
     *
     */
    public function delmed()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        //cek dulu apakah ada akta yang menggunakan media ini
        $q = sprintf("SELECT count(id) jml FROM data_akta WHERE media=%d", $id);
        $jml = $this->db->query($q)->row_array()['jml'];
        if ($jml == 0) { //kalau tidak data akta yang menggunakan, boleh dihapus
            $q = sprintf("DELETE FROM master_media WHERE id=%d", $id);
            $hsl = $this->db->query($q);
            if ($hsl) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo '[]';
            }
            exit();
        } else {
        }
    }

    /**
     * Get media data and respond in JSON format
     *
     */
    public function amed()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = "SELECT * FROM master_media WHERE id=$id";
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if ($row) {
            echo json_encode($row);
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * AJAX reload for media data
     *
     */
    public function reloadmed($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_media ";
        if ($katakunci) {
            $q .= ' WHERE nama_media LIKE \'%' . $katakunci . '%\' OR id LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_media";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $row = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/media/');
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
        $pages = $this->pagination->create_links();

        if ($row) {
            echo "<div class='table-responsive'><table class='table table-bordered' name='vmed' id='vmed'>
			<thead>
				<th class='width-sm'>No</th>
				<th>Nama</th>
				<th class='width-sm'></th>
				<th class='width-sm'></th>
			</thead>";
            $no = 1;
            foreach ($row as $u) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $u['nama_media'] . "</td>";
                echo "<td align=\"center\"><a data-toggle=\"modal\" data-target=\"#editmed\" class='edmed text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i> </a></td>";
                echo "<td align=\"center\"><a data-toggle=\"modal\" data-target=\"#delmed\" class='delmed text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i> </a></td>";
                echo "</tr>";
                $no++;
            }
            echo "</table></div>";
            echo "<div class=\"mt-2\">$pages</div>";
        }
    }

    /**
     * Show user data page
     *
     */
    public function vuser($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_user ";
        if ($katakunci) {
            $q .= ' WHERE username LIKE \'%' . $katakunci . '%\' OR tipe LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_user";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $data['user'] = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/vuser/');
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

        $data["title"] = "Master User";
        $this->__output('master/user', $data);
    }

    /**
     * Check for user data and respond in JSON format
     *
     */
    public function cekuser()
    {
        $username = $this->__sanitizeString($this->input->post('username'));
        $q = "SELECT username FROM master_user WHERE username='$username'";
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if (isset($row['username']) == $username) {
            echo json_encode(array('msg' => 'error'));
        } else {
            echo json_encode(array('msg' => 'ok'));
        }
    }

    /**
     * Add user data and respond in JSON format
     *
     */
    public function adduser()
    {
        $password_str = $this->input->post('password');
        $conf_password_str = $this->input->post('conf_password');
        if ($password_str !== $conf_password_str) {
            echo json_encode(array('status' => 'error', 'pesan' => 'PASSWORD_UNMATCH'));
            exit();
        }

        $username = $this->__sanitizeString($this->input->post('username'));
        $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $tipe = $this->__sanitizeString($this->input->post('tipe'));
        $akses_klas = $this->__sanitizeString($this->input->post('akses_klas'));
        $akses_modul = json_encode($this->input->post('modul'));
        $q = sprintf(
            "INSERT INTO master_user (username,password,tipe,akses_klas,akses_modul) VALUES ('%s','%s','%s','%s','%s')",
            $username,
            $password,
            $tipe,
            $akses_klas,
            $akses_modul
        );
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Update user data and respond in JSON format
     *
     */
    public function eduser()
    {
        $username = $this->__sanitizeString($this->input->post('username'));
        $password = "";
        if ($this->input->post('password') != "") {
            $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        }
        $tipe = $this->__sanitizeString($this->input->post('tipe'));
        $akses_klas = $this->__sanitizeString($this->input->post('akses_klas'));
        $akses_modul = json_encode($this->input->post('modul'));
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("UPDATE master_user SET username='%s'", $username);
        if ($password != "") {
            $q .= sprintf(",password='%s'", $password);
        }

        $q .= sprintf(
            ",tipe='%s',akses_klas='%s',akses_modul='%s' WHERE id=%d",
            $tipe,
            $akses_klas,
            $akses_modul,
            $id
        );
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Delete user data and respond in JSON format
     *
     */
    public function deluser()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("DELETE FROM master_user WHERE id=%d", $id);
        $hsl = $this->db->query($q);
        if ($hsl) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * Get user data in JSON format
     *
     */
    public function auser()
    {
        $id = $this->__sanitizeString($this->input->post('id'));
        $q = sprintf("SELECT * FROM master_user WHERE id=%d", $id);
        $hsl = $this->db->query($q);
        $row = $hsl->row_array();
        if ($row) {
            echo json_encode($row);
        } else {
            echo '[]';
        }
        exit();
    }

    /**
     * AJAX reload for user data
     *
     */
    public function reloaduser($offset = 0)
    {
        $katakunci = $this->__sanitizeString($this->input->get('katakunci'));

        $q = "SELECT * FROM master_user ";
        if ($katakunci) {
            $q .= ' WHERE username LIKE \'%' . $katakunci . '%\' OR tipe LIKE \'%' . $katakunci . '%\' ';
        }
        $q .= " ORDER BY id ASC LIMIT $this->data_per_page";
        $q_count = "SELECT COUNT(*) AS jmldata FROM master_user";

        $data['current_page'] = 1;
        if ($offset >= $this->data_per_page) {
            $data['current_page'] = floor(($offset + $this->data_per_page) / $this->data_per_page);
        }
        if ($offset > 0) $q .= " OFFSET $offset";

        $jmldata = $this->db->query($q_count)->row()->jmldata;
        $data['jml'] = $jmldata;

        $hsl = $this->db->query($q);
        $row = $hsl->result_array();

        $this->load->library('pagination');
        $config['base_url'] = site_url('/admin/vuser/');
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
        $pages = $this->pagination->create_links();

        if ($row) {
            echo "<div class='table-responsive'><table class='table table-bordered' name='vuser' id='vuser'>
			<thead>
				<th class='width-sm'>No</th>
				<th>Username</th>
				<th>Akses Klasifikasi</th>
				<th>Akses Modul</th>
				<th>Tipe</th>
				<th class='width-sm'></th>
				<th class='width-sm'></th>
			</thead>";
            $no = 1;
            foreach ($row as $u) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $u['username'] . "</td>";
                echo "<td>" . $u['akses_klas'] . "</td>";
                echo "<td>";
                $mm = $u['akses_modul'];
                if ($mm != "") {
                    $mm = json_decode($mm);
                    if ($mm) {
                        foreach ($mm as $key => $val) {
                            echo $key . ",";
                        }
                    }
                }
                echo "</td>";
                echo "<td>" . $u['tipe'] . "</td>";
                echo "<td align=\"center\"><a data-toggle=\"modal\" data-target=\"#edituser\" class='eduser text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i> </a></td>";
                echo "<td align=\"center\"><a data-toggle=\"modal\" data-target=\"#deluser\" class='deluser text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i> </a></td>";
                echo "</tr>";
                $no++;
            }
            echo "</table></div>";
            echo "<div class=\"mt-2\">$pages</div>";
        }
    }

    /**
     * Export/import data page
     *
     */
    public function import()
    {
        $this->__output('import');
    }

    /**
     * Export data to Excel file
     *
     */
    public function exportdata()
    {
        include 'dbimexport.php';
        $db_config = array(
            'dbtype' => "MYSQL",
            'host' => $this->db->hostname,
            'database' => $this->db->database,
            'user' => $this->db->username,
            'password' => $this->db->password,
        );
        $dbimexport = new dbimexport($db_config);
        $dbimexport->download_path = "";
        $dbimexport->download = true;
        $dbimexport->file_name = "backup_data_" . date("Y-m-d_H-i-s");
        $dbimexport->export();
    }

    /**
     * Import data from Excel file
     *
     */
    public function importdata()
    {
        if ($_FILES["up_file"]["name"]) {
            $source = $_FILES["up_file"]["tmp_name"];
            $this->load->library('excel');
            $read = PHPExcel_IOFactory::createReaderForFile($source);
            $read->setReadDataOnly(true);
            $excel = $read->load($source);
            $sheets = $read->listWorksheetNames($source); //baca semua sheet yang ada
            foreach ($sheets as $sheet) {
                $_sheet = $excel->setActiveSheetIndexByName($sheet); //Kunci sheetnye biar kagak lepas :-p
                $maxRow = $_sheet->getHighestRow();
                $maxCol = $_sheet->getHighestColumn();
                $field = array();
                $sql = array();
                $AllCol = range('A', $maxCol);
                //echo implode(",", $AllCol);
                foreach ($AllCol as $key => $coloumn) {
                    $field[$key] = $this->__sanitizeString($_sheet->getCell($coloumn . '2')->getCalculatedValue()); //Kolom pertama sebagai field list pada table
                }
                for ($i = 3; $i <= $maxRow; $i++) {
                    foreach ($AllCol as $k => $coloumn) {
                        $sql[$field[$k]] = $this->__sanitizeString($_sheet->getCell($coloumn . $i)->getCalculatedValue());
                    }
                    $noakta = (isset($sql['No.Akta']) ? $sql['No.Akta'] : "");
                    $namadokumen = (isset($sql['Nama Dokumen']) ? $sql['Nama Dokumen'] : "");
                    $tanggal = (isset($sql['Tanggal']) ? $sql['Tanggal'] : "");
                    $uraian = (isset($sql['Uraian']) ? $sql['Uraian'] : "");
                    $id_kode = "";
                    $ket = (isset($sql['Ket']) ? $sql['Ket'] : "");
                    $file = "";
                    $id_penc = "";
                    $id_med = "";
                    $user = (isset($sql['username']) ? $sql['username'] : "");
                    if (isset($sql["Kode Klasifikasi"]) && $sql["Kode Klasifikasi"] != "") {
                        $s = $sql["Kode Klasifikasi"];
                        $this->db->where('kode', $s);
                        $kode = $this->db->get('master_kode')->result_array();
                        if (count($kode) > 0) {
                            $id_kode = $kode[0]['id'];
                        } else {
                            $q = "insert ignore into master_kode (kode) values('$s');";
                            $this->db->query($q);
                            $id_kode = $this->db->insert_id();
                        }
                        $sql["Kode Klasifikasi"] = $id_kode;
                    }
                    if (isset($sql["Pencipta"]) && $sql["Pencipta"] != "") {
                        $s = $sql["Pencipta"];
                        $this->db->where('nama_pencipta', $s);
                        $kode = $this->db->get('master_pencipta')->result_array();
                        if (count($kode) > 0) {
                            $id_penc = $kode[0]['id'];
                        } else {
                            $q = "insert ignore into master_pencipta (nama_pencipta) values('$s');";
                            $this->db->query($q);
                            $id_penc = $this->db->insert_id();
                        }
                        $sql["Pencipta"] = $id_penc;
                    }
                    if (isset($sql["Media"]) && $sql["Media"] != "") {
                        $s = $sql["Media"];
                        $this->db->where('nama_media', $s);
                        $kode = $this->db->get('master_media')->result_array();
                        if (count($kode) > 0) {
                            $id_med = $kode[0]['id'];
                        } else {
                            $q = "insert ignore into master_media (nama_media) values('$s');";
                            $this->db->query($q);
                            $id_med = $this->db->insert_id();
                        }
                        $sql["Media"] = $id_med;
                    }
                    //echo "<pre>" . var_dump($sql) . "</pre>";
                    $q = sprintf(
                        "INSERT IGNORE INTO data_akta (idakta,noakta,nama_dokumen,tanggal,uraian,kode,ket,file,pencipta,media,tgl_input,username)
			        VALUES ('%s','%s','%s','%s','%s','%s','%s','%s',%d,%d,now(),'%s')",
                        encrypt_url($noakta),
                        $noakta,
                        $namadokumen,
                        $tanggal,
                        $uraian,
                        $id_kode,
                        $ket,
                        $file,
                        $id_penc,
                        $id_med,
                        $user
                    );
                    //echo $q . "<br/>";
                    $this->load->library('ciqrcode'); //pemanggilan library QR CODE
                    $config['cacheable']    = true; //boolean, the default is true
                    //$config['cachedir']		= './assets/'; //string, the default is application/cache/
                    //$config['errorlog']		= './assets/'; //string, the default is application/logs/
                    $config['imagedir']        = './files/qrcode/'; //direktori penyimpanan qr code
                    $config['quality']        = true; //boolean, the default is true
                    $config['size']            = '1024'; //interger, the default is 1024
                    $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
                    $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)
                    $this->ciqrcode->initialize($config);

                    $image_name = encrypt_url($noakta) . '.png'; //buat name dari qr code sesuai dengan nim

                    $params['data'] = base_url() . 'dokumen/detail/' . encrypt_url($noakta); //data yang akan di jadikan QR CODE
                    $params['level'] = 'H'; //H=High
                    $params['size'] = 25;
                    $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
                    $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE

                    $this->db->query($q);
                }
            }

            $this->session->set_flashdata('success', "Data berhasil diimport");
            redirect('/admin/import', 'refresh');
        } else {
            $this->session->set_flashdata('error', "Tidak ada file yang diupload");
            redirect('/admin/import', 'refresh');
        }
    }

    /**
     * Show Backup DB page
     *
     */
    public function backup()
    {
        $data["title"] = "Backup DB";
        $this->__output('backup', $data);
    }

    public function backup_list()
    {
        $list = $this->backup->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $value) {
            $no++;
            $row = array();
            $row[] = $value->file_name;
            $row[] = '<a href="' . "" . base_url() . $value->file_path . "" . '" title="">' . "" . $value->file_path . "" . '</a>';
            $row[] = $value->created_at;

            //add html for action
            $row[] = '<a class="btn btn-sm btn-danger text-white" href="#" title="Hapus" onClick="delete_backup(' . "'" . $value->id_backup . "'" . ')"><i class="mdi mdi-delete"></i> Hapus</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->backup->count_all(),
            "recordsFiltered" => $this->backup->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    function backup_save()
    {
        $tanggal = date('Ymd-His');
        $namaFile = 'backup-' . $tanggal . '.sql.zip';
        $pathFile = 'files/backup';
        $this->load->dbutil();
        $backup = &$this->dbutil->backup();
        // Load the file helper and write the file to your server
        $this->load->helper('file');
        write_file($pathFile . '/' . $namaFile, $backup);

        $input = array(
            'file_name' => $namaFile,
            'file_path' => $pathFile . '/' . $namaFile,
            'created_at' => date('Y-m-d H:i:s')
        );

        $save = $this->crud->insert('backup', $input);

        echo json_encode(array("status" => TRUE));
    }

    public function backup_delete($id)
    {
        $query = $this->backup->get_by_id($id);
        unlink($query->file_path);

        $this->backup->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
}
