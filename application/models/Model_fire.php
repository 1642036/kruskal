<?php

class Model_fire extends CI_Model
{
    protected $_table = 'phanloaibai';

    public function __construct()
    {
        parent::__construct();
    }
        public function insert_array_table($array,$table){
            if(empty($array) || empty($table) ) return false;
            return $this->db->insert($table,$array);
        }
        public function update_array_table($array,$field,$id,$table){
            if(empty($array) || empty($table) ) return false;
            $this->db->set($array); $this->db->where($field,$id);
            return $this->db->update($table);
        }
        public function edit_table_total($array,$id,$field,$table){
            if(empty($array) || empty($id)) return false;
            $this->db->set($array);
            $this->db->where($field , $id);
            $kq = $this->db->update($table);
            return $kq;
        }
    public function insert_table($array){
        if(empty($array)) return false;
        $kq = $this->db->insert($this->_table,$array);
        return $kq;
    }
    public function edit_table($array,$id){
        if(empty($array)) return false;
        $this->db->set($array);
        $this->db->where('idloai' , $id);
        $kq = $this->db->update($this->_table);
        return $kq;
    }
    public function get_value_datajs($name,$id){
        $kq = $this->db->query('select Datajs from '. $this->_table .' where idloai='.$id);
        $row = $kq->result_array();
        if(empty($row)) return '';
        $data = json_decode($row[0]['Datajs']);
        return $data->$name;
    }
    public function one_theloai($idloai=-1)
    {
        $kq = $this->db->query('select * from '.$this->_table.' where ( idloai = ' .$idloai.' or '.$idloai.' = -1 )');
        $row = $kq->result_array();
        $kq->free_result();
        return $row;
    }
    public function listtheloai($idcha = 0, $allRe = false)
    {
        if ($allRe) {
            $allRe = -1;
            $idcha = -1;
        } else $allRe = 0;
        $kq = $this->db->query('select * from ' . $this->_table . ' where ( idCha =  ' . $idcha .
            ' or ' . $allRe . '= -1 ) order by ThuTu ASC');
        $row = $kq->result_array();$kq->free_result();
        return $row;
    }//function the loai
    public function listtheloaiAH($idcha = 0, $allRe = false)
    {
        if ($allRe) {
            $allRe = -1;
            $idcha = -1;
        } else $allRe = 0;
        $kq = $this->db->query('select * from ' . $this->_table . ' where ( idCha =  ' . $idcha .
            ' or ' . $allRe . '= -1 ) and AnHien=1 order by ThuTu ASC');
        $row = $kq->result_array();$kq->free_result();
        return $row;
    }//function the loai

    public function StripAny($str)
    {
        $str = trim($str);
        $str = strip_tags($str);
        $str = $this->bodau($str);
        if (!$str) return "empty";
        else {
            $str = str_replace("?", "", $str);
            $str = str_replace("&", "", $str);
            $str = str_replace("'", "", $str);
            $str = str_replace("+", "", $str);
            $str = str_replace("=", "", $str);
            $str = str_replace("(", "", $str);
            $str = str_replace(")", "", $str);
            $str = str_replace("/", "-", $str);
            $str = str_replace("$", "", $str);
            $str = str_replace("\"", "", $str);
            $str = str_replace("\\", "", $str);
            while (strpos($str, '  ')) {
                $str = str_replace('  ', ' ', $str);
            }
            $str = mb_convert_case($str, MB_CASE_LOWER, 'utf-8');
            $str = str_replace(" ", "-", $str);
        }
        return $str;
    }

    public function bodau($str)
    {
        if (!$str) return false;
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ',
            'D' => 'Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ');
        foreach ($unicode as $khongdau => $codau) {
            $arr = explode("|", $codau);
            $str = str_replace($arr, $khongdau, $str);
        }
        return $str;
    }
    public function suatheloai($act,$id,$arr){
        if($act=='thututheloai'){
            $sql = 'update phanloaibai set ThuTu = '. $arr .' where idloai ='.$id;
            $kq = $this->db->query($sql);
            return $kq;
        }elseif($act=='anhientheloai'){
            $sql = 'update phanloaibai set AnHien = '. $arr .' where idloai ='.$id;
            $kq = $this->db->query($sql);
            return $kq;
        }elseif($act=='theloai'){
            $this->db->set($arr);
            $this->db->where('idloai' , $id);
            $kq = $this->db->update($this->_table);
            return $kq;
        }elseif($act=='xoatheloai'){
            $kq = $this->db->delete($this->_table,array('idloai' => $id) );
            return $kq;
        }
    }
    public function themtheloai($array)
    {
        $sql = 'select count(idloai) as dem from ' . $this->_table .
            ' where Alias like "' . $array['Alias'] . '"';
        $check = $this->db->query($sql); $row = $check->result_array(); $check->free_result();
        if(($row[0]['dem']) > 0 ) {
            $this->session->set_flashdata('error', ' dữ liệu trùng');
            return false;}
        $kq = $this->db->insert($this->_table, $array);
        return $kq;
    }
}

?>
