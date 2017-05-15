<?php

defined('BASEPATH') OR exit('No direct script access allowed');



require APPPATH . '/libraries/REST_Controller.php';



class Api extends REST_Controller {



	public function __construct()

	{

        header('Access-Control-Allow-Origin: *');

        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        $method = $_SERVER['REQUEST_METHOD'];

        if($method == "OPTIONS") {die('not access options method contact admin');}

        parent::__construct();

        $this->load->library('curl');

	}

    /**

     * Table Name : api

     * GET, POST, PUT, DELETE

     **/

     private function return_st($error,$reason){

        $return['error'] = $error; $return['reason'] = $reason; $this->response($return);

     }
    function checkkruskal_get(){
        $user = $this->session->userdata('user');
        if(empty($user)) $this->return_st(1,'chưa có user');
        $result = $this->db->where('user',$user)->get('toado')->result_array();
        if(empty($result)) $this->return_st(0,'chưa có toạ độ');
        $this->return_st(1,'đã có toạ độ bạn nên xoá toạ độ trước , hoặc xem lại');

    }

    function uplatlng_post(){
        $lat = $this->post('lat');
        $lng = $this->post('lng');
        $ten = $this->post('ten');
        $user = $this->session->userdata('user');
        if(empty($user)) $this->return_st(1,'chưa có user');
        $data = array('lat'=>$lat,'lng'=>$lng,'ten'=>$ten,'user'=>$user);
        $kq = $this->db->insert('toado',$data);
        if(!$kq) $this->return_st(1,'chưa thêm được');
        $this->return_st(0,'thêm thành công');
    }

    function xoatotaltoado_post(){
        $key = $this->post('key');
        $user = $this->session->userdata('user');
        if(empty($user)) $this->return_st(1,'chưa có user');
        if($key == 'toilalam'){ 
            $kq = $this->db->query('delete from toado where user = "'.$user.'"');
            //$this->db->query('ALTER TABLE toado AUTO_INCREMENT = 1');
            if(file_exists("$user.jso")) unlink($user.'.jso');
            $this->return_st(0,'xoa thanh cong');
        }
        $this->return_st(1,'sai key');
    }

    //////////////////////////////////////////

    //////////////////////////////////////////

    //////////////////////////////////////////

    //////////////////////////////////////////

	/*function user_get()

    {



        if (!$this->get('id'))

        {

            $apiAll = $this->db->get('api')->result();

            $this->response($apiAll, 200);

        }

        elseif ($this->get('id')) {

            $this->db->where('id_api', $this->get('id'));

            $apiGet = $this->db->get('api')->row();

            $this->response($apiGet, 200);

        }

        else {

            $this->response(['error' => 'Api could not be found'], 404);

        }

    }



    function user_post()

    {

        $api = array(

                        'name' => $this->post('name'),

                        'email' => $this->post('email')

                    );



        if ($this->post('name') && $this->post('email')) {

            $this->db->insert('api', $api);

            $message['message'] = 'Success POST';

            $this->response(['message' => 'Success POST'], 201);

        }

        else {

            $this->response(['error' => 'Api could not be found'], 404);

        }



        $this->response($message, 201);

    }



    function user_put()

    {

        $api = array(

                        'name' => $this->put('name'),

                        'email' => $this->put('email')

                    );



        if ($this->get('id')) {

            $this->db->where('id_api', $this->get('id'));

            $this->db->update('api', $api);

            $this->response(['message' => 'Success PUT'], 201);

        }

        else {

            $this->response(['error' => 'Api could not be found'], 404);

        }

    }



    function user_delete()

    {

        if ($this->get('id')) {

            $this->db->where('id_api', $this->get('id'));

            $this->db->delete('api');

            $this->response(['message' => 'Success DELETE'], 201);

        }

        else {

            $this->response(['error' => 'Api could not be found'], 404);

        }

    }*/

}



/* End of file Api.php */

/* Location: ./application/controllers/Api.php */