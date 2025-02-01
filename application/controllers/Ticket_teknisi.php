<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_teknisi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		// Load model utama sebagai 'model'
		$this->load->model('Main_model', 'model');

		// Load model tiket (pastikan file model-nya ada)
		$this->load->model('Ticket_models'); 

		// Load library session
        $this->load->library('session'); 

		// Jika session tidak ditemukan, redirect ke login
		if (!$this->session->userdata('id_user')) {
			$this->session->set_flashdata('status1', 'expired');
			redirect('login');
		}
	}

	// Validasi file upload
	function file_upload()
	{
		$this->form_validation->set_message('file_upload', 'Silahkan pilih file untuk diupload.');
		if (empty($_FILES['fileupdate']['name'])) {
			return false;
		} else {
			return true;
		}
	}

	function file_upload2()
	{
		$this->form_validation->set_message('file_upload2', 'Silahkan pilih file untuk diupload.');
		if (empty($_FILES['filediagnosa']['name'])) {
			return false;
		} else {
			return true;
		}
	}

	// Cek tiket baru yang di-assign ke teknisi dalam 5 menit terakhir
	public function check_new_assignments() {
        $id_user = $this->session->userdata('id_user'); // Ambil ID teknisi
        
        // Ambil tiket terbaru dalam 5 menit terakhir
        $last_checked = date('Y-m-d H:i:s', strtotime('-5 minutes'));

        // Ambil tiket baru yang di-assign setelah waktu terakhir dicek
        $new_tickets = $this->Ticket_models->get_new_assigned_tickets($id_user, $last_checked);

        // Kirim response dalam bentuk JSON
        echo json_encode(['new_ticket' => !empty($new_tickets), 'tickets' => $new_tickets]);
    }


// edit
//Buat Ticket
public function buat()
{
	//User harus User, tidak boleh role user lain
	if ($this->session->userdata('level') == "Technician") {
		//Menyusun template Buat ticket
		$data['title'] 	  = "Buat Tiket";
		$data['navbar']   = "navbar";
		$data['sidebar']  = "sidebar";
		$data['body']     = "ticketTeknisi/buatticket";

		//Session
		$id_dept 	= $this->session->userdata('id_dept');
		$id_user 	= $this->session->userdata('id_user');

		//Get kode ticket yang akan digunakan sebagai id_ticket menggunakan model(getkodeticket)
		$data['ticket'] = $this->model->getkodeticket();

		//Mengambil semua data profile user yang sedang login menggunakan model (profile)
		$data['profile'] = $this->model->profile($id_user)->row_array();

		//Dropdown pilih kategori, menggunakan model (dropdown_kategori), nama kategori ditampung pada 'dd_kategori', data yang akan di simpan adalah id_kategori dan akan ditampung pada 'id_kategori'
		$data['dd_kategori'] = $this->model->dropdown_kategori();
		$data['id_kategori'] = "";

		//Dropdown pilih sub kategori, menggunakan model (dropdown_sub_kategori), nama kategori ditampung pada 'dd_sub_kategori', data yang akan di simpan adalah id_sub_kategori dan akan ditampung pada 'id_sub_kategori'
		$data['dd_sub_kategori'] = $this->model->dropdown_sub_kategori('');
		$data['id_sub_kategori'] = "";

		//Dropdown pilih lokasi, menggunakan model (dropdown_lokasi), nama kondisi ditampung pada 'dd_lokasi', data yang akan di simpan adalah id_lokasi dan akan ditampung pada 'id_lokasi'
		$data['dd_lokasi'] = $this->model->dropdown_lokasi();
		$data['id_lokasi'] = "";

		$data['error'] = "";

		//Load template
		$this->load->view('template', $data);
	} else {
		//Bagian ini jika role yang mengakses tidak sama dengan User
		//Akan dibawa ke Controller Errorpage
		redirect('Errorpage');
	}
}

