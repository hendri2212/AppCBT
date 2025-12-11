<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Tes_hasil extends Member_Controller {
	private $kode_menu = 'tes-hasil';
	private $kelompok = 'tes';
	private $url = 'manager/tes_hasil';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_user_model');
		$this->load->model('cbt_user_grup_model');
		$this->load->model('cbt_tes_model');
		$this->load->model('cbt_tes_token_model');
		$this->load->model('cbt_tes_topik_set_model');
		$this->load->model('cbt_tes_user_model');
		$this->load->model('cbt_tesgrup_model');
		$this->load->model('cbt_soal_model');
		$this->load->model('cbt_jawaban_model');
		$this->load->model('cbt_tes_soal_model');
		$this->load->model('cbt_tes_soal_jawaban_model');

        parent::cek_akses($this->kode_menu);
	}
	
    public function index($page=null, $id=null){
        $data['kode_menu'] = $this->kode_menu;
        $data['url'] = $this->url;

        $tanggal_awal = date('Y-m-d H:i', strtotime('- 1 days'));
        $tanggal_akhir = date('Y-m-d H:i', strtotime('+ 1 days'));
        
        $data['rentang_waktu'] = $tanggal_awal.' - '.$tanggal_akhir;

        $query_group = $this->cbt_user_grup_model->get_group();
        $select = '<option value="semua">Semua Group</option>';
        if($query_group->num_rows()>0){
        	$query_group = $query_group->result();
        	foreach ($query_group as $temp) {
        		$select = $select.'<option value="'.$temp->grup_id.'">'.$temp->grup_nama.'</option>';
        	}

        }else{
        	$select = '<option value="0">Tidak Ada Group</option>';
        }
        $data['select_group'] = $select;

        $query_tes = $this->cbt_tes_user_model->get_by_group();
        $select = '<option value="semua">Semua Tes</option>';
        if($query_tes->num_rows()>0){
        	$query_tes = $query_tes->result();
        	foreach ($query_tes as $temp) {
        		$select = $select.'<option value="'.$temp->tes_id.'">'.$temp->tes_nama.'</option>';
        	}
        }
        $data['select_tes'] = $select;
        
        $this->template->display_admin($this->kelompok.'/tes_hasil_view', 'Hasil Tes', $data);
    }

    /**
     * Melakukan perubahan pada tes yang diseleksi
     */
    function edit_tes(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('edit-testuser-id[]', 'Hasil Tes','required|strip_tags');
        $this->form_validation->set_rules('edit-pilihan', 'Pilihan','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $pilihan = $this->input->post('edit-pilihan', true);
            $tesuser_id = $this->input->post('edit-testuser-id', TRUE);

            if($pilihan=='hapus'){
                foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                        // Get all tessoal_id related to this tesuser before deleting
                        $query_tessoal = $this->cbt_tes_soal_model->get_by_kolom('tessoal_tesuser_id', $kunci);
                        if($query_tessoal->num_rows() > 0){
                            foreach($query_tessoal->result() as $tessoal){
                                // Delete from cbt_tes_soal_jawaban first (child table)
                                $this->cbt_tes_soal_jawaban_model->delete('soaljawaban_tessoal_id', $tessoal->tessoal_id);
                            }
                        }
                        // Delete from cbt_tes_soal
                        $this->cbt_tes_soal_model->delete('tessoal_tesuser_id', $kunci);
                        // Finally delete from cbt_tes_user (parent table)
                        $this->cbt_tes_user_model->delete('tesuser_id', $kunci);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Hasil tes berhasil dihapus';
            }else if($pilihan=='hentikan'){
            	foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                    	$data_tes['tesuser_status']=4;
            			$this->cbt_tes_user_model->update('tesuser_id', $kunci, $data_tes);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Tes berhasil dihentikan';
            }else if($pilihan=='buka'){
            	foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                    	$data_tes['tesuser_status']=1;
            			$this->cbt_tes_user_model->update('tesuser_id', $kunci, $data_tes);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Tes berhasil dibuka, user bisa mengerjakan kembali';
            }else if($pilihan=='waktu'){
            	foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                    	$waktu = intval($this->input->post('waktu-menit', TRUE));

            			$this->cbt_tes_user_model->update_menit($kunci, $waktu);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Waktu Tes berhasil ditambah';
            }

        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }

    function export($tes_id=null, $grup_id=null, $waktu=null, $urutkan=null, $status=null, $keterangan=null){
        if(!empty($tes_id) AND !empty($grup_id) AND !empty($waktu) AND !empty($urutkan) AND !empty($status)){
            // $this->load->library('excel');

            $waktu =  urldecode($waktu);
            $tanggal = explode(" - ", $waktu);
			if(!empty($keterangan)){
				$keterangan =  urldecode($keterangan);
			}

			if($status=='mengerjakan'){
				$query = $this->cbt_tes_user_model->get_by_tes_group_urut_tanggal($tes_id, $grup_id, $urutkan, $tanggal, $keterangan);
			}else{
				$query = $this->cbt_user_model->get_by_tes_group_urut_tanggal($tes_id, $grup_id, $urutkan, $tanggal, $keterangan);
			}
            // $inputFileName = './public/form/form-data-hasil-tes.xls';
            // $excel = PHPExcel_IOFactory::load($inputFileName);
            // $worksheet = $excel->getSheet(0);
			$hasData = $query->num_rows()>0;
			if($hasData){
				$data = $query->row();
				$tesNama = strtoupper($data->tes_nama);
			}else{
				$tesNama = 'DATA TIDAK DITEMUKAN';
			}
			$excel		= new Spreadsheet();
			$align		= new Alignment();
			$drawing	= new Drawing();
			$border		= new Border();

			$styleArray = [
				'borders' => [
					// 'outline' => [
					'allBorders' => [
						// 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
						'borderStyle' => $border::BORDER_THIN,
						'color' => ['argb' => '0000'],
					],
				],
			];
			
			// $worksheet->getStyle('B2:G8')->applyFromArray($styleArray);

			$myfile = fopen("./public/info.txt", "r") or die("Unable to open file!");

			$worksheet	= $excel->getActiveSheet();
			$worksheet->setCellValueByColumnAndRow(1, 1, strtoupper(fgets($myfile)));
			$worksheet->mergeCells('A1:F1');
			$worksheet->getStyle('A1:F1')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
			$worksheet->getStyle('A1:F1')->getFont()->setBold(true);
			$worksheet->getStyle('A1:F1')->getFont()->setSize(12);
			$worksheet->getStyle('A4:F39')->applyFromArray($styleArray);
			// $worksheet->getDefaultColumnDimension()->setWidth(32);
			$worksheet->getColumnDimension('A')->setWidth(5);
			$worksheet->getColumnDimension('B')->setWidth(20);
			$worksheet->getColumnDimension('D')->setWidth(20);
			$worksheet->getColumnDimension('F')->setWidth(25);

			
			$worksheet->setCellValueByColumnAndRow(1, 2, $tesNama);
			$worksheet->mergeCells('A2:F2');
			$worksheet->getStyle('A2:F2')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
			$worksheet->getStyle('4')->getFont()->setBold(true);
			$worksheet->getStyle('A')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
			$worksheet->getStyle('C')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
			$worksheet->getStyle('E')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);

			$worksheet->setCellValueByColumnAndRow(1, 4, 'No');
			$worksheet->setCellValueByColumnAndRow(2, 4, 'Tanggal');
			$worksheet->setCellValueByColumnAndRow(3, 4, 'Kelas');
			$worksheet->setCellValueByColumnAndRow(4, 4, 'Nama Siswa');
			$worksheet->setCellValueByColumnAndRow(5, 4, 'Nilai');
			$worksheet->setCellValueByColumnAndRow(6, 4, 'Kriteria Penilaian');

			fclose($myfile);

			// // Add image logo to header letter
			// $drawing->setName('Gibli Chromo');
			// $drawing->setDescription('Logo');
			// $drawing->setPath('./public/images/avatar.png');
			// $drawing->setCoordinates('A1');
			// $drawing->setHeight(36);
			// // $drawing->setOffsetX(110);
			// // $drawing->setRotation(25);
			// // $drawing->getShadow()->setVisible(true);
			// // $drawing->getShadow()->setDirection(45);
			// $drawing->setWorksheet($excel->getActiveSheet());


            if($hasData){
                $query = $query->result();
                $row = 5;
                foreach ($query as $temp) {
                    $worksheet->setCellValueByColumnAndRow(1, $row, ($row-4));
                    $worksheet->setCellValueByColumnAndRow(2, $row, $temp->tesuser_creation_time);
                    $worksheet->setCellValueByColumnAndRow(3, $row, $temp->grup_nama);
                    // $worksheet->setCellValueByColumnAndRow(3, $row, $temp->tes_nama);
                    // $worksheet->setCellValueByColumnAndRow(4, $row, $temp->user_name);
                    $worksheet->setCellValueByColumnAndRow(4, $row, stripslashes($temp->user_firstname));
                    $worksheet->setCellValueByColumnAndRow(5, $row, $temp->nilai);
                    $worksheet->setCellValueByColumnAndRow(6, $row, $temp->kriteria);

                    $row++;
                }
            }
            $filename='Data Hasil Tes - '.date('Y-m-d H:i').'.xlsx'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
                 
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            // $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
			$objWriter = new Xlsx($excel);
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        }
    }
    
    function get_datatable(){
		// variable initialization
		$tes_id = $this->input->get('tes');
		$grup_id = $this->input->get('group');
		$urutkan = $this->input->get('urutkan');
		$waktu = $this->input->get('waktu');
		$keterangan = $this->input->get('keterangan');
		$status = $this->input->get('status');
		$tanggal = explode(" - ", $waktu);

		$search = "";
		$start = 0;
		$rows = 10;

		// get search value (if any)
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$search = $_GET['sSearch'];
		}

		// limit
		$start = $this->get_start();
		$rows = $this->get_rows();

		// run query to get user listing
		if($status=='mengerjakan'){
			$query = $this->cbt_tes_user_model->get_datatable($start, $rows, $tes_id, $grup_id, $urutkan, $tanggal, $keterangan, $search);
			$iTotal= $this->cbt_tes_user_model->get_datatable_count($tes_id, $grup_id, $urutkan, $tanggal, $keterangan, $search)->row()->hasil;
		}else{
			$query = $this->cbt_user_model->get_datatable_hasiltes($start, $rows, $tes_id, $grup_id, $urutkan, $tanggal, $keterangan, $search);
			$iTotal= $this->cbt_user_model->get_datatable_hasiltes_count($tes_id, $grup_id, $urutkan, $tanggal, $keterangan, $search)->row()->hasil;
		}
		
		$iFilteredTotal = $query->num_rows();
	    
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
	        "iTotalRecords" => $iTotal,
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );

	    // get result after running query and put it in array
		$i=$start;
		$query = $query->result();
	    foreach ($query as $temp) {			
			$record = array();
            
			$record[] = ++$i;
			if(empty($temp->tesuser_creation_time)){
				$record[] = 'Belum memulai tes';
				$record[] = '0';
			}else{
				$record[] = $temp->tesuser_creation_time;
				$record[] = $temp->tes_duration_time.' menit';
			}
			$record[] = $temp->tes_nama;
            $record[] = $temp->grup_nama;
			if(empty($temp->tesuser_id)){
				$record[] = '<b>'.stripslashes($temp->user_firstname).'</b>';
			}else{
				$record[] = '<a href="#" title="Klik untuk mengetahui Detail Tes" onclick="detail_tes(\''.$temp->tesuser_id.'\')"><b>'.stripslashes($temp->user_firstname).'</b></a>';
			}
			if(empty($temp->nilai)){
				$record[] = '0';
			}else{
				$record[] = $temp->nilai;
			}
			
			if(empty($temp->tesuser_status)){
				$record[] = 'Belum memulai';
			}else{
				if($temp->tesuser_status==1){
					$tanggal = new DateTime();
					// Cek apakah tes sudah melebihi batas waktu
					$tanggal_tes = new DateTime($temp->tesuser_creation_time);
					$tanggal_tes->modify('+'.$temp->tes_duration_time.' minutes');
					if($tanggal>$tanggal_tes){
						$record[] = 'Selesai';
					}else{
						$tanggal = $tanggal_tes->diff($tanggal);
						$menit_sisa = ($tanggal->h*60)+($tanggal->i);
						$record[] = 'Berjalan (-'.$menit_sisa.' menit)';
					}
				}else{
					$record[] = 'Selesai';
				}
			}
			
			// menampilkan pilihan edit untuk data yang sudah mengerjakan
			if(empty($temp->tesuser_id)){
				$record[] = '';
			}else{
				$record[] = '<input type="checkbox" name="edit-testuser-id['.$temp->tesuser_id.']" >';
			}

			$output['aaData'][] = $record;
		}
		// format it to JSON, this output will be displayed in datatable
        
		echo json_encode($output);
	}
	
	/**
	* funsi tambahan 
	* 
	* 
*/
	
	function get_start() {
		$start = 0;
		if (isset($_GET['iDisplayStart'])) {
			$start = intval($_GET['iDisplayStart']);

			if ($start < 0)
				$start = 0;
		}

		return $start;
	}

	function get_rows() {
		$rows = 10;
		if (isset($_GET['iDisplayLength'])) {
			$rows = intval($_GET['iDisplayLength']);
			if ($rows < 5 || $rows > 500) {
				$rows = 10;
			}
		}

		return $rows;
	}

	function get_sort_dir() {
		$sort_dir = "ASC";
		$sdir = strip_tags($_GET['sSortDir_0']);
		if (isset($sdir)) {
			if ($sdir != "asc" ) {
				$sort_dir = "DESC";
			}
		}

		return $sort_dir;
	}
}