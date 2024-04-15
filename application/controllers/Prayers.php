<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Prayers extends BaseController {

	public function __construct(){
        parent::__construct();
				$this->isLoggedIn();
				$this->load->model('prayers_model');
    }

		//rss links methods
		public function prayersListing(){
        $this->load->template('prayers/listing', []); // this will load the view file
    }

		function getPrayers(){
      // Datatables Variables

        $draw = intval($_POST['draw']);
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
				$columnIndex = $_POST['order'][0]['column']; // Column index
				$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
				$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
				$searchValue="";
				if(isset($_POST['search']['value'])){
					$searchValue = $_POST['search']['value']; // Search value
				}

				$columnName="";
				if(isset($_POST['columns'][$columnIndex]['data'])){
					$columnSortOrder = $_POST['columns'][$columnIndex]['data']; // Search value
				}

        $columnSortOrder = "ASC";
				if(isset($_POST['order'][0]['dir'])){
					$columnSortOrder = $_POST['order'][0]['dir']; // Search value
				}


        $feeds = $this->prayers_model->adminPrayersListing($columnName,$columnSortOrder,$searchValue,$start, $length);
				$total_feeds = $this->prayers_model->get_total_prayers($searchValue);
        //var_dump($feeds); die;
        $dat = array();

				 $count = $start + 1;
        foreach($feeds as $r) {
					//var_dump($r); die;
          //$title = substr($r->title,0,10 );
          //$content = substr($r->content,0,50 );

             $dat[] = array(
							    $count,
									$r->date,
									$r->title,
									'<div class="btn-group btn-group-sm" style="float: none;">'.
									'<a href="'.site_url().'editPrayer/'.$r->id.'" type="button" class="tabledit-edit-button btn btn-sm btn-default" style="float: none;">'.
									'<i style="margin-bottom:5px;" class="material-icons list-icon" data-id="'.$r->id.'">create</i></a>'.
									'<button onclick="delete_item(event)" data-type="prayer" data-id="'.$r->id.'" type="button" class="tabledit-delete-button btn btn-sm btn-default" style="float: none;">'.
									'<i style="color:red;margin-bottom:5px;"  class="material-icons list-icon" data-type="prayer" data-id="'.$r->id.'">delete</i></button>'.
									'</div>'
             );
						 $count++;
        }

        $output = array(
             "draw" => $draw,
               "recordsTotal" => $total_feeds,
               "recordsFiltered" => $total_feeds,
               "data" => $dat
          );
        echo json_encode($output);
    }


		public function newPrayer(){
        $this->load->template('prayers/new', []); // this will load the view file
    }

    public function editPrayer($id=0)
    {

        $data['prayer'] = $this->prayers_model->getPrayerInfo($id);
        if(count((array)$data['prayer'])==0)
        {
            redirect('prayersListing');
        }
        $this->load->template('prayers/edit', $data); // this will load the view file
    }



    function saveNewPrayer()
    {

            $this->load->library('session');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('date','Prayer Date','trim|required');
						$this->form_validation->set_rules('title','Prayer Title','trim|required');
						$this->form_validation->set_rules('content','Prayer Content','trim|required');

            if($this->form_validation->run() == FALSE)
            {
							  $this->session->set_flashdata('error', "Some fields were left empty");
                redirect('newPrayer');
            }else {

							$date = $this->input->post('date');
							$title = $this->input->post('title');
							$author =$this->input->post('author');
							$content = $this->input->post('content');

							$info = array(
									'date' => $date,
									'title' => $title,
									'author' => $author,
									'content' => $content
							);

							if(!empty($_FILES['thumbnail']['name'])){
								$upload = $this->upload_thumbnail();
								if($upload[0]=='ok'){
									$info['thumbnail'] =  $upload[1];
								}
							}

              $this->prayers_model->addNewPrayer($info);
						}

							if($this->prayers_model->status == "ok")
							{
									$this->session->set_flashdata('success', $this->prayers_model->message);
							}
							else
							{
									$this->session->set_flashdata('error', $this->prayers_model->message);
							}
                redirect('newPrayer');

    }



    function editPayerData()
    {
			//var_dump($_FILES); die;
			$this->load->library('session');
			$this->load->library('form_validation');
      $id = $this->input->post('id');

			$this->form_validation->set_rules('date','Prayer Date','trim|required');
			$this->form_validation->set_rules('title','Prayer Title','trim|required');
			$this->form_validation->set_rules('content','Prayer Content','trim|required');

			if($this->form_validation->run() == FALSE)
			{
					$this->session->set_flashdata('error', "Some fields were left empty");
					redirect('editPrayer/'.$id);
			}else {

				$date = $this->input->post('date');
				$title = $this->input->post('title');
				$author =$this->input->post('author');
				$content = $this->input->post('content');

				$info = array(
						'date' => $date,
						'title' => $title,
						'author' => $author,
						'content' => $content
				);

				if(!empty($_FILES['thumbnail']['name'])){
					$upload = $this->upload_thumbnail();
					if($upload[0]=='ok'){
						$info['thumbnail'] =  $upload[1];
					}
				}

				$this->prayers_model->editPrayer($info,$id);
			}

				if($this->prayers_model->status == "ok")
				{
						$this->session->set_flashdata('success', $this->prayers_model->message);
				}
				else
				{
						$this->session->set_flashdata('error', $this->prayers_model->message);
				}

			redirect('editPrayer/'.$id);
    }


    function deletePrayer($id=0)
    {
      $this->load->library('session');
      $this->prayers_model->deletePrayer($id);
      if($this->prayers_model->status == "ok")
      {
          $this->session->set_flashdata('success', $this->prayers_model->message);
      }
      else
      {
          $this->session->set_flashdata('error', $this->prayers_model->message);
      }
      redirect('prayersListing');
    }

		public function upload_thumbnail(){
			$path = $_FILES['thumbnail']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$new_name = time().".".$ext;

			$config['file_name'] = $new_name;
			$config['upload_path']          = './uploads/thumbnails';
			$config['max_size']             = 10000;
			$config['allowed_types']        = 'jpg|png|jpeg|PNG';
			$config['overwrite'] = TRUE; //overwrite thumbnail


			//var_dump($config);

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('thumbnail'))
			{
					//$error = array('error' => $this->upload->display_errors());
					return ['error',strip_tags($this->upload->display_errors())];
			}
			else{
					$image_data = $this->upload->data();
					return ['ok',$new_name];
			}
		}

}