public function submit()
{
	//Form validasi untuk ketgori dengan nama validasi = id_kategori
	$this->form_validation->set_rules(
		'id_kategori',
		'Id_kategori',
		'required',
		array(
			'required' => '<strong>Failed!</strong> Kategori Harus dipilih.'
		)
	);

	//Form validasi untuk sub kategori dengan nama validasi = id_sub_kategori
	$this->form_validation->set_rules(
		'id_sub_kategori',
		'id_sub_kategori',
		'required',
		array(
			'required' => '<strong>Failed!</strong> Sub Kategori Harus dipilih.'
		)
	);

	//Form validasi untuk lokasi dengan nama validasi = lokasi
	$this->form_validation->set_rules(
		'id_lokasi',
		'Id_lokasi',
		'required',
		array(
			'required' => '<strong>Failed!</strong> Lokasi Harus dipilih.'
		)
	);

	//Form validasi untuk subject dengan nama validasi = problem_summary
	$this->form_validation->set_rules(
		'problem_summary',
		'Problem_summary',
		'required',
		array(
			'required' => '<strong>Failed!</strong> Field Harus diisi.'
		)
	);

	//Form validasi untuk deskripsi dengan nama validasi = problem_detail
	$this->form_validation->set_rules(
		'problem_detail',
		'Problem_detail',
		'required',
		array(
			'required' => '<strong>Failed!</strong> Field Harus diisi.'
		)
	);

	//Form validasi untuk deskripsi dengan nama validasi = filefoto
	// $this->form_validation->set_rules(
	// 	'filefoto',
	// 	'File_foto',
	// 	'callback_file_upload'
	// );

	//Kondisi jika proses buat tiket tidak memenuhi syarat validasi akan dikembalikan ke form buat tiket
	if ($this->form_validation->run() == FALSE) {
		//User harus User, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template Buat ticket
			$data['title'] 	  = "Buat Tiket";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/buatticket";

			//Session
			$id_dept 	= $this->session->userdata('id_dept');
			$id_user 	= $this->session->userdata('id_user');

			//Get kode ticket yang akan digunakan sebagai id_ticket menggunakan model(getkodeticket)
			$data['ticket'] = $this->model->getkodeticket();

			//Mengambil semua data profile user yang sedang login menggunakan model (profile)
			$data['profile'] = $this->model->profile($id_user)->row_array();

			//Dropdown pilih kategori, menggunakan model (dropdown_kategori), nama kategori ditampung pada 'dd_kategori', data yang akan di simpan adalah id_kategori dan akan ditampung pada 'id_kategori'
			$data['dd_kategori'] = $this->model->dropdown_kategori();
			$data['id_kategori'] = "";

			//Dropdown pilih sub kategori, menggunakan model (dropdown_sub_kategori), nama kategori ditampung pada 'dd_sub_kategori', data yang akan di simpan adalah id_sub_kategori dan akan ditampung pada 'id_sub_kategori'
			$data['dd_sub_kategori'] = $this->model->dropdown_sub_kategori('');
			$data['id_sub_kategori'] = "";

			//Dropdown pilih lokasi, menggunakan model (dropdown_lokasi), nama kondisi ditampung pada 'dd_lokasi', data yang akan di simpan adalah id_lokasi dan akan ditampung pada 'id_lokasi'
			$data['dd_lokasi'] = $this->model->dropdown_lokasi();
			$data['id_lokasi'] = "";

			$data['error'] = "";

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan User
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	} else {
		//Bagian ini jika validasi dipenuhi untuk membuat ticket
		//Session
		$id_user 	= $this->session->userdata('id_user');

		//Get kode ticket yang akan digunakan sebagai id_ticket menggunakan model(getkodeticketnew)
		$ticket 	= $this->model->getkodeticketnew();
		$date       = date("Y-m-d  H:i:s");

		//Konfigurasi Upload Gambar
		$config['upload_path'] 		= './uploads/';		//Folder untuk menyimpan gambar
		$config['allowed_types'] 	= 'gif|jpg|jpeg|png|pdf';	//Tipe file yang diizinkan
		$config['max_size'] 		= '25600';			//Ukuran maksimum file gambar yang diizinkan
		$config['max_width']        = '0';				//Ukuran lebar maks. 0 menandakan ga ada batas
		$config['max_height']       = '0';				//Ukuran tinggi maks. 0 menandakan ga ada batas

		//Memanggil library upload pada codeigniter dan menyimpan konfirguasi
		$this->load->library('upload', $config);
		//Jika upload gambar tidak sesuai dengan konfigurasi di atas, maka upload gambar gagal, dan kembali ke halaman Create ticket
		if (!$this->upload->do_upload('filefoto')) {
			//$this->session->set_flashdata('status', 'Error');
			//redirect('ticket_user/buat');

			if ($_FILES['filefoto']['error'] != 4) {
				//Menyusun template Buat ticket
				$data['title'] 	  = "Buat Tiket";
				$data['navbar']   = "navbar";
				$data['sidebar']  = "sidebar";
				$data['body']     = "ticketTeknisi/buatticket";
				//Session
				$id_dept 	= $this->session->userdata('id_dept');
				$id_user 	= $this->session->userdata('id_user');

				//Get kode ticket yang akan digunakan sebagai id_ticket menggunakan model(getkodeticket)
				$data['ticket'] = $this->model->getkodeticket();

				//Mengambil semua data profile user yang sedang login menggunakan model (profile)
				$data['profile'] = $this->model->profile($id_user)->row_array();

				//Dropdown pilih kategori, menggunakan model (dropdown_kategori), nama kategori ditampung pada 'dd_kategori', data yang akan di simpan adalah id_kategori dan akan ditampung pada 'id_kategori'
				$data['dd_kategori'] = $this->model->dropdown_kategori();
				$data['id_kategori'] = "";

				//Dropdown pilih sub kategori, menggunakan model (dropdown_sub_kategori), nama kategori ditampung pada 'dd_sub_kategori', data yang akan di simpan adalah id_sub_kategori dan akan ditampung pada 'id_sub_kategori'
				$data['dd_sub_kategori'] = $this->model->dropdown_sub_kategori('');
				$data['id_sub_kategori'] = "";

				//Dropdown pilih lokasi, menggunakan model (dropdown_lokasi), nama kondisi ditampung pada 'dd_lokasi', data yang akan di simpan adalah id_lokasi dan akan ditampung pada 'id_lokasi'
				$data['dd_lokasi'] = $this->model->dropdown_lokasi();
				$data['id_lokasi'] = "";

				$data['error'] = $this->upload->display_errors();

				$this->load->view('template', $data);
			} else {
				$data = array(
					'id_ticket'			=> $ticket,
					'tanggal'			=> $date,
					'last_update'		=> date("Y-m-d H:i:s"),
					'reported'			=> $id_user,
					'id_sub_kategori' 	=> $this->input->post('id_sub_kategori'),
					'problem_summary'	=> ucfirst($this->input->post('problem_summary')),
					'problem_detail'	=> ucfirst($this->input->post('problem_detail')),
					'status'    		=> 1,
					'progress'			=> 0,
					'filefoto'			=> 'no-image.jpg',
					'id_lokasi'			=> $this->input->post('id_lokasi')
				);

				$kat      = $this->input->post('id_kategori');
				$subkat   = $this->input->post('id_sub_kategori');
				$row      = $this->model->getkategori($kat)->row();
				$key      = $this->db->query("SELECT * FROM kategori_sub WHERE id_sub_kategori = '$subkat'")->row();

				//Data tracking ditampung dalam bentuk array
				$datatracking = array(
					'id_ticket'  => $ticket,
					'tanggal'    => date("Y-m-d H:i:s"),
					'status'     => "Ticket Submited. Kategori: " . $row->nama_kategori . "(" . $key->nama_sub_kategori . ")",
					'deskripsi'  => ucfirst($this->input->post('problem_detail')),
					'id_user'    => $id_user
				);

				//Query insert data ticket yang ditampung ke dalam database. tersimpan ditabel ticket
				$this->db->insert('ticket', $data);
				//Query insert data tarcking yang ditampung ke dalam database. tersimpan ditabel tracking
				$this->db->insert('tracking', $datatracking);

				//Memanggil fungsi kirim email dari user ke admin
				$this->model->emailbuatticket2($ticket);

				//Set pemberitahuan bahwa data tiket berhasil dibuat
				$this->session->set_flashdata('status', 'Dikirim');

				//Dialihkan ke halaman my ticket
				redirect('dashboard');
			}
		} else {
			//Bagian ini jika file gambar sesuai dengan konfirgurasi di atas
			//Menampung file gambar ke variable 'gambar'
			$gambar = $this->upload->data();
			//Data ticket ditampung dalam bentuk array
			$data = array(
				'id_ticket'			=> $ticket,
				'tanggal'			=> $date,
				'last_update'		=> date("Y-m-d H:i:s"),
				'reported'			=> $id_user,
				'id_sub_kategori' 	=> $this->input->post('id_sub_kategori'),
				'problem_summary'	=> ucfirst($this->input->post('problem_summary')),
				'problem_detail'	=> ucfirst($this->input->post('problem_detail')),
				'status'    		=> 1,
				'progress'			=> 0,
				'filefoto'			=> $gambar['file_name'],
				'id_lokasi'			=> $this->input->post('id_lokasi')
			);

			$kat      = $this->input->post('id_kategori');
			$subkat   = $this->input->post('id_sub_kategori');
			$row      = $this->model->getkategori($kat)->row();
			$key      = $this->db->query("SELECT * FROM kategori_sub WHERE id_sub_kategori = '$subkat'")->row();

			//Data tracking ditampung dalam bentuk array
			$datatracking = array(
				'id_ticket'  => $ticket,
				'tanggal'    => date("Y-m-d H:i:s"),
				'status'     => "Ticket Submited. Kategori: " . $row->nama_kategori . "(" . $key->nama_sub_kategori . ")",
				'deskripsi'  => ucfirst($this->input->post('problem_detail')),
				'id_user'    => $id_user
			);

			//Query insert data ticket yang ditampung ke dalam database. tersimpan ditabel ticket
			$this->db->insert('ticket', $data);
			//Query insert data tarcking yang ditampung ke dalam database. tersimpan ditabel tracking
			$this->db->insert('tracking', $datatracking);

			//Memanggil fungsi kirim email dari user ke admin
			$this->model->emailbuatticket2($ticket);

			//Set pemberitahuan bahwa data tiket berhasil dibuat
			$this->session->set_flashdata('status', 'Dikirim');

			//Dialihkan ke halaman my ticket
			redirect('dashboard');
		}
	}
}


