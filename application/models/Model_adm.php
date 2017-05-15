<?php
    class Model_adm extends CI_Model{
        public function __construct(){
            parent::__construct();
        }
        public function timbaiviet($id=0){
            if(empty($id)) return false;
            $kq = $this->db->where('idbv',$id)->where('AnHien',1)->get('baiviet')->result_array();
            return $kq;
        }
        public function timtheloai($id){
            if(empty($id)) return false;
            $kq = $this->db->where('idCha',$id)->where('AnHien',1)->order_by('ThuTu','asc')->get('phanloaibai')->result_array();
            if(empty($kq)){
                $kq = $this->db->select('idbv,TieuDe,Alias,urlHinh,')->where('idloai',$id)->where('AnHien',1)->order_by('ThuTu','asc')->get('baiviet')->result_array();
            }
            return $kq;
        }
        public function timbaivietnoibat(){
            $kq = $this->db->select('idbv,TieuDe,Alias,urlHinh,')->where('NoiBat',1)->where('AnHien',1)->order_by('ThuTu','asc')->get('baiviet')->result_array();
            return $kq;
        }
        public function Mtimsodepkhts($tukhoa,$kho,$dauso=-1,$perpage=50,&$totalrows,$currentpage=1,$dang=''){
            $start = ($currentpage-1)*$perpage;
            settype($dauso,'int');
            if(empty($dauso) || $dauso==-1){
                $sql1 = 'sdt like "%'.$tukhoa.'"';
            }else $sql1 = 'sdt like "%'.$tukhoa.'" and sdt like "'.$dauso.'%"';
            $sql1 .= ' and dangso like "%'.$dang.'%"';
            if($kho=='camket'){
                $sql = 'select sdtview,goicuoc,gia from sim where '.$sql1.' and loai = "camket" limit ' . $start .','.$perpage ;
                $sql_dem = 'select  count(sdt) as dem from sim where '.$sql1.' and loai = "camket" ' ;

            }elseif($kho=='trasau'){
                $sql = 'select sdtview,goicuoc,gia from sim where '.$sql1.'and (loai = "khuyenkhich" or loai = "tudo" ) limit '. $start .','.$perpage;
                $sql_dem = 'select count(sdt) as dem from sim where '.$sql1.'and (loai = "khuyenkhich" or loai = "tudo" ) ';
                //$return = $this->db->query($sql)->result_array();
                //return $return;
            }elseif($kho=='tratruoc'){
                $sql = 'select sdtview,goicuoc,gia from sim where '.$sql1.' and loai = "tratruoc" limit '. $start .','.$perpage;
                $sql_dem = 'select count(sdt) as dem from sim where '.$sql1.' and loai = "tratruoc" ';
                //$return = $this->db->query($sql)->result_array();
                //return $return;
            }else{
                return false;
            }
            $query_total = $this->db->query($sql_dem)->result_array();
            $totalrows = $query_total[0]['dem'];
            $return = $this->db->query($sql)->result_array();
            return $return;
        }
        public function nextlink($baseURL,$totalrow,$currentpage=1,$perpage=50,$dangso=''){
            $return['nextLink'] = '';
            if($totalrow<=0) return $return;
            $totalpage = ceil($totalrow/$perpage);
            if($totalpage<=1) return $return;
            $nextLink="";
        	if ($currentpage < $totalpage) {
        		$nextPage = $currentpage + 1;
        		$nextLink = "$baseURL&page=$nextPage&dang=$dangso";
        	}
            $return['nextLink'] = $nextLink;
        	return $return;
        }
        public function pageslink($baseURL,$totalrow,$currentpage=1,$perpage=50){
            if($totalrow<=0) return 0;
            $totalpage = ceil($totalrow/$perpage);
            if($totalpage<=1) return 0;
            $firstLink="";  $prevLink="";  $lastLink="";  $nextLink="";
            if ($currentpage > 1) {
        		$firstLink = "$baseURL";
        		$prevPage = $currentpage - 1;
        		$prevLink="$baseURL&page=$prevPage";
        	}
        	if ($currentpage < $totalpage) {
        		$lastLink = "$baseURL&page=$totalpage";
        		$nextPage = $currentpage + 1;
        		$nextLink = "$baseURL&page=$nextPage";
        	}
            $return['firstLink'] = $baseURL;
            $return['prevLink'] = $prevLink;
            $return['nextLink'] = $nextLink;
            $return['lastLink'] = $lastLink;
        	return $return;
        }
        public function pageslist($baseURL, $totalrow, $currentpage=1, $perpage=50, $offset=2){
            if($totalrow<=0) return 0;
            $totalpage = ceil($totalrow/$perpage);
            if($totalpage<=1) return 0;
            $from = $currentpage - $offset;
            $to = $currentpage + $offset;
            if($from<=0) {$from = 1; $to = $offset*2;}
            if ($to > $totalpage) { $to = $totalpage; $from = $totalpage-$offset; }
            $links = "";
            for($j = $from; $j <= $to; $j++) {
                if($j == $currentpage) $return[$j]="";
                else $return[$j]= "$baseURL&page=$j";
        	} //for
        	return $return;
        }
        public function themso($path,$dang=0){
            $data = '';
            $i=0; $k = 0;
            $mydang = $this->db->select('keyds,tenkey,tends')->get('dangsim')->result_array();
            //$dem = count($array);
            $dem = 1;
            $row = 1;
            if (($handle = fopen($path, "r")) !== FALSE) {
              while (($array = fgetcsv($handle, 100, "\t")) !== FALSE) {
                $num = count($array);// dem 1 dong co bao nhiu record hien tai co $data[0] $data1 $data2
                if($num < 4) die('Dong '. $row . ' sai');
                $dang = '';$camket='';
                if(empty($array[$k]) || empty($array[$k+2]) ) continue;
                $sdt = $array[$k];
                $gia = $array[$k+2];
                if(empty($sdt)) continue;
                if($array[$k+1]=="0"){
                    $dang='tudo';
                }elseif($array[$k+1]=="1"){$dang='tratruoc';}
                preg_match("/CK*/i",$array[$k+3],$kq_matched);
                if(!empty($kq_matched)){
                    $dang='camket'; $camket = $array[$k+3];
                }
                $viewsdt = '';$dang_insert='';
                preg_match_all('/[0-9]/', $sdt, $matches, PREG_SET_ORDER);
                $sdt_arr = $matches;
                foreach($mydang as $dangso){
                    preg_match_all('/[a-zA-Z]/', $dangso['keyds'], $dang_arr, PREG_SET_ORDER);
                    if(!empty($dang_arr)){
                        $dem_dang = count($dang_arr);
                        for($d=0;$d<$dem_dang;$d++){
                            for($j=$d+1;$j<$dem_dang;$j++){
                                if($dang_arr[$d][0]==$dang_arr[$j][0]){
                         			$start = ( 9 - $dem_dang ) + $d ;
                         			$ss = $j - $d;
                         			if($sdt_arr[$start][0] != $sdt_arr[$start+$ss][0]){break 2;}
                          		}
                            }
                            if($d == $dem_dang-1){
                                $dang_insert.=','.$dangso['tenkey'];
                                //viet viewsdt ở đây
                                if(empty($viewsdt)){
                                    $viewsdt = '0';
                                    $start = 9 - $dem_dang ; $DemArraySdt = count($sdt_arr);
                                    $ViTriDau = strpos($dangso['tends'],'.');
                                    if(!empty($ViTriDau)){
                                        for($demview = 0 ; $demview<$DemArraySdt; $demview++){
                                            if($demview==$start){$viewsdt .= '.';}
                                            if($demview==$ViTriDau+$start){
                                                $viewsdt .= '.';
                                                $ViTriDau = strpos($dangso['tends'],'.',$ViTriDau+1);
                                                $ViTriDau = $ViTriDau -1;
                                            }
                                            $viewsdt .= $sdt_arr[$demview][0];
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $dang_arr = explode('-',$dangso['keyds']);
                        if(empty($dang_arr)){
                        $preg_dangso = $dangso['keyds'];
                        $preg = "/$preg_dangso$/i";
                        preg_match($preg,$sdt,$matches);
                        if(!empty($matches)){$dang_insert.=','.$dangso['tenkey'];}
                        }else{
                            foreach($dang_arr as $dangtheoso){
                                $preg_dangso = $dangtheoso;
                                $preg = "/$preg_dangso$/i";
                                preg_match($preg,$sdt,$matches);
                                if(!empty($matches)){$dang_insert.=','.$dangso['tenkey'];}
                            }
                        }
                    }
                }
                if(empty($viewsdt)) $viewsdt = '0'.$sdt_arr[0][0].$sdt_arr[1][0].$sdt_arr[2][0].'.'.$sdt_arr[3][0].$sdt_arr[4][0].$sdt_arr[5][0].'.'.$sdt_arr[6][0].$sdt_arr[7][0].$sdt_arr[8][0];
                $json_dang = trim($dang_insert,',');
                $i++;
                $data .= ",('$sdt','$dang','$camket','$gia','$json_dang','$viewsdt')";
                if($i==500){
                    $data = trim($data,",");
                    $sql = "insert into sim (sdt,loai,goicuoc,gia,dangso,sdtview) values " . $data;
                    if(!$this->db->query($sql)){$error .= ' 1 lỗi phat sinh';}
                    $i=0; $data='';
                }
                $row++; // tang row luon
              }
            }else{
                die('chưa có file này');
            }
            if(!empty($data)){
                $data = trim($data,",");
                $sql = "insert into sim (sdt,loai,goicuoc,gia,dangso,sdtview) values " . $data;
                if(!$this->db->query($sql)){
                    $error .= ' 1 lỗi phat sinh';
                }
            }
            fclose($handle);
            echo "Đã thêm " .$row ." records <br>";
        }//end themso
        public function getdssdt($tt=1){
            $table = $this->db->where('trangthai',1)->where('trungtam',$tt)->get('vas');
            return $table->result_array();
        }
        public function getpasstheosdt($sdt){
            $table = $this->db->where('sdt',$sdt)->get('vas');
            $obj = $table->row();
            return $obj->password;
        }
        public function tattrangthai($sdt){
            $check = $this->checksdt($sdt);
            if($check==0) return false;
            $this->db->set('trangthai',0)->where('sdt',$sdt)->update('vas');
            return true;
        }
        public function battrangthai($sdt){
            $check = $this->checksdt($sdt);
            if($check==0) return false;
            $this->db->set('trangthai',1)->where('sdt',$sdt)->update('vas');
            return true;
        }
        public function doitrangthai($sdt){
            $check = $this->checksdt($sdt);
            if($check==0) return false;
            elseif($check==1) $this->db->set('trangthai',0)->where('sdt',$sdt)->update('vas');
            else $this->db->set('trangthai',1)->where('sdt',$sdt)->update('vas');
            return true;
        }
        public function doisdt($sdt=-1,$trungtam=-1){
            if($sdt!=-1){
                $table = $this->db->where('sdt',$sdt)->get('vas');
            }else{
                if($trungtam!=-1){
                    $table = $this->db->where('trungtam',$trungtam)->where('trangthai','1')->get('vas');
                }else{
                    $sql = 'SELECT sdt,trangthai,id FROM vas WHERE trangthai=1 order by rand() limit 1';
                    $table = $this->db->query($sql);
                }
            }
            $obj = $table->row(); $trangthai = $obj->trangthai;
            if(empty($trangthai)){
                $kq = $this->GetSdtIdLonHon($obj->id);
                if(empty($kq)) return 0; // false nếu ko tìm thấy sdt nào
                $this->session->set_userdata('sdt_dk',$kq->sdt);
                return 1;
            }else{
                $this->session->set_userdata('sdt_dk',$obj->sdt);
                return 1;
            }
        }
        public function doisdt_returnsdt($sdt=-1,$trungtam=-1){
            if($sdt!=-1){
                $table = $this->db->where('sdt',$sdt)->get('vas');
            }else{
                if($trungtam!=-1){
                    $table = $this->db->where('trungtam',$trungtam)->where('trangthai','1')->get('vas');
                }else{
                    //$sql = 'SELECT sdt,trangthai,id FROM vas WHERE trangthai=1 order by rand() limit 1';
                    $sql = 'SELECT sdt,trangthai,id FROM vas WHERE sdt="84901067979"';//fic 1 chỗ
                    $table = $this->db->query($sql);
                }
            }
            $obj = $table->row(); $trangthai = $obj->trangthai;
            return $obj->sdt;// fix bỏ chỗ này
            if(empty($trangthai)){
                $kq = $this->GetSdtIdLonHon($obj->id);
                if(empty($kq)) return 0; // false nếu ko tìm thấy sdt nào
                return $kq->sdt;
            }else{
                return $obj->sdt;
            }
        }
        public function GetSdtIdLonHon($id){
            //$table = $this->db->where('id >',$id)->where('trangthai','1')->where('trungtam',2)->get('vas');
            $table = $this->db->where('id >',$id)->where('trangthai','1')->get('vas');
            $dem = $table->num_rows();
            if($dem==0){
                //$table = $this->db->where('id <=',$id)->where('trangthai','1')->where('trungtam',2)->get('vas');
                $table = $this->db->where('id <=',$id)->where('trangthai','1')->get('vas');
                $dem = $table->num_rows();
                if(empty($dem)) return 0;
            }
            $return = $table->row();
            return $return;
        }
        /*// ---hàm checksdt----
        nếu sdt đó đk đc. trả về 1
        //nếu có sdt đó mà ko đk đc trả về 2;
        //nếu rỗng hoặc ko có trả về 0*/
        public function checksdt($sdt=0){
            if($sdt==0) return 0;
            $table = $this->db->like('sdt',$sdt)->get('vas');
            $dem = $table->num_rows();
            if($dem==1){
                $obj = $table->row(); $id = $obj->id;
                $trangthai = $obj->trangthai;
                if($trangthai==1)
                    return 1;
                else{
                    return 2;
                }
            }else{
                return 0;
            }
        }
        public function insert_vas($data){
            if(empty($data)) return false;
            $kq = $this->db->insert('dsdkvas',$data);
            return $kq;
        }
        public function insert_tracuusdt($data){
            $kq = $this->db->insert('tracuusdt',$data);
            return $kq;
        }
        public function insert_tracuucuoc($data){
            $kq = $this->db->insert('tracuucuoc',$data);
            return $kq;
        }
        public function insert_tracuumst($data){
            $kq = $this->db->insert('tracuumst',$data);
            return $kq;
        }
        public function insert_tracuuam($data){
            $kq = $this->db->insert('tracuuam',$data);
            return $kq;
        }
        public function total_delete($table){
            $kq = $this->db->query('delete from '.$table);
            $this->db->query('ALTER TABLE '.$table.' AUTO_INCREMENT = 1');
            return $kq;
        }
        public function insert_tracuutt($data){
            $kq = $this->db->insert('tracuutt',$data);
            return $kq;
        }
        public function insert_tracuutbc($data){
            $kq = $this->db->insert('tracuutbc',$data);
            return $kq;
        }
    }
?>