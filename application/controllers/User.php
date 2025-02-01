<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//Meload model
		$this->load->model('Main_model', 'model');
		//Jika session tidak ditemukan
		if (!$this->session->userdata('id_user')) {
			//Kembali ke halaman Login
			$this->session->set_flashdata('status1', 'expired');
			redirect('login');
		}
	}

	public function index()
	{
		//User harus admin, tidak boleh role user lain
		if ($this->session->userdata('level') == "Admin") {
			//Menyusun template List user
			$data['title'] 	  = "User";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "user/index";

			//Session
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Daftar semua user, get dari model (user), data akan ditampung dalam parameter 'user'
			$data['user'] = $this->model->user()->result();

			//Dropdown pilih pegawai, menggunakan model (dropdown_pegawai), nama pegawai ditampung pada 'dd_pegawai', data yang akan di simpan adalah id_pegawai dan akan ditampung pada 'id_pegawai'
			$data['dd_pegawai'] = $this->model->dropdown_pegawai();
			$data['id_pegawai'] = "";

			//Dropdown pilih level, menggunakan model (dropdown_level), nama level ditampung pada 'dd_level', data yang akan di simpan adalah id_level dan akan ditampung pada 'id_level'
			$data['dd_level'] = $this->model->dropdown_level();
			$data['id_level'] = "";

			$data['nama'] = "";
			$data['email'] = "";

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan admin
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function tambah()
	{
		$nama 	= $this->input->post('nama');
		$email 	= $this->input->post('email');

		//Form validasi untuk id_pegawai dengan nama validasi = id_pegawai
		$this->form_validation->set_rules(
			'id_pegawai',
			'Id_pegawai',
			'required|is_unique[user.username]',
			array(
				'required' =>  '<strong>Failed!</strong> Field Harus diisi.',
				'is_unique' => '<strong>Error!</strong> Pegawai sudah memiliki User Akun.'
			)
		);

		//Form validasi untuk id_level dengan nama validasi = id_level
		$this->form_validation->set_rules(
			'id_level',
			'Id_level',
			'required',
			array(
				'required' =>  '<strong>Failed!</strong> Level Harus dipilih.'
			)
		);

		//Kondisi jika proses tambah user tidak memenuhi syarat validasi akan dikembalikan ke form tambah user
		if ($this->form_validation->run() == FALSE) {
			//User harus admin, tidak boleh role user lain
			if ($this->session->userdata('level') == "Admin") {
				//Menyusun template List user
				$data['title'] 	  	= "User";
				$data['navbar']   	= "navbar";
				$data['sidebar']  	= "sidebar";
				$data['modal_show'] = "$('#modal-fade').modal('show');";
				$data['body']     	= "user/index";

				//Session
				$id_dept = $this->session->userdata('id_dept');
				$id_user = $this->session->userdata('id_user');

				//Daftar semua user, get dari model (user), data akan ditampung dalam parameter 'user'
				$data['user'] = $this->model->user()->result();

				//Dropdown pilih pegawai, menggunakan model (dropdown_pegawai), nama pegawai ditampung pada 'dd_pegawai', data yang akan di simpan adalah id_pegawai dan akan ditampung pada 'id_pegawai'
				$data['dd_pegawai'] = $this->model->dropdown_pegawai();
				$data['id_pegawai'] = "";

				//Dropdown pilih level, menggunakan model (dropdown_level), nama level ditampung pada 'dd_level', data yang akan di simpan adalah id_level dan akan ditampung pada 'id_level'
				$data['dd_level'] = $this->model->dropdown_level();
				$data['id_level'] = "";

				$data['nama'] = "";
				$data['email'] = "";

				//Load template
				$this->load->view('template', $data);
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan admin
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		} else {
			//Bagian ini jika validasi dipenuhi, maka berhasil menambah user
			//Get kode user yang akan digunakan sebagai id_user menggunakan model(getkodeuser)
			//$getkodeuser 	= $this->model->getkodeuser();

			//Password default
			$password = '12345678';

			//Data user ditampung dalam bentuk array
			$data = array(
				//'id_user' 	=> $getkodeuser,
				//username diinput menggunakan id_pegawai yang dipilih
				'username'  => $this->input->post('id_pegawai'),
				//password diinput menggunakan id_pegawai yang dipilih
				'password'  => md5($password),
				'level'  	=> $this->input->post('id_level')
			);

			$isiEmail   = "<div>Dear " . $nama . "</div>";
			$isiEmail  .= "<div>Your Helpdesk Account Has Been Created!</div>";
			$isiEmail  .= "<div>Please enter your ID Number as username and password for the first login</div>";
			$isiEmail  .= "<div>You can change the password after login in <code>Profile -> Change Password</code></div>";
			$isiEmail  .= "<div>Thank You</code></div>";

			$config = array(
				'protocol'  => $this->settings->info['protocol'], // 'mail', 'sendmail', or 'smtp'
				'smtp_host' => $this->settings->info['smtp_host'], // 'mail.domain.com'
				'smtp_port' => $this->settings->info['smtp_port'], // '465', '587'
				'smtp_user' => $this->settings->info['smtp_user'], // 'your@email.com
				'smtp_pass' => $this->settings->info['smtp_pass'], // 'password'
				'mailtype'  => 'html', //plaintext 'text' mails or 'html'
				'starttls'  => true,
				'newline'   => "\r\n",
				'smtp_timeout' => '4', //in seconds
				'charset' => 'iso-8859-1',
				'wordwrap' => TRUE,
			);

			$from = $this->settings->info['smtp_user'];
			$name = $this->settings->info['perusahaan'];
			$to = $email;

			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");
			$this->email->from($from, $name);
			$this->email->to($to);
			$this->email->subject('Your Helpdesk Account Has Been Created!');
			$this->email->message($isiEmail);

			//Query insert data yang ditampung ke dalam database. tersimpan ditabel user
			$this->db->insert('user', $data);

			if (!$this->email->send()) {
				$this->session->set_flashdata('status1', 'Tidak Dapat Mengirim Email Karena Email tidak Ditemukan. Data Akun Pengguna Berhasil Ditambah');
				redirect('user');
			} else {
				echo 'Berhasil mengirim email';
			}

			//Set pemberitahuan bahwa data user berhasil ditambahkan
			$this->session->set_flashdata('status', 'Ditambahkan');
			//Kembali ke halaman user (index)
			redirect('user');
		}
	}

	public function hapus($id)
	{
		//Get session id_user admin yang sedang login dan ditampung ke dalam variabel $user
		$user = $this->session->userdata('id');

		//Jika $user (id_user admin) tidak sama dengan $id yang dipilih, maka session admin tidak dihancurkan dan akan kembali ke halaman List user
		if ($user != $id) {
			//Query menghapus data pada tabel user berdasarkan id_user yang dipilih
			$this->db->where('id_user', $id);
			$this->db->delete('user');
			//Set pemberitahuan bahwa data user berhasil dihapus
			$this->session->set_flashdata('status', 'Dihapus');
			//Kembali ke halaman user (index)
			redirect('User');
		} else {
			//Bagian ini jika admin menghapus akunnya sendiri, maka session admin akan dihancurkan
			//Query menghapus data pada tabel user berdasarkan id_user yang dipilih
			$this->db->where('id_user', $id);
			$this->db->delete('user');
			//Menghancurkan session admin
			$this->session->sess_destroy();
			//Kembali ke halaman login (index)
			redirect('Login');
		}
	}

	public function edit($id)
	{
		//User harus admin, tidak boleh role user lain
		if ($this->session->userdata('level') == "Admin") {
			//Menyusun template Edit user
			$data['title'] 	  = "Edit User";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "user/edit";

			//Session
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Query untuk mengambil data user yang akan diedit, query ditampung dalam variabel '$row' untuk memanggil setiap data pada 1 user
			$row = $this->db->query("SELECT * FROM user WHERE id_user = '$id'")->row();

			//Mengambil data id_user yang sesuai dengan $id yang dipilih dan ditampung pada variabel $data dengan nama = id_user
			$data['id_user'] = $id;
			//Mengambil data username melalui query $row dan ditampung pada variabel $data dengan nama = username
			$data['username'] = $row->username;
			//Dropdown pilih level, menggunakan model (dropdown_level), nama level ditampung pada 'dd_level', dan mengambil level melalui $row dan ditampung pada variabel $data dengan nama = id_level
			$data['dd_level'] = $this->model->dropdown_level();
			$data['id_level'] = $row->level;

			//Load template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan admin
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}

	public function update($id)
	{
		//Form validasi untuk id_level dengan nama validasi = id_level
		$this->form_validation->set_rules(
			'id_level',
			'Id_level',
			'required',
			array(
				'required' => '<strong>Failed!</strong> Level Harus dipilih.'
			)
		);

		//Kondisi jika proses edit user tidak memenuhi syarat validasi akan dikembalikan ke halaman edit user
		if ($this->form_validation->run() == FALSE) {
			//User harus admin, tidak boleh role user lain
			if ($this->session->userdata('level') == "Admin") {
				//Menyusun template Edit user
				$data['title'] 	  = "Edit User";
				$data['navbar']   = "navbar";
				$data['sidebar']  = "sidebar";
				$data['body']     = "user/edit";

				//Session
				$id_dept = $this->session->userdata('id_dept');
				$id_user = $this->session->userdata('id_user');

				//Query untuk mengambil data user yang akan diedit, query ditampung dalam variabel '$row' untuk memanggil setiap data pada 1 user
				$row = $this->db->query("SELECT * FROM user WHERE id_user = '$id'")->row();

				//Mengambil data id_user yang sesuai dengan $id yang dipilih dan ditampung pada variabel $data dengan nama = id_user
				$data['id_user'] = $id;
				//Mengambil data username melalui query $row dan ditampung pada variabel $data dengan nama = username
				$data['username'] = $row->username;
				//Dropdown pilih level, menggunakan model (dropdown_level), nama level ditampung pada 'dd_level', dan mengambil level melalui $row dan ditampung pada variabel $data dengan nama = id_level
				$data['dd_level'] = $this->model->dropdown_level();
				$data['id_level'] = $row->level;

				//Load template
				$this->load->view('template', $data);
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan admin
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		} else {
			//Bagian ini jika validasi dipenuhi, maka berhasil edit user
			//User harus admin, tidak boleh role user lain
			if ($this->session->userdata('level') == "Admin") {
				//Get session id_user admin yang sedang login dan ditampung ke dalam variabel $user
				$user = $this->session->userdata('id');

				//Jika $user (id_user admin) tidak sama dengan $id yang dipilih, maka session admin tidak dihancurkan dan akan kembali ke halaman List user
				if ($user != $id) {
					//Data user ditampung dalam bentuk array
					$data = array(
						'level' => $this->input->post('id_level')
					);
					//Query update data yang ditampung ke dalam database. tersimpan ditabel user
					$this->db->where('id_user', $id);
					$this->db->update('user', $data);

					//Set pemberitahuan bahwa data user berhasil diupdate
					$this->session->set_flashdata('status', 'Diperbarui');
					//Kembali ke halaman user (index)
					redirect('user');
				} else {
					//Bagian ini jika admin melakukan edit data terhadap data user miliknya sendiri, maka setelah diedit, session admin akan dihancurkan
					//Data user ditampung dalam bentuk array
					$data = array(
						'level' => $this->input->post('id_level')
					);
					//Query update data yang ditampung ke dalam database. tersimpan ditabel user
					$this->db->where('id_user', $id);
					$this->db->update('user', $data);
					//Menghancurkan session admin
					$this->session->sess_destroy();
					//Kembali ke halaman login (index)
					redirect('login');
				}
			} else {
				//Bagian ini jika role yang mengakses tidak sama dengan admin
				//Akan dibawa ke Controller Errorpage
				redirect('Errorpage');
			}
		}
	}

	//Bagian User Profile, dan Ubah Password
	public function profile()
	{
		//Menyusun template profile tiap user
		$data['title'] 	  = "User Profile";
		$data['navbar']   = "navbar";
		$data['sidebar']  = "sidebar";
		$data['body']     = "user/profile";

		//Session
		$id_dept = $this->session->userdata('id_dept');
		$id_user = $this->session->userdata('id_user');

		//Mengambil semua data profile user yang sedang login menggunakan model (profile)
		$data['profile'] = $this->model->profile($id_user)->row_array();

		//Load template
		$this->load->view('template', $data);
	}

	public function password()
	{
		//Menyusun template Edit Password
		$data['title'] 	  = "Ganti Password";
		$data['navbar']   = "navbar";
		$data['sidebar']  = "sidebar";
		$data['body']     = "user/password";

		//Session
		$id_dept = $this->session->userdata('id_dept');
		$id_user = $this->session->userdata('id_user');

		//Load Template
		$this->load->view('template', $data);
	}

	public function updatepass()
	{
		//Form validasi untuk password lama dengan nama validasi = password_lama
		$this->form_validation->set_rules(
			'password_lama',
			'Password_lama',
			'required',
			array(
				'required' =>  '<strong>Failed!</strong> Please Enter Your Old Password.'
			)
		);

		//Form validasi untuk password baru dengan nama validasi = password
		$this->form_validation->set_rules(
			'password',
			'Password',
			'required',
			array(
				'required' =>  '<strong>Failed!</strong> Please Enter Your New Password.'
			)
		);

		//Form validasi untuk konfirmasi password dengan nama validasi = password2
		$this->form_validation->set_rules(
			'password2',
			'Password2',
			'required',
			array(
				'required' =>  '<strong>Failed!</strong> Please Retype Your New Password.'
			)
		);

		//Kondisi jika proses ubah password tidak memenuhi syarat validasi akan dikembalikan ke halaman ubah password
		if ($this->form_validation->run() == FALSE) {
			//Menyusun template Edit Password
			$data['title'] 	  = "Ganti Password";
			$data['navbar']   = "navbar";
			$data['sidebar']  = "sidebar";
			$data['body']     = "user/password";

			//Session
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Load Template
			$this->load->view('template', $data);
		} else {
			//Bagian ini jika validasi dipenuhi
			//Session
			$id 	 = $this->session->userdata('id');
			$id_dept = $this->session->userdata('id_dept');
			$id_user = $this->session->userdata('id_user');

			//Query untuk mengambil password user yang akan diubah, query ditampung dalam variabel '$passlama'
			$passlama = $this->db->query("SELECT * FROM user WHERE username = '$id_user' ")->row_array();

			//Input password sekarang
			$password 	= md5($this->input->post('password_lama'));
			//Input password baru
			$pass_baru  = md5($this->input->post('password'));
			//Input konfirmasi password
			$pass_conf  = md5($this->input->post('password2'));

			//Jika password dalam databse tidak sama dengan password yang diinput, maka password gagal diubah
			if ($passlama['password'] != $password) {
				//Set pemberitahuan bahwa data user gagal diupdate
				$this->session->set_flashdata('status1', 'Password lama salah!');
				//Kembali ke halaman ubah password
				redirect('user/password');
			} else {
				//Bagian ini jika password dalam database sesuai dengan password yang diinput
				//akan dilakukan pengecekan lagi, jika password baru yang diinput sesuai dengan konfirmasi password yang diinput, maka passowrd berhasil diubah
				if ($pass_baru == $pass_conf) {
					//Data user ditampung dalam bentuk array
					$data = array(
						'password' => $pass_baru
					);
					//Query update data yang ditampung ke dalam database. tersimpan ditabel user
					$this->db->where('id_user', $id);
					$this->db->update('user', $data);
					//Set pemberitahuan bahwa data user berhasil diupdate
					$this->session->set_flashdata('status', 'Diperbarui');
					//Kembali ke halaman ubah password
					redirect('user/password');
				} else {
					//Set pemberitahuan bahwa data user gagal diupdate
					$this->session->set_flashdata('status1', 'New Password not Match With Confirm Password');
					//Kembali ke halaman ubah password
					redirect('user/password');
				}
			}
		}
	}

	public function resetpassword($id)
	{
		//User harus admin, tidak boleh role user lain
		if ($this->session->userdata('level') == "Admin") {
				//Password default
				$password = '12345678';

				//Data user ditampung dalam bentuk array
				$data = array(
					'password' => md5($password)
				);
				
				//Query update data yang ditampung ke dalam database. tersimpan ditabel user
				$this->db->where('id_user', $id);
				$this->db->update('user', $data);

				//Set pemberitahuan bahwa data user berhasil diupdate
				$this->session->set_flashdata('status', 'Diperbarui');
				//Kembali ke halaman user (index)
				redirect('user');
		} else {
			//Bagian ini jika role yang mengakses tidak sama dengan admin
			//Akan dibawa ke Controller Errorpage
			redirect('Errorpage');
		}
	}
}
