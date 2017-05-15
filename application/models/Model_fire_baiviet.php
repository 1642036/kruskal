<?php
    class Model_fire_baiviet extends CI_Model{
        protected $_table = 'baiviet';
        public function __construct(){
            parent::__construct();
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
        public function listbv($idloai=-1,$currentpage,$perpage=10,&$totalrow){
            settype($idloai,'int'); if(empty($idloai)) $idloai = -1;
            $start = ($currentpage-1)*$perpage;
            $kq = $this->db->query("select count(idbv) as dem from $this->_table
                where AnHien=1 and (idLoai = $idloai or $idloai = -1
                or idLoai in (select idLoai from phanloaibai
                where idcha = $idloai ) )");// lưu ý quan trong ko đc để limit vao đây
            $row = $kq->row();
            $totalrow = (empty($row)) ? 0 : $row->dem ;
            $kq = $this->db->query("select * from ".$this->_table." where AnHien=1 and (idLoai = $idloai or $idloai = -1
                or idLoai in (select idLoai from phanloaibai where idcha = $idloai ) ) limit $start,$perpage");
            $row = $kq->result_array(); $kq->free_result();
            return $row;
        }
        public function chitietbv($idbv){
            $kq = $this->db->query('select * from '.$this->_table.' where AnHien=1 and idbv = '.$idbv.' ');
            $row = $kq->result_array(); $kq->free_result();
            return $row;
        }
        public function bvcungtheloai($idbv){
            $kq = $this->db->query('select idLoai from baiviet where idbv = '. $idbv);
            $row = $kq->result_array(); $kq->free_result();
            $idLoai = $row[0]['idLoai'];
            $kq = $this->db->query('select idbv,TieuDe,baiviet.Alias as bvAlias,TomTat,urlHinh,Ngay,idUser,baiviet.idLoai as bvIdloai,
            SoLanXem from baiviet,phanloaibai where baiviet.AnHien=1 and baiviet.idLoai = phanloaibai.idloai and baiviet.idLoai = '.$idLoai.'
            and idbv != '.$idbv.'  limit 5 ');
            $row = $kq->result_array(); $kq->free_result();
            return $row;
        }
        public function bvcungtacgia($idbv){
            $kq = $this->db->query('select idUser from '. $this->_table.' where idbv = '.$idbv);
            $row = $kq->result_array(); $kq->free_result();
            $iduser = $row[0]['idUser'];
            $kq = $this->db->query('select idbv,TieuDe,Alias,TomTat,urlHinh,Ngay,idUser,idLoai,
            SoLanXem from '.$this->_table.' where AnHien=1 and idUser = '. $iduser . ' and idbv != '.$idbv.' limit 5');
            $row = $kq->result_array(); $kq->free_result();
            return $row;
        }
        public function bvMoiNhatCungLoaiOrKo($idloai=-1){
            settype($idloai,'int'); if(empty($idloai)) $idloai = -1;
            $kq = $this->db->query("select * from ".$this->_table." where (idLoai = $idloai or $idloai = -1
                or idLoai in (select idLoai from phanloaibai where AnHien=1 and idcha = $idloai ) ) order by rand()  limit 5  ");
            $row = $kq->result_array(); $kq->free_result();
            return $row;
        }
        public function timkiem($tukhoa='',$currentpage,$perpage=10,&$totalrow){
            settype($idloai,'int'); if(empty($idloai)) $idloai = -1;
            $start = ($currentpage-1)*$perpage;
            $kq = $this->db->query("select count(idbv) as dem from $this->_table where AnHien=1 and  ( TieuDe like '%$tukhoa%'
            or TomTat like '%$tukhoa%' or Content like '%$tukhoa%' )" );// lưu ý quan trong ko đc để limit vao đây
            $row = $kq->row();
            $totalrow = (empty($row)) ? 0 : $row->dem ;
            $kq = $this->db->query("select * from ".$this->_table." where AnHien=1 and  ( TieuDe like '%$tukhoa%'
            or TomTat like '%$tukhoa%' or Content like '%$tukhoa%' ) limit $start,$perpage ");
            $row = $kq->result_array(); $kq->free_result();
            return $row;
        }
    }
?>