public function detail($id)
	{
		//User harus User, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template Detail Ticket
			$data['title'] 	  = "Detail Tiket";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/detail";

			//Session
			$id_dept 	= $this->session->userdata('id_dept');
			$id_user 	= $this->session->userdata('id_user');

			//Detail setiap tiket, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
			$data['detail'] = $this->model->detail_ticket($id)->row_array();

			//Tracking setiap tiket, get dari model (tracking_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'tracking'
			$data['tracking'] = $this->model->tracking_ticket($id)->result();

			//Message setiap tiket, get dari model (ticket_message) berdasarkan id_ticket, data akan ditampung dalam parameter 'message'
			$data['message'] = $this->model->message_ticket($id)->result();

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan User
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}


//---------------------//
	//My Ticket
	public function index()
	{
		//User harus User, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template My Ticket
			$data['title'] 	  = "Tiket Saya";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/listticket";

			//Session
			$id_dept 	= $this->session->userdata('id_dept');
			$id_user 	= $this->session->userdata('id_user');

			//Daftar semua ticket user, get dari model (myticket) berdasarkan id_user masing-masing, data akan ditampung dalam parameter 'ticket'
			$data['ticket'] = $this->model->myticket($id_user)->result();

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan User
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}







	// --------------------------------------------------end tambahan -------------------

	//Approval & Pending Ticket

	public function index_approve()
	{
		//User harus Teknisi, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template List Approval
			$data['title'] 	  = "Tiket Ditugaskan";
			$data['desc'] 	  =	"Daftar semua tiket yang Ditugaskan untuk Anda menunggu approval.";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/listapprove";

			//Session
			$id_dept 	= $this->session->userdata('id_dept');
			$id_user 	= $this->session->userdata('id_user');

			//Daftar semua ticket yang belum di-approval oleh teknisi, get dari model (approve_tugas) berdasarkan id_user teknisi, data akan ditampung dalam parameter 'approve'
			$data['approve'] = $this->model->approve_tugas($id_user)->result();

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function detail_approve($id)
	{
		//User harus Teknisi, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template Detail ticket
			$data['title']    = "Detail Tiket";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/detailapprove";

			//Session
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Detail setiap tiket yang belum di-approve, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
			$data['detail'] = $this->model->detail_ticket($id)->row_array();

			//Tracking setiap tiket, get dari model (tracking_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'tracking'
			$data['tracking'] = $this->model->tracking_ticket($id)->result();

			//Message setiap tiket, get dari model (ticket_message) berdasarkan id_ticket, data akan ditampung dalam parameter 'message'
			$data['message'] = $this->model->message_ticket($id)->result();

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function submitMessage($id)
	{
		//Form validasi untuk deskripsi dengan nama validasi = problem_detail
		$this->form_validation->set_rules(
			'message',
			'Message',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Field Harus diisi.'
			)
		);

		//Form validasi untuk deskripsi dengan nama validasi = problem_detail
		$this->form_validation->set_rules(
			'filefoto',
			'File_foto',
			''
		);

		//Kondisi jika proses buat tiket tidak memenuhi syarat validasi akan dikembalikan ke form buat tiket
		if ($this->form_validation->run() == FALSE) {
			//User harus User, tidak boleh role user lain
			if ($this->session->userdata('level') == "Technician") {
				//Menyusun template Buat ticket
				$data['title']    = "Detail Ticket";
				$data['navbar']   = "navbar";
				$data['sidebar']  = "sidebar";
				$data['body']     = "ticketTeknisi/detailupdate";

				//Session
				$id_dept     = $this->session->userdata('id_dept');
				$id_user     = $this->session->userdata('id_user');

				//Detail setiap tiket, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
				$data['detail'] = $this->model->detail_ticket($id)->row_array();

				//Tracking setiap tiket, get dari model (tracking_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'tracking'
				$data['tracking'] = $this->model->tracking_ticket($id)->result();

				//Message setiap tiket, get dari model (ticket_message) berdasarkan id_ticket, data akan ditampung dalam parameter 'message'
				$data['message'] = $this->model->message_ticket($id)->result();

				//Load template
				$this->load->view('template', $data);
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan User
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		} else {
			//Bagian ini jika validasi dipenuhi untuk membuat ticket
			//Session
			$id_user     = $this->session->userdata('id_user');

			//Tanggal
			$date       = date("Y-m-d H:i:s");

			//Konfigurasi Upload Gambar
			$config['upload_path']      = './uploads/';        //Folder untuk menyimpan gambar
			$config['allowed_types']    = 'gif|jpg|jpeg|png';    //Tipe file yang diizinkan
			$config['max_size']         = '25600';            //Ukuran maksimum file gambar yang diizinkan
			$config['max_width']        = '0';                //Ukuran lebar maks. 0 menandakan ga ada batas
			$config['max_height']       = '0';                //Ukuran tinggi maks. 0 menandakan ga ada batas

			//Memanggil library upload pada codeigniter dan menyimpan konfirguasi
			$this->load->library('upload', $config);

			if ($_FILES['filefoto']['name'] != "") {
				//Jika upload gambar tidak sesuai dengan konfigurasi di atas, maka upload gambar gagal, dan kembali ke halaman Create ticket
				if (!$this->upload->do_upload('filefoto')) {
					$this->session->set_flashdata('status', 'Error');
					redirect('ticket_teknisi/detail_update/' . $id);
				} else {
					//Bagian ini jika file gambar sesuai dengan konfirgurasi di atas
					//Menampung file gambar ke variable 'gambar'
					$gambar = $this->upload->data();

					//Data message ditampung dalam bentuk array
					$datamessage = array(
						'id_ticket'  => $id,
						'tanggal'    => $date,
						'status'     => 1,
						'message'    => htmlspecialchars($this->input->post('message')),
						'id_user'    => $id_user,
						'filefoto'   => $gambar['file_name'],
					);

					//Query insert data ticket_message yang ditampung ke dalam database. tersimpan ditabel ticket_message
					$this->db->insert('ticket_message', $datamessage);

					//Memanggil fungsi kirim email dari user ke admin
					$this->model->emailmessageticket($id);

					//Set pemberitahuan bahwa data tiket berhasil dibuat
					$this->session->set_flashdata('status', 'Success');
					//Dialihkan ke halaman my ticket
					redirect('ticket_teknisi/detail_update/' . $id);
				}
			} else {
				//Bagian ini jika file gambar sesuai dengan konfirgurasi di atas
				//Menampung file gambar ke variable 'gambar'
				$gambar = $this->upload->data();

				//Data message ditampung dalam bentuk array
				$datamessage = array(
					'id_ticket'  => $id,
					'tanggal'    => $date,
					'status'     => 1,
					'message'    => htmlspecialchars($this->input->post('message')),
					'id_user'    => $id_user,
				);

				//Query insert data ticket_message yang ditampung ke dalam database. tersimpan ditabel ticket_message
				$this->db->insert('ticket_message', $datamessage);

				//Memanggil fungsi kirim email dari user ke admin
				$this->model->emailmessageticket($id);

				//Set pemberitahuan bahwa data tiket berhasil dibuat
				$this->session->set_flashdata('status', 'Success');
				//Dialihkan ke halaman my ticket
				redirect('ticket_teknisi/detail_update/' . $id);
			}
		}
	}

	public function approve($id)
	{
		//User harus teknisi, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Proses me-approve ticket, menggunakan model (approve_tiket) dengan parameter id_ticket yang akan di-approve
			$this->model->approve_tiket($id);

			$this->model->emaildiproses($id);
			//Set pemberitahuan bahwa ticket berhasil di-approve
			$this->session->set_flashdata('status', 'Process');
			//Kembali ke halaman List approval ticket (Ticket Assigned)
			redirect('ticket_teknisi/index_approve');
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function pending($id)
	{
		//User harus teknisi, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Proses pending ticket, menggunakan model (pending_tugas) dengan parameter id_ticket yang akan di-pending
			$this->model->pending_tugas($id);
			$this->model->emaildipending($id);
			//Set pemberitahuan bahwa ticket berhasil di-pending
			$this->session->set_flashdata('status', 'Hold');
			//Kembali ke halaman List approval ticket (Ticket Assigned)
			redirect('ticket_teknisi/index_approve');
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	//List Assignment

	public function index_tugas()
	{
		//User harus Teknisi, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template List Assignment
			$data['title'] 	  = "Daftar Tugas";
			$data['desc'] = "Daftar semua tiket yang Ditugaskan untuk Anda.";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/listtugas";

			//Session
			$id_dept 	= $this->session->userdata('id_dept');
			$id_user 	= $this->session->userdata('id_user');

			//Daftar semua ticket yang ditugaskan kepada teknisi, get dari model (daftar_tugas) berdasarkan id_user teknisi, data akan ditampung dalam parameter 'tugas'
			$data['tugas'] = $this->model->daftar_tugas($id_user)->result();

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function detail_update($id)
	{
		//User harus Teknisi, tidak boleh role user lain
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template Detail ticket
			$data['title']    = "Update Progress";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/detailupdate";

			//Session
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Detail setiap tiket yang dikerjakan, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
			$data['detail'] = $this->model->detail_ticket($id)->row_array();

			//Tracking setiap tiket, get dari model (tracking_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'tracking'
			$data['tracking'] = $this->model->tracking_ticket($id)->result();

			//Message setiap tiket, get dari model (ticket_message) berdasarkan id_ticket, data akan ditampung dalam parameter 'message'
			$data['message'] = $this->model->message_ticket($id)->result();

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function update_progress($id)
	{
		//Form validasi untuk deskripsi dengan nama validasi = desk
		$this->form_validation->set_rules(
			'desk',
			'Desk',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Deskripsikan Progress Pekerjaan Anda.'
			)
		);

		$this->form_validation->set_rules(
			'progress',
			'Progress',
			'required|greater_than[0]',
			array(
				'required' => '<strong>Failed!</strong> Progress harus dipilih.'
			)
		);

		$this->form_validation->set_rules(
			'signed',
			'Signature',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Tanda Tangan harus dibuat.'
			)
		);

		//Form validasi untuk deskripsi dengan nama validasi = fileupdate
		$this->form_validation->set_rules(
			'fileupdate',
			'File_update',
			'callback_file_upload'
		);

		//Kondisi jika saat proses update tidak memenuhi syarat validasi akan dikembalikan ke halaman update progress
		if ($this->form_validation->run() == FALSE) {
			//User harus Teknisi, tidak boleh role user lain
			if ($this->session->userdata('level') == "Technician") {
				//Menyusun template Detail ticket
				$data['title']    = "Update Progress";
				$data['navbar']   = "navbar";
				$data['sidebar']  = "sidebar";
				$data['body']     = "ticketTeknisi/detailupdate";

				//Session
				$id_dept = $this->session->userdata('id_dept');
				$id_user = $this->session->userdata('id_user');

				//Detail setiap tiket yang dikerjakan, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
				$data['detail'] = $this->model->detail_ticket($id)->row_array();

				//Tracking setiap tiket, get dari model (tracking_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'tracking'
				$data['tracking'] = $this->model->tracking_ticket($id)->result();

				//Load template
				$this->load->view('template', $data);
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		} else {
			//Bagian ini jika validasi terpenuhi
			//User harus Teknisi, tidak boleh role user lain
			if ($this->session->userdata('level') == "Technician") {
				//Proses update ticket, menggunakan model (update) dengan parameter id_ticket yang akan di-update
				$this->model->update_progress($id);

				$this->model->emailselesai($id);
				//Set pemberitahuan bahwa ticket berhasil di-update
				$this->session->set_flashdata('status', 'Diperbarui');
				//Kembali ke halaman List ticket (Assignment Ticket)
				redirect('ticket_teknisi/index_tugas');
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		}
	}

	public function change_category($id)
	{
		if ($this->session->userdata('level') == "Technician") {
			//Menyusun template Detail ticket
			$data['title']    = "Ganti Kategori";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "ticketTeknisi/change";

			//Session
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Detail setiap tiket yang dikerjakan, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
			$data['detail'] = $this->model->detail_ticket($id)->row_array();

			$row = $this->model->detail_ticket($id)->row();

			//Tracking setiap tiket, get dari model (tracking_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'tracking'
			$data['tracking'] = $this->model->tracking_ticket($id)->result();

			//Dropdown pilih kategori, menggunakan model (dropdown_kategori), nama kategori ditampung pada 'dd_kategori', data yang akan di simpan adalah id_kategori dan akan ditampung pada 'id_kategori'
			$data['dd_kategori'] = $this->model->dropdown_kategori();
			$data['id_kategori'] = $row->id_kategori;

			//Dropdown pilih sub kategori, menggunakan model (dropdown_sub_kategori), nama kategori ditampung pada 'dd_sub_kategori', data yang akan di simpan adalah id_sub_kategori dan akan ditampung pada 'id_sub_kategori'
			$data['dd_sub_kategori'] = $this->model->dropdown_sub_kategori('');
			$data['id_sub_kategori'] = '';

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function change($id)
	{
		//Form validasi untuk ketgori dengan nama validasi = id_kategori
		$this->form_validation->set_rules(
			'id_kategori',
			'Id_kategori',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Kategori Harus dipilih.'
			)
		);

		//Form validasi untuk sub kategori dengan nama validasi = id_sub_kategori
		$this->form_validation->set_rules(
			'id_sub_kategori',
			'id_sub_kategori',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Sub Kategori Harus dipilih.'
			)
		);

		//Form validasi untuk diagnosa dengan nama validasi = diagnos
		$this->form_validation->set_rules(
			'diagnos',
			'diagnos',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Diagnosa Harus diisi.'
			)
		);

		//Form validasi untuk deskripsi dengan nama validasi = filediagnosa
		$this->form_validation->set_rules(
			'filediagnosa',
			'File_diagnosa',
			'callback_file_upload2'
		);

		//Kondisi jika proses buat tiket tidak memenuhi syarat validasi akan dikembalikan ke form ganti kategori
		if ($this->form_validation->run() == FALSE) {
			if ($this->session->userdata('level') == "Technician") {
				//Menyusun template Detail ticket
				$data['title']    = "Ganti Kategori";
				$data['navbar']   = "navbar";
				$data['sidebar']  = "sidebar";
				$data['body']     = "ticketTeknisi/change";

				//Session
				$id_dept = $this->session->userdata('id_dept');
				$id_user = $this->session->userdata('id_user');

				//Detail setiap tiket yang dikerjakan, get dari model (detail_ticket) berdasarkan id_ticket, data akan ditampung dalam parameter 'detail'
				$data['detail'] = $this->model->detail_ticket($id)->row_array();

				$row = $this->model->detail_ticket($id)->row();

				//Dropdown pilih kategori, menggunakan model (dropdown_kategori), nama kategori ditampung pada 'dd_kategori', data yang akan di simpan adalah id_kategori dan akan ditampung pada 'id_kategori'
				$data['dd_kategori'] = $this->model->dropdown_kategori();
				$data['id_kategori'] = $row->id_kategori;

				//Dropdown pilih sub kategori, menggunakan model (dropdown_sub_kategori), nama kategori ditampung pada 'dd_sub_kategori', data yang akan di simpan adalah id_sub_kategori dan akan ditampung pada 'id_sub_kategori'
				$data['dd_sub_kategori'] = $this->model->dropdown_sub_kategori($row->id_kategori);
				$data['id_sub_kategori'] = $row->id_sub_kategori;

				//Load template
				$this->load->view('template', $data);
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		} else {
			if ($this->session->userdata('level') == "Technician") {
				$this->model->changekategori($id);
				$this->model->emailubah($id);

				$this->session->set_flashdata('status', 'Diganti');
				//Kembali ke halaman List ticket (Assignment Ticket)
				redirect('ticket_teknisi/index_tugas');
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan Teknisi
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		}
	}
}

