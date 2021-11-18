<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Peminjam extends CI_Controller {
	
	function __construct(){
		parent::__construct();
        $this->load->model('model_barang');
            if($this->session->userdata('status') != "login"){
                redirect(site_url("login"));}
		
	}
 
	function index(){
		$this->load->view('template/header');
		$this->load->view('template/sidebar');
		$this->load->view('peminjam/view_beranda');
		$this->load->view('template/footer');
	}

    function list(){
        $data['tb_barang'] = $this->model_barang->list()->result();
        $this->load->view('template/header');
		$this->load->view('template/sidebar');
		$this->load->view('peminjam/view_barang', $data);
		$this->load->view('template/footer');
    }

	function add($id){
		$where = array('id_barang' => $id);
        $data['tb_barang'] = $this->model_barang->pinjam($where,'tb_barang')->result();
        $this->load->view('template/header');
		$this->load->view('template/sidebar');
		$this->load->view('peminjam/view_peminjaman', $data);

    }

	function save()
    {   
		$username = $this->session->userdata('username');
        $this->db->select('username, id');
        $this->db->where('username', $username);//
        $this->db->from('tb_user');
        $query = $this->db->get()->row();
     // return print($username);
        
        $id = $query->id;
        $id_barang = $this->input->post('id_barang');
        $jumlah_pinjam = $this->input->post('jumlah_pinjam');
        $tanggal_pinjam = $this->input->post('tanggal_pinjam');
        $status = "Di booking";
        $kondisi_saat_pinjam = $this->input->post('kondisi_saat_pinjam');
        
        $data = array (
            'id_user' => $id,
            'id_barang' => $id_barang,
            'jumlah_pinjam' => $jumlah_pinjam,
            'tanggal_pinjam' => $tanggal_pinjam,
            'status' => $status,
            'kondisi_saat_pinjam' => $kondisi_saat_pinjam        
            ); 

        $this->db->select('stok, id_barang');
        $this->db->where('id_barang', $id_barang);//
        $this->db->from('tb_barang');
        $query1 = $this->db->get()->row();
        
        $oldStok = $query1->stok;
        $newStok = $oldStok - $jumlah_pinjam;

        $stok = array (
            'stok' => $newStok
        );

        $where = array (
            'id_barang' => $id_barang
        );

        $this->model_barang->save($data, 'tb_peminjaman');
        $this->model_barang->update($where, $stok, 'tb_barang');
        redirect('peminjam/list');
    }

 
		
	
 
}