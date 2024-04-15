<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Books extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedIn();
		$this->load->model('books_model');
	}

	public function index()
	{
		$data['ebooks'] = $this->books_model->ebooksListing();
		$this->load->template('ebooks/listing', $data); // this will load the view file
	}

	public function newEbook()
	{
		$this->load->template('ebooks/new', []); // this will load the view file
	}

	public function editEbook($id = 0)
	{
		$data['ebook'] = $this->books_model->getEbookInfo($id);
		if (count((array)$data['ebook']) == 0) {
			redirect('books');
		}
		$this->load->template('ebooks/edit', $data); // this will load the view file
	}

	function savenewebook()
	{
		//var_dump($_FILES); die;
		$this->load->library('session');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Ebook Title', 'trim|required|xss_clean');

		if ($this->form_validation->run() == FALSE) {
			redirect('newEbook');
		}

		$title = $this->input->post('title');
		$description = $this->input->post('description');
		$author = $this->input->post('author');
		$amount = $this->input->post('amount');
		$info = array(
			'title' => $title,
			'description' => $description,
			'author' => $author,
			'amount' => $amount,
		);

		if (!empty($_FILES['thumbnail']['name'])) {
			$upload = $this->upload_thumbnail();
			if ($upload[0] == 'ok') {
				$info['thumbnail'] =  $upload[1];
			} else {
				$this->session->set_flashdata('error', $upload[1]);
				redirect('newEbook');
			}
		} else {
			$this->session->set_flashdata('error', "You need to select an image thumbnail to upload");
			redirect('newEbook');
		}

		$this->books_model->addNewEbook($info);
		if ($this->books_model->status == "ok") {
			$this->session->set_flashdata('success', $this->books_model->message);
		} else {
			$this->session->set_flashdata('error', $this->books_model->message);
		}
		redirect('newEbook');
	}


	function editEbookData()
	{
		//var_dump($_FILES); die;
		$this->load->library('session');
		$this->load->library('form_validation');
		$id = $this->input->post('id');
		$this->form_validation->set_rules('title', 'Ebook Title', 'trim|required|max_length[128]|xss_clean');

		if ($this->form_validation->run() == FALSE) {
			redirect('editEbook/' . $id);
		} else {

			$title = $this->input->post('title');
			$description = $this->input->post('description');
			$author = $this->input->post('author');
			$amount = $this->input->post('amount');
			$info = array(
				'title' => $title,
				'description' => $description,
				'author' => $author,
				'amount' => $amount,
			);

			if (!empty($_FILES['thumbnail']['name'])) {
				$upload = $this->upload_thumbnail();
				if ($upload[0] == 'ok') {
					$info['thumbnail'] =  $upload[1];
				} else {
					$this->session->set_flashdata('error', $upload[1]);
					redirect('editEbook/' . $id);
				}
			}

			$this->books_model->editEbook($info, $id);
			if ($this->books_model->status == "ok") {
				$this->session->set_flashdata('success', $this->books_model->message);
			} else {
				$this->session->set_flashdata('error', $this->books_model->message);
			}
			redirect('editEbook/' . $id);
		}
	}


	function deleteEbook($id = 0)
	{
		$this->load->library('session');
		$this->books_model->deleteEbook($id);
		if ($this->books_model->status == "ok") {
			$this->session->set_flashdata('success', $this->books_model->message);
		} else {
			$this->session->set_flashdata('error', $this->books_model->message);
		}
		redirect('ebooksListing');
	}



	public function upload_thumbnail()
	{
		$path = $_FILES['thumbnail']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$new_name = time() . "." . $ext;

		$config['file_name'] = $new_name;
		$config['upload_path']          = './uploads/thumbnails';
		$config['max_size']             = 10000;
		$config['allowed_types']        = 'jpg|png|jpeg|PNG';
		$config['overwrite'] = TRUE; //overwrite thumbnail


		//var_dump($config);

		$this->load->library('upload');
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('thumbnail')) {
			//$error = array('error' => $this->upload->display_errors());
			return ['error', strip_tags($this->upload->display_errors())];
		} else {
			$image_data = $this->upload->data();
			return ['ok', $new_name];
		}
	}

	public function upload_thumbnail2()
	{
		$path = $_FILES['thumbnail2']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$new_name = time() . "." . $ext;

		$config['file_name'] = $new_name;
		$config['upload_path']          = './uploads/thumbnails';
		$config['max_size']             = 10000;
		$config['allowed_types']        = 'jpg|png|jpeg|PNG';
		$config['overwrite'] = TRUE; //overwrite thumbnail


		//var_dump($config);

		$this->load->library('upload');
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('thumbnail2')) {
			//$error = array('error' => $this->upload->display_errors());
			return ['error', strip_tags($this->upload->display_errors())];
		} else {
			$image_data = $this->upload->data();
			return ['ok', $new_name];
		}
	}

	public function upload_pdf()
	{
		$path = $_FILES['pdf']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$new_name = time() . "." . $ext;

		$config['file_name'] = $new_name;
		$config['upload_path']          = './uploads/books';
		$config['max_size']             = 1000000;
		$config['allowed_types']        = 'pdf|PDF|epub|EPUB';
		$config['overwrite'] = TRUE; //overwrite thumbnail
		$this->load->library('upload');
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('pdf')) {
			//$error = array('error' => $this->upload->display_errors());
			return ['error', strip_tags($this->upload->display_errors())];
		} else {
			$image_data = $this->upload->data();
			return ['ok', $new_name];
		}
	}

	public function upload_epub()
	{
		$path = $_FILES['epub']['name'];
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$new_name = time() . "." . $ext;

		$config['file_name'] = $new_name;
		$config['upload_path']          = './uploads/books';
		$config['max_size']             = 1000000;
		$config['allowed_types']        = '*';
		$config['overwrite'] = TRUE; //overwrite thumbnail
		$this->load->library('upload');
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('epub')) {
			//$error = array('error' => $this->upload->display_errors());
			return ['error', strip_tags($this->upload->display_errors())];
		} else {
			$image_data = $this->upload->data();
			return ['ok', $new_name];
		}
	}

	public function bookpurchases()
	{
		$this->load->template('ebooks/purchases', []); // this will load the view file
	}

	function getBookPurchases()
	{
		// Datatables Variables

		$draw = intval($_POST['draw']);
		$start = intval($_POST['start']);
		$length = intval($_POST['length']);
		$columnIndex = $_POST['order'][0]['column']; // Column index
		$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
		$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
		$searchValue = "";
		if (isset($_POST['search']['value'])) {
			$searchValue = $_POST['search']['value']; // Search value
		}

		$columnName = "";
		if (isset($_POST['columns'][$columnIndex]['data'])) {
			$columnSortOrder = $_POST['columns'][$columnIndex]['data']; // Search value
		}

		$columnSortOrder = "ASC";
		if (isset($_POST['order'][0]['dir'])) {
			$columnSortOrder = $_POST['order'][0]['dir']; // Search value
		}

		$date1 = isset($_GET['date']) ? filter_var($_GET['date'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH) : 0;
		$date2 = isset($_GET['date2']) ? filter_var($_GET['date2'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH) : 0;


		$users = $this->books_model->purchasesListing(/*$year1,$month1,$day1,$year2,$month2,$day2*/$date1, $date2, $columnName, $columnSortOrder, $searchValue, $start, $length);
		$total_users = $this->books_model->get_total_purchases($date1, $date2, $searchValue);
		//var_dump($users); die;
		$dat = array();

		$count = $start + 1;
		foreach ($users as $r) {
			$dat[] = array(
				$count,
				$r->booktitle,
				$r->email,
				$r->name,
				$r->reference,
				$r->shipping,
				$r->amount,
				$r->date
			);
			$count++;
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $total_users,
			"recordsFiltered" => $total_users,
			"data" => $dat
		);
		echo json_encode($output);
	}
}
