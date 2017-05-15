<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
class Fire extends CI_Controller
	{
	public function __construct()
		{
		parent::__construct();
		$this->load->model('Model_adm');
		}
	public function test()
		{
		$this->load->library('curl_new');
		// "10.76041889461084,106.65377140045166,0" "10.753883908991131,106.65497303009033,1"
        // "10.7604144,106.6537741,4" , "10.76041889461084,106.65377140045166,0"
		$lat1 = '10.76041889461084';
		$lng1 = '106.65377140045166';
		$lat2 = '10.753883908991131';
		$lng2 = '106.65497303009033';
		$url_direction = "https://maps.googleapis.com/maps/api/directions/json?origin=$lat1,$lng1&destination=$lat2,$lng2&mode=walking&key=AIzaSyD1pf16ahH7FsdbEum3W2VU-ybqTpME30w";
		$json = $this->curl_new->simple_get($url_direction);
		echo $json;
		}
	public function testguifile1()
		{
		require_once (APPPATH . 'libraries/Pusher.php');
		$options = array(
			'cluster' => 'ap1',
			'encrypted' => false
		);
		$pusher = new Pusher('8a52acf03f615e76dafa', 'd4576a4d9153195b44fc', '330711', $options);
		$data['message'] = 'hello world';
		$data['sdt'] = '1222647037';
		$data['auth_code'] = '123';
		$pusher->trigger('my-channel-check', 'my-event', $data);
		echo ' đã gửi tin nhắn message đến my-channel my-event';
		}
	public function index()
		{
		  $this->load->view('map_index');
		}
	public function googlemap()
		{
		  if(!empty($_POST)){
		      if(empty($_POST['user'])) die('bạn chưa nhập user');
		      if(!isset($_POST['adress'])) die('bạn chưa nhập địa chỉ');
              $user = $this->StripAny($_POST['user']);
              $this->session->set_userdata('user',$user);
		      $this->load->view('map_direct',$_POST);
		  }else header("Location:".base_url());
		}
	function return_st($error, $reason)
		{
    		$return['error'] = $error;
    		$return['reason'] = $reason;
    		die(json_encode($return));
		}
    function checkdiem_new(&$dske = array() , $result1 = array() , $result2 = array() , $cotgiuacactram = 0)
		{
		$lat1 = $result1['lat'];$lat2 = $result2['lat'];
		$lng1 = $result1['lng'];$lng2 = $result2['lng'];
		$ten1 = $result1['ten'];$ten2 = $result2['ten'];
        $return = array();
		$url_direction = "https://maps.googleapis.com/maps/api/directions/json?origin=$lat1,$lng1&destination=$lat2,$lng2&mode=walking&key=AIzaSyD1pf16ahH7FsdbEum3W2VU-ybqTpME30w";
		$json = $this->curl_new->simple_get($url_direction);
		$data = json_decode($json);
		if ($data->status == 'OK')
			{
			$leg = $data->routes[0]->legs[0];
			$total_distance = $leg->distance->value;
			$dem_step = count($leg->steps);
			$khoangcach = 0;
			if (empty($dem_step))
				{
				$reason = 'chưa có dữ liệu khoảng cách';
				$this->return_st(1, $reason);
				}
			$dske[] = $return = array(
				"$lat1,$lng1,$ten1",
				"$lat2,$lng2,$ten2",
				$total_distance
			);
			}
		return $total_distance;
		}
	function checkdiem(&$dske = array() , $result1 = array() , $result2 = array() , $cotgiuacactram = 0)
		{
		$lat1 = $result1['lat'];$lat2 = $result2['lat'];
		$lng1 = $result1['lng'];$lng2 = $result2['lng'];
		$ten1 = $result1['ten'];$ten2 = $result2['ten'];
		$url_direction = "https://maps.googleapis.com/maps/api/directions/json?origin=$lat1,$lng1&destination=$lat2,$lng2&mode=walking&key=AIzaSyD1pf16ahH7FsdbEum3W2VU-ybqTpME30w";
		$json = $this->curl_new->simple_get($url_direction);
		$data = json_decode($json);
		if ($data->status == 'OK')
			{
			$leg = $data->routes[0]->legs[0];
			$total_distance = $leg->distance->value;
			$dem_step = count($leg->steps);
			$khoangcach = 0;
			if (empty($dem_step))
				{
				$reason = 'chưa có dữ liệu khoảng cách';
				$this->return_st(1, $reason);
				}
			$dske[] = array(
				"$lat1,$lng1,$ten1",
				"$lat2,$lng2,$ten2",
				$total_distance
			);
			}
		return $data;
		}
	function themdiemvaodske(&$dske, $result1 = array() , $result2 = array() , $to = array() , $khoangcach, $dem, $cotgiuacactram, $total_distance, $khoangcach_hientai)
		{
		$lat1 = $result1['lat'];$lat2 = $result2['lat'];
		$lng1 = $result1['lng'];$lng2 = $result2['lng'];
		                      $ten_result2 = $result2['ten'];
		$lat_cuoi = $to['lat'];$lng_cuoi = $to['lng'];$ten_cuoi = $to['ten'];
		$ten1 = $dske[count($dske) - 1][0];$ten2 = $dske[count($dske) - 1][1];
		$arr1 = explode(",", $ten1);$arr2 = explode(",", $ten2);
		$ten = -1;
		if ($arr1[2] > $arr2[2])
			{
			$ten = $arr1[2] + 1;
			$ten_vuathem = $arr1[2];
			$lat_vuathem = $arr1[0];
			$lng_vuathem = $arr1[1];
			}
		  else
			{
			$ten = $arr2[2] + 1;
			$ten_vuathem = $arr2[2];
			$lat_vuathem = $arr2[0];
			$lng_vuathem = $arr2[1];
			}
		if ($dem == 1)
			{
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat2,$lng2,$ten_result2",
				$khoangcach
			);
			}
		elseif ($dem == ($cotgiuacactram))
			{
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat_vuathem,$lng_vuathem,$ten_vuathem",
				$khoangcach
			);
			$khoangcach = $total_distance - $khoangcach_hientai;
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat_cuoi,$lng_cuoi,$ten_cuoi",
				$khoangcach
			);
			}
		  else
			{
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat_vuathem,$lng_vuathem,$ten_vuathem",
				$khoangcach
			);
			}
		return array(
			"$lat1,$lng1,$ten"
		);
		}
    function themdiemvaodske_new(&$dske, $result1 = array() , $result2 = array() , $to = array() , $khoangcach, $dem, $cotgiuacactram, $total_distance, $khoangcach_hientai)
		{
		$lat1 = $result1['lat'];$lat2 = $result2['lat'];
		$lng1 = $result1['lng'];$lng2 = $result2['lng'];
		                      $ten_result2 = $result2['ten'];
		$lat_cuoi = $to['lat'];$lng_cuoi = $to['lng'];$ten_cuoi = $to['ten'];
		$ten1 = $dske[count($dske) - 1][0];$ten2 = $dske[count($dske) - 1][1];
		$arr1 = explode(",", $ten1);$arr2 = explode(",", $ten2);
		$ten = -1;
		if ($arr1[2] > $arr2[2])
			{
			$ten = $arr1[2] + 1;
			$ten_vuathem = $arr1[2];
			$lat_vuathem = $arr1[0];
			$lng_vuathem = $arr1[1];
			}
		  else
			{
			$ten = $arr2[2] + 1;
			$ten_vuathem = $arr2[2];
			$lat_vuathem = $arr2[0];
			$lng_vuathem = $arr2[1];
			}
		if ($dem == 1)
			{
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat2,$lng2,$ten_result2",
				$khoangcach
			);
			}
		elseif ($dem == ($cotgiuacactram))
			{
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat_vuathem,$lng_vuathem,$ten_vuathem",
				$khoangcach
			);
			$khoangcach = $total_distance - $khoangcach_hientai;
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat_cuoi,$lng_cuoi,$ten_cuoi",
				$khoangcach
			);
			}
		  else
			{
			$dske[] = array(
				"$lat1,$lng1,$ten",
				"$lat_vuathem,$lng_vuathem,$ten_vuathem",
				$khoangcach
			);
			}
		return array(
			"$lat1,$lng1,$ten"
		);
		}
	function themcacdiemvaomotcanh(&$dske = array() , $data, $cotgiuacactram, $from, $to)
		{
		$leg = $data->routes[0]->legs[0];
		$total_distance = $leg->distance->value;
		$khoangvan = ceil($total_distance / $cotgiuacactram); // 0 < $khoangvan*1 < $khoangvan*2 < $khoangvan
		$dem = 0;
		$return = array();
		$check = 0;
		while ($dem <= $cotgiuacactram)
			{
			for ($kc = 1; $kc <= $cotgiuacactram; $kc++)
				{ // sua lai so 3
				if ($kc == $cotgiuacactram)
					{ // sua lai so 3
					$kctram = $total_distance;
					}
				  else $kctram = $kc * $khoangvan;
				$kctram_tru = ($kc - 1) * $khoangvan;
				$khoangcach = 0;
				$bandau = 0; 
				$dem_step = count($leg->steps);
				for ($i = 0; $i < $dem_step; $i++)
					{
					if ($leg->steps[$i]->distance->value == 0) continue;
					$khoangcach+= $leg->steps[$i]->distance->value;
                    
					$bandau = $leg->steps[$i]->distance->value;
					if ($khoangcach <= $kctram && $khoangcach > $kctram_tru ) 
						{
				        $dem++;
						$leg->steps[$i]->distance->value = 0;
						$toado_lat = $leg->steps[$i]->end_location->lat;
						$toado_lng = $leg->steps[$i]->end_location->lng;
						$diem = array(
							'lat' => $toado_lat,
							'lng' => $toado_lng
						);
						$return[] = $this->themdiemvaodske($dske, $diem, $from, $to, $bandau, $dem, $cotgiuacactram, $total_distance, $khoangcach);
                        if($dem == $cotgiuacactram) return $return;
						break;
						}
					}
				}
                //if($dem == ($dem_step)) return $return;
			}
		return $return;
		}
	function cheohaicanh(&$dske, $canh1, $canh2, $cotgiuacactram)
		{
		// lat,lng,ten
		//$tongdiem = $cotgiuacactram - 1;
		for ($i = 0; $i < $cotgiuacactram; $i++)
			{
			$lam = $cotgiuacactram - ($i + 1);
			$arr1 = explode(",", $canh1[$i][0]);
			$arr2 = explode(",", $canh2[$lam][0]);
			$lat1 = $arr1[0];
			$lat2 = $arr2[0];
			$lng1 = $arr1[1];
			$lng2 = $arr2[1];
			$ten1 = $arr1[2];
			$ten2 = $arr2[2];
			// $from = array("$lat1,$lng1,$ten1");
			// $to = array("$lat2,$lng2,$ten2");
			$from = array(
				'lat' => $lat1,
				'lng' => $lng1,
				'ten' => $ten1
			);
			$to = array(
				'lat' => $lat2,
				'lng' => $lng2,
				'ten' => $ten2
			);
			//echo $ten1 . " - " . $ten2 . '<br />';
			//continue;
			$url_direction = "https://maps.googleapis.com/maps/api/directions/json?origin=$lat1,$lng1&destination=$lat2,$lng2&mode=walking&key=AIzaSyD1pf16ahH7FsdbEum3W2VU-ybqTpME30w";
			$json = $this->curl_new->simple_get($url_direction);
			$data = json_decode($json);
			if ($data->status == 'OK')
				{
				$leg = $data->routes[0]->legs[0];
				$total_distance = $leg->distance->value;
				$dem_step = count($leg->steps);
				$khoangcach = 0;
				if (empty($dem_step))
					{
					return;
					} // return neu rỗng
				//$khoangvan = ceil($total_distance / $cotgiuacactram); // 0 < $khoangvan*1 < $khoangvan*2 < $khoangvan
				$dem = 0;
				$return = array();
				//$check = 0;
				//while ($dem <= $dem_step)
				//	{
					//echo 'dem = ' . $dem_step . '<br />';
					//die;
					// echo 'kc=';
					//for ($kc = 1; $kc <= $dem_step; $kc++)
						//{ // sua lai so 3
						// echo $kc.' ';
						// echo 'j = ';
						//if ($kc == $dem_step)
						//	{ // sua lai so 3
						//	$kctram = $total_distance;
						//	}
						 // else $kctram = $kc * $khoangvan;
						//$kctram_tru = ($kc - 1) * $khoangvan;
						$khoangcach = 0;
						$bandau = 0;
						for ($j = 0; $j < $dem_step; $j++)
							{
							//if ($leg->steps[$i]->distance->value == 0) continue;
							$khoangcach+= $leg->steps[$j]->distance->value;
							$bandau = $leg->steps[$j]->distance->value;
							//if ($khoangcach <= $kctram && $khoangcach > $kctram_tru)
							//	{
								$dem++;
								//$leg->steps[$i]->distance->value = 0;
								$toado_lat = $leg->steps[$j]->end_location->lat;
								$toado_lng = $leg->steps[$j]->end_location->lng;
								$diem = array(
									'lat' => $toado_lat,
									'lng' => $toado_lng
								);
								$return[] = $this->themdiemvaodske($dske, $diem, $from, $to, $bandau, $dem, $dem_step, $total_distance, $khoangcach);
								//if ($dem == $dem_step) break 2; // return khi da tim thay du
								//break;
							//	}
							}
						// echo 'khoang cach cuoi = '.$khoangcach;
						// flush();
						//if ($khoangcach == 0) break 2;
						//}
				//	}
				}
			}
		return;
		}
    function cheohaicanh_new(&$dske, $canh1, $canh2, $cotgiuacactram)
		{
		for ($i = 0; $i < $cotgiuacactram; $i++)
			{
            $lam = $cotgiuacactram - ($i + 1);
			$lat1 = $canh1[$i]['lat'];$lat2 = $canh2[$lam]['lat'];
			$lng1 = $canh1[$i]['lng'];$lng2 = $canh2[$lam]['lng'];
			$ten1 = $canh1[$i]['ten'];$ten2 = $canh2[$lam]['ten'];
			$from = array(
				'lat' => $lat1,
				'lng' => $lng1,
				'ten' => $ten1
			);
			$to = array(
				'lat' => $lat2,
				'lng' => $lng2,
				'ten' => $ten2
			);
			$url_direction = "https://maps.googleapis.com/maps/api/directions/json?origin=$lat1,$lng1&destination=$lat2,$lng2&mode=walking&key=AIzaSyD1pf16ahH7FsdbEum3W2VU-ybqTpME30w";
			$json = $this->curl_new->simple_get($url_direction);
			$data = json_decode($json);
			if ($data->status == 'OK')
				{
				$leg = $data->routes[0]->legs[0];
				$total_distance = $leg->distance->value;
				$dem_step = count($leg->steps);
                $bandau = 0;
				$khoangcach = 0; $dem_thoa = 0; 
                for($j = 0 ; $j < $dem_step-1 ; $j++){
                    $khoangcach+= $leg->steps[$j]->distance->value;
				    $bandau = $leg->steps[$j]->distance->value;
                    if( $khoangcach > 200){
                        $dem_thoa++;
                        $khoangcach = 0;
                    }
                }
                //$dem_step = $dem ;
				if (empty($dem_thoa))
					{
					   $this->return_st(1,'canh nay gia tri rong');
					} // return neu rỗng
				//$khoangvan = ceil($total_distance / $cotgiuacactram); // 0 < $khoangvan*1 < $khoangvan*2 < $khoangvan
				$dem = 0;
				$return[$i] = array();
						$khoangcach = 0;
						$bandau = 0;$khoangcachtinh=0;
						for ($j = 0; $j < $dem_step-1 ; $j++){
							$khoangcach+= $leg->steps[$j]->distance->value;
							$bandau = $leg->steps[$j]->distance->value;
                            $khoangcachtinh += $leg->steps[$j]->distance->value;
                            if( $khoangcach > 200){
                                $dem++;
								$toado_lat = $leg->steps[$j]->end_location->lat;
								$toado_lng = $leg->steps[$j]->end_location->lng;
								$diem = array(
									'lat' => $toado_lat,
									'lng' => $toado_lng
								);
								$return[$i][] = $this->themdiemvaodske_new($dske, $diem, $from, $to, $khoangcach, $dem, $dem_thoa, $total_distance, $khoangcachtinh);
                                $khoangcach = 0;
                            }
						}
				}
			}
		return $return;
		}
        public function kiemtrafile(){
            $user = $this->session->userdata('user');
            if(empty($user)) $this->return_st(1,'chưa có user');
            if(file_exists("$user.jso")) $this->return_st(1,'đã có file'); 
            else $this->return_st(0,'ko có file');
        }
    public function thucong_get(){
        //settype($mode);
        //if(empty($mode)) $this->return_st(1,'chọn mode');
        $this->load->library('Curl_new');
        $cotgiuacactram = 0;
        $user = $this->session->userdata('user');
        if(empty($user)) $this->return_st(1,'chưa có user');
        if(file_exists("$user.jso")) $this->return_st(1,'đã có file'); 
		$result = $this->db->where('user',$user)->order_by('idtd','asc')->get('toado')->result_array();
        if(empty($result)) $this->return_st(1,'ko lấy đc thông tin');
        $dem_result = count($result);
        if($dem_result<=8){
            $this->db->query('delete from toado where user = "'.$user.'"');
            $this->return_st(1,'phải chọn 8 toạ độ trở lên');
        } 
        $dske = array();
        $tongdiem= count($result)-1; // - trung tâm , 
        $TongDiemCanh = ( $tongdiem - 4 ) / 4 ; //  
        $tongmang = array();
        $tongmang[0] = 'ok';
        for($i = 1 ; $i<5;$i++ ){
            if($i == 1 ) $start = 5;
            else $start = $start + $TongDiemCanh;
            $tongmang[$i][] = $result[$start]; 
            $this->checkdiem($dske, $result[$start], $result[$i], $TongDiemCanh); // 1 5
            for($j = 0 ; $j < $TongDiemCanh-1 ; $j ++){//nho hon tongdiemcanh -1 vì chỉ lấy khoảng giữa ko lay dau cuoi
                $tongmang[$i][] =  $result[$start+$j+1];
                $this->checkdiem($dske, $result[$start+$j], $result[$start+$j+1], $TongDiemCanh);
            } // 56 67 
            if($i!=4) $this->checkdiem($dske, $result[$start+ ($TongDiemCanh-1) ], $result[$i+1], $TongDiemCanh); // 7 2 vì lấy thằng trước thằng start của cạnh 2 , nối với cạnh tiếp theo
            else $this->checkdiem($dske, $result[$start + ($TongDiemCanh-1)], $result[1], $TongDiemCanh);
        }
        $tmp = $dske;
        $chieudai = $this->checkdiem_new($tmp,$result[1],$result[2]);    
        $chieurong = $this->checkdiem_new($tmp,$result[2],$result[3]);
        if($chieudai >= $chieurong){
            $dsDiem = $this->cheohaicanh_new($dske,$tongmang[1],$tongmang[3],$TongDiemCanh);
            $this->NoiCacDiem($dske,$tongmang[2],$tongmang[4],$dsDiem,$TongDiemCanh);
        }
        else{
            $dsDiem = $this->cheohaicanh_new($dske,$tongmang[2],$tongmang[4],$TongDiemCanh);
            $this->NoiCacDiem($dske,$tongmang[1],$tongmang[3],$dsDiem,$TongDiemCanh);
        }
        $return['data'] = $dske;
		$return['trungtam'] = $result[0];
		$return['error'] = 0;
		$return['reason'] = 'load toạ độ thành công';
		$json = json_encode($return);
        $tenfile = $this->session->userdata('user');
        //file_put_contents($tenfile.'.jso', $json);
        $file = @fopen($tenfile.'.jso','w+');
        if($file){
            fwrite($file,$json);
            fclose($file);
            $this->return_st(0,'tạo file thành công');
        }else{
            $this->return_st(1,'ko mở file đc');
        }
        
    }
    public function NoiCacDiem(&$dske,$canh1,$canh2,$dsDiem , $TongDiemCanh){
        for($i = 0 ; $i< $TongDiemCanh ; $i++){
            $lam = $TongDiemCanh - ($i + 1);
            for($j = 0; $j < $TongDiemCanh ; $j++){
                $diem = array();
                if(isset($dsDiem[$j][$i])){
                    $arr1 = explode(",", $dsDiem[$j][$i][0]);
                    if(empty($arr1)) $this->return_st(1,"co 1 canh ds rong");
                    $diem['lat'] = $arr1[0];
                    $diem['lng'] = $arr1[1];
                    $diem['ten'] = $arr1[2];  
                    if($j == 0 ){
                        // noi no voi canh1 
                        //$this->checkdiem_new($dske,$canh1[$i],$diem);
                        $this->noiall($dske,$canh1[$i],$dsDiem[$j]);
                        if(isset($dsDiem[$j+1])){
                            $this->noiall($dske,$diem,$dsDiem[$j+1]);
                        }
                    }elseif($j == $TongDiemCanh - 1){//neu j gần đến cuối
                        //nối nó với cạnh2 cạnh cuối
                        //$this->checkdiem_new($dske,$diem,$canh2[$lam]);
                        $this->noiall($dske,$canh2[$lam],$dsDiem[$j]);
                    }else{ // nối các điem voi nhau (nối nó với điểm tiếp theo)
                        if(isset($dsDiem[$j+1])){
                            $this->noiall($dske,$diem,$dsDiem[$j+1]);
                        }
                        
                    }
                }
            }
            
        }
    }
    public function noiall(&$dske,$diem,$canh){
        $dem = count($canh);
        for($i = 0 ; $i< $dem ; $i++){
            if(isset($canh[$i][0])){
                $arr1 = explode(",", $canh[$i][0]);
                if(empty($arr1)) $this->return_st(1,"canh diem tiep theo rong");
                $diemtieptheo['lat'] = $arr1[0];
                $diemtieptheo['lng'] = $arr1[1];
                $diemtieptheo['ten'] = $arr1[2];
                $this->checkdiem_new($dske,$diem,$diemtieptheo);
            }else{
                print_r($canh);
                flush();
            }
        }
    }
	public function kruskal()
		{
		// yeu cau cạnh ab // cd
		// ac // bd
		$this->load->library('Curl_new');
		// $kctram = $this->input->post('kctram'); // don vi la m
		// $kcnha = $this->input->post('kcnha');// don vi la m
		$cotgiuacactram = 0;
		$result = $this->db->order_by('ten','asc')->limit(5)->get('toado')->result_array();
        
		// đầu tiên lấy khoảng cách
		// $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Washington,DC&destinations=New+York+City,NY&key=YOUR_API_KEY';
		if (count($result) != 5) die('not submit'); $tongmang = array();
		$dske = array();
		$d0 = $result[1];
		$d1 = $result[2];
		$d2 = $result[3];
		$d3 = $result[4];
		/*$c1 = $this->checkdiem($dske, $d0, $d1, $cotgiuacactram);
		$c2 = $this->checkdiem($dske, $d1, $d2, $cotgiuacactram);
		$c3 = $this->checkdiem($dske, $d2, $d3, $cotgiuacactram);
		$c4 = $this->checkdiem($dske, $d3, $d0, $cotgiuacactram);*/
        $tongmang[] = $this->checkdiem($dske, $d0, $d1, $cotgiuacactram);
		$tongmang[] = $this->checkdiem($dske, $d1, $d2, $cotgiuacactram);
		$tongmang[] = $this->checkdiem($dske, $d2, $d3, $cotgiuacactram);
		$tongmang[] = $this->checkdiem($dske, $d3, $d0, $cotgiuacactram);
        $min = count($tongmang[0]->routes[0]->legs[0]->steps);
        for($i = 1 ; $i < 4 ; $i++){
            $leg = $tongmang[$i]->routes[0]->legs[0];
            $dem_step = count($leg->steps);
            if($dem_step < $min){
                $min = $dem_step;
            }
        }
        $cotgiuacactram = $min;
		/*$dsc1 = $this->themcacdiemvaomotcanh($dske, $c1, $cotgiuacactram, $d0, $d1);
		$dsc2 = $this->themcacdiemvaomotcanh($dske, $c2, $cotgiuacactram, $d1, $d2);
		$dsc3 = $this->themcacdiemvaomotcanh($dske, $c3, $cotgiuacactram, $d2, $d3);
		$dsc4 = $this->themcacdiemvaomotcanh($dske, $c4, $cotgiuacactram, $d3, $d0);*/
        for($i = 0 ; $i<4;$i++){
            $d1 = $i+1; $d2 = $i+2;
            if($i == 3) $d2 = 1;
            $dsc[] = $this->themcacdiemvaomotcanh($dske, $tongmang[$i], $cotgiuacactram, $result[$d1], $result[$d2]);
        }
        for($i = 0 ; $i<4;$i=$i+2){
            $this->cheohaicanh($dske, $dsc[$i], $dsc[$i+1], $cotgiuacactram);
        }
		$return['data'] = $dske;
		$return['trungtam'] = $result[0];
		$return['error'] = 0;
		$return['reason'] = 'load toạ độ thành công';
		//$this->cheohaicanh($dske, $dsc1, $dsc2, $cotgiuacactram);$this->cheohaicanh($dske, $dsc3, $dsc4, $cotgiuacactram);print_r($dske);die;
		$json = json_encode($return);
        file_put_contents('json.jso', $json);
        echo 'tạo file thành công';
		}
    public function showdata(){
        $user = $this->session->userdata('user');
        if(empty($user)) $this->return_st(1,'chưa có user');
        if(!file_exists("$user.jso")) $this->return_st(1,'chưa có file');
        $file = '';
            $handle = fopen("$user.jso", "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $file .= $line;
                }
                fclose($handle);
            } else {
                $this->return_st(1,'chưa mở được file');
            } 
        if(empty($file)) $this->return_st(1,'chưa có dữ liệu');
        echo $file;
    }
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
                $str = str_replace("/", "", $str);
                $str = str_replace("$", "", $str);
                $str = str_replace("\"", "", $str);
                $str = str_replace("\\", "", $str);
                while (strpos($str, '  ')) {
                    $str = str_replace('  ', ' ', $str);
                }
                $str = mb_convert_case($str, MB_CASE_LOWER, 'utf-8');
                $str = str_replace(" ", "", $str);
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
	}
?>