<?php
/**
 * Created by PhpStorm.
 * User: ray
 * Date: 12/06/2018
 * Time: 14:29
 */

class Prayers_model extends CI_Model{
    public $status = 'error';
    public $message = 'Something went wrong';
    public $data = [];
    public $date = "";

    function __construct(){
       parent::__construct();
	  }

    function getPrayer($date=""){
        $this->db->select('tbl_prayers.*');
        $this->db->from('tbl_prayers');
        $this->db->where('date', $date);
        $query = $this->db->get();
        return $query->row();
    }



   function adminPrayersListing($columnName,$columnSortOrder,$searchValue,$start, $length){
     $this->db->select('tbl_prayers.*');
     $this->db->from('tbl_prayers');
     if($searchValue!=""){
         $this->db->like('title', $searchValue);
         $this->db->or_like('content', $searchValue);
     }
     if($columnName!=""){
        $this->db->order_by($columnName, $columnSortOrder);
     }else{
       $this->db->order_by("date", "DESC");
     }
     $this->db->limit($length,$start);
     $query = $this->db->get();
     return $query->result();
   }

   public function get_total_prayers($searchValue=""){
     if($searchValue==""){
       $query = $this->db->select("COUNT(*) as num")->get("tbl_prayers");
     }else{
       $this->db->select("COUNT(*) as num");
       $this->db->from('tbl_prayers');
       $this->db->like('title', $searchValue);
       $this->db->or_like('content', $searchValue);
       $query = $this->db->get();
     }
     $result = $query->row();
     if(isset($result)) return $result->num;
     return 0;
  }

   function checkDevotionalExists($date, $id = 0)
   {
       $this->db->select("title");
       $this->db->from("tbl_prayers");
       $this->db->where("date", $date);
       if($id != 0){
           $this->db->where("id !=", $id);
       }
       $query = $this->db->get();

       return $query->result();
   }


   function addNewPrayer($info)
   {
     $insert_id = 0;
     //if(empty($this->checkDevotionalExists($info['date']))){
       $this->db->trans_start();
       $this->db->insert('tbl_prayers', $info);
       $insert_id = $this->db->insert_id();
       $this->db->trans_complete();
       $this->status = 'ok';
       $this->message = 'Prayer added successfully';
     /*}else{
       $this->status = 'error';
       $this->message = 'Devotional already added for this date '.$info['date'];
     }*/
     return $insert_id;
   }


   function editPrayer($info, $id){
     //if(empty($this->checkDevotionalExists($info['date'],$id))){
       $this->db->where('id', $id);
       $this->db->update('tbl_prayers', $info);
       $this->status = 'ok';
       $this->message = 'Prayer edited successfully';
     /*}else{
       $this->status = 'error';
       $this->message = 'Date for this devotional already exists for another';
     }*/
   }


   function getPrayerInfo($id)
   {
     $this->db->select('tbl_prayers.*');
     $this->db->from('tbl_prayers');
       $this->db->where('tbl_prayers.id', $id);
       $query = $this->db->get();
       $row = $query->row();
       if(count((array)$row) > 0 && $row->thumbnail!=""){
         $row->thumbnail = base_url()."uploads/thumbnails/".$row->thumbnail;
       }
       return $row;
   }


   function deletePrayer($id){
       $this->db->where('id', $id);
       $this->db->delete('tbl_prayers');
       $this->status = 'ok';
       $this->message = 'Prayer deleted successfully.';
   }

   function fetchPrayerPoints($date=""){
       $this->db->select('tbl_prayers.*');
       $this->db->from('tbl_prayers');
       $this->db->where('date',$date);
       $this->db->order_by('date','desc');
       $query = $this->db->get();
       $result = $query->result();
       foreach ($result as $res) {
         $res->thumbnail = base_url()."uploads/thumbnails/".$res->thumbnail;
       }
       return $result;
   }
}
