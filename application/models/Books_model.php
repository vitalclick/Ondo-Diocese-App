<?php
/**
 * Created by PhpStorm.
 * User: ray
 * Date: 12/06/2018
 * Time: 14:29
 */

class Books_model extends CI_Model{
    public $status = 'error';
    public $message = 'Error processing requested operation.';
    public $user = "";

    function __construct(){
       parent::__construct();
	  }

   public function get_related_books($category, $bookid, $email = ""){
     $this->db->select('tbl_ebooks.*');
     $this->db->from('tbl_ebooks');
     $this->db->where('tbl_ebooks.id !=',$bookid);

       $this->db->limit(6);

       $query = $this->db->get();
       $result = $query->result();
       foreach ($result as $res) {
         $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
       }
       return $result;
   }

    public function fetch_user_purchases($page = 0,$email="null"){
      $this->db->select('tbl_ebooks.*');
      $this->db->from('tbl_ebooks');
      $this->db->join('tbl_books_purchases','tbl_books_purchases.book=tbl_ebooks.id');
      $this->db->where('tbl_books_purchases.email',$email);
        $this->db->order_by('tbl_books_purchases.date','desc');

        if($page!=0){
            $this->db->limit(20,$page * 20);
        }else{
          $this->db->limit(20);
        }

        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $res) {
          $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
        }
        return $result;
    }

    public function fetch_user_purchases_id($email="null", $type = ""){
      $purchases = [];
      $this->db->select('tbl_books_purchases.book');
      $this->db->from('tbl_books_purchases');
      $this->db->where('tbl_books_purchases.email',$email);
       $this->db->where('tbl_books_purchases.type',$type);
      $query = $this->db->get();
      $result = $query->result();
      foreach ($result as $res) {
        array_push($purchases, $res->book);
      }
      return $purchases;
    }

    public function searchListing($data = []){
      $query = $data->query;

      $email = "null";
      if(isset($data->email)){
        $email = $data->email;
      }
      $this->db->select('tbl_ebooks.*');
      $this->db->from('tbl_ebooks');
      $this->db->like('tbl_ebooks.title', $query);
      $this->db->or_like('tbl_ebooks.description', $query);
      $this->db->or_like('tbl_categories.name', $query);
        $this->db->order_by('tbl_ebooks.id','DESC');
        $this->db->limit(50);
        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $res) {
          $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
        }
        return $result;
     }

    function fetchRandom($email = ""){
      $this->db->select('tbl_ebooks.*,tbl_artists.id as artist_id,tbl_artists.name as authorname
      ,tbl_categories.id as category_id,tbl_categories.name as categoryname');
      $this->db->from('tbl_ebooks');
      $this->db->join('tbl_categories','tbl_categories.id=tbl_ebooks.category');
      $this->db->join('tbl_artists','tbl_artists.id=tbl_ebooks.author');
        $this->db->order_by('tbl_ebooks.id','RANDOM');
        $this->db->limit(20);
        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $res) {
          $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
          $res->thumbnail2 = base_url()."uploads/thumbnails/".$res->thumbnail2;
          if($res->pdf!=""){
            $res->pdf = base_url()."uploads/books/".$res->pdf;
          }
          if($res->epub!=""){
            $res->epub = base_url()."uploads/books/".$res->epub;
          }
          $res->hasrate = $this->checkIfUserRateBook($res->id,$email,"books");
          $res->rates = $this->getRatesSummary($res->id, "books");
        }
        return $result;
    }

    function fetch_ebooks($page,$email = ""){
      $this->db->select('tbl_ebooks.*');
      $this->db->from('tbl_ebooks');
        $this->db->order_by('tbl_ebooks.date','DESC');
        if($page!=0){
            $this->db->limit(20,$page * 20);
        }else{
          $this->db->limit(20);
        }
        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $res) {
          $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
        }
        return $result;
    }



    public function get_total_books(){
      $query = $this->db->select("COUNT(*) as num")->get("tbl_ebooks");
      $result = $query->row();
      if(isset($result)) return $result->num;
      return 0;
   }

   function ebooksListing($email = ""){
     $this->db->select('tbl_ebooks.*');
     $this->db->from('tbl_ebooks');
       $this->db->order_by('tbl_ebooks.id','DESC');
       $query = $this->db->get();
       $result = $query->result();
       foreach ($result as $res) {
         $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
       }
       return $result;
   }


   function addNewEbook($info)
   {
     $this->db->trans_start();
     $this->db->insert('tbl_ebooks', $info);
     $this->db->trans_complete();
     $this->status = 'ok';
     $this->message = 'Item added successfully';
   }


   function editEbook($info, $id){
     $this->db->where('id', $id);
     $this->db->update('tbl_ebooks', $info);
     $this->status = 'ok';
     $this->message = 'Ebook edited successfully';
   }


   function getEbookInfo($id, $email = "")
   {
     $this->db->select('tbl_ebooks.*');
     $this->db->from('tbl_ebooks');
       $this->db->where('tbl_ebooks.id', $id);
       $query = $this->db->get();
       $row = $query->row();
       if(count((array)$row) > 0){
         $row->thumbnail = base_url()."uploads/thumbnails/".$row->thumbnail;
       }
       return $row;
   }


   function deleteEbook($id){
       $this->db->where('id', $id);
       $this->db->delete('tbl_ebooks');
        $this->status = 'ok';
        $this->message = 'Ebook deleted successfully.';
   }

   function purchasesListing($date1,$date2,$columnName,$columnSortOrder,$searchValue,$start, $length){
     $this->db->select('tbl_books_purchases.*');
     $this->db->from('tbl_books_purchases');
     if($searchValue!=""){
         $this->db->like('tbl_books_purchases.email', $searchValue);
         $this->db->or_like('tbl_books_purchases.name', $searchValue);
         $this->db->or_like('tbl_books_purchases.booktitle', $searchValue);
         $this->db->or_like('tbl_books_purchases.amount', $searchValue);
     }
     if($columnName!=""){
        $this->db->order_by($columnName, $columnSortOrder);
     }
     $this->db->limit($length,$start);
     $query = $this->db->get();
     $result =  $query->result();
     return $result;
   }

   public function get_total_purchases($date1,$date2,$searchValue=""){
     $this->db->select("COUNT(*) as num");
     $this->db->from('tbl_books_purchases');
     if($searchValue!=""){
       $this->db->like('tbl_books_purchases.email', $searchValue);
       $this->db->or_like('tbl_books_purchases.name', $searchValue);
       $this->db->or_like('tbl_books_purchases.booktitle', $searchValue);
       $this->db->or_like('tbl_books_purchases.amount', $searchValue);
     }

     $query = $this->db->get();
     $result = $query->row();
     if(isset($result)) return $result->num;
     return 0;
  }

  function recordpurchase($info)
  {
    $this->db->trans_start();
    $this->db->insert('tbl_books_purchases', $info);
    $this->db->trans_complete();
    $this->status = 'ok';
    $this->message = 'Item added successfully';
  }
}
