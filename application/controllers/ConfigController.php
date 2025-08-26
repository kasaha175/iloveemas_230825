<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConfigController extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('UserModel');
        $this->load->model('ConfigModel');
        $this->load->helper(['url', 'form']);
        date_default_timezone_set('Asia/Jakarta');
    }

    private function must_admin(){
        if ($this->session->userdata('authUser') !== true) redirect(base_url('login'));
        $id = $this->session->userdata('idUser');
        $u  = $this->UserModel->userDataById($id)->row();
        if (!$u || strtolower($u->u_rule) !== 'administrator') show_404(); // atau redirect
        return $u;
    }

    public function index(){
        $u = $this->must_admin();
        $data['title']     = 'Konfigurasi';
        $data['userData']  = [$u];
        $data['config']    = $this->ConfigModel->getAllAssoc();
        $data['sidebar']   = $this->load->view('Sidebar', $data, true);
        $data['content']   = $this->load->view('ConfigForm', $data, true);
        $this->load->view('UserTemplate', $data);
    }

    public function save(){
        $u = $this->must_admin();
        if ($this->input->method(TRUE) !== 'POST') return redirect(base_url('config'));

        // text fields
        $payload = [
            'app_name'      => trim($this->input->post('app_name', true)),
            'app_version'   => trim($this->input->post('app_version', true)),
            'color_primary' => trim($this->input->post('color_primary', true)),
            'color_secondary'=>trim($this->input->post('color_secondary', true)),
            'color_pastel'  => trim($this->input->post('color_pastel', true)),
        ];

        // uploads
        $uploads = [
            'logo'           => ['types'=>'png|jpg|jpeg|webp|svg','name'=>'logo'],
            'favicon'        => ['types'=>'png|ico','name'=>'favicon'],
            'bg_login'       => ['types'=>'png|jpg|jpeg|webp','name'=>'bg_login'],
            'bg_dashboard'   => ['types'=>'png|jpg|jpeg|webp','name'=>'bg_dashboard'],
        ];
        foreach ($uploads as $field => $opt){
            $path = $this->_handle_upload($field, $opt['types'], $opt['name']);
            if ($path) $payload[$field] = $path; // simpan relative path
        }

        $this->ConfigModel->setBatch($payload);
        $this->session->set_flashdata('success', 'Konfigurasi disimpan.');
        return redirect(base_url('config'));
    }

    private function _handle_upload($field, $allowed, $prefix){
        if (empty($_FILES[$field]['name'])) return null;
        $dir = FCPATH.'assets/img/config/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $config = [
            'upload_path'   => $dir,
            'allowed_types' => $allowed,
            'max_size'      => 2048, // KB
            'file_name'     => $prefix.'-'.date('YmdHis'),
            'overwrite'     => true,
        ];
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($field)) {
            log_message('error', 'Upload error '.$field.': '.$this->upload->display_errors('',''));
            return null;
        }
        $data = $this->upload->data();
        // return relative path for use in views
        return 'assets/img/config/'.$data['file_name'];
    }
}
