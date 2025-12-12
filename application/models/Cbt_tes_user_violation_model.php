<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Model untuk menyimpan log pelanggaran tab switch
*/
class Cbt_tes_user_violation_model extends CI_Model{
	public $table = 'cbt_tes_user_violation';
	
	function __construct(){
        parent::__construct();
    }
	
    function save($data){
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    function delete($kolom, $isi){
        $this->db->where($kolom, $isi)
                 ->delete($this->table);
    }
    
    function count_by_tesuser($tesuser_id){
        $this->db->select('COUNT(*) AS hasil')
                 ->where('violation_tesuser_id', $tesuser_id)
                 ->from($this->table);
        return $this->db->get();
    }

    function get_by_tesuser($tesuser_id){
        $this->db->where('violation_tesuser_id', $tesuser_id)
                 ->from($this->table)
                 ->order_by('violation_time', 'DESC');
        return $this->db->get();
    }
}
