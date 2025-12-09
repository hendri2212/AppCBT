<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Tes_evaluasi extends Member_Controller {
	private $kode_menu = 'tes-evaluasi';
	private $kelompok = 'tes';
	private $url = 'manager/tes_evaluasi';
	
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

        $query_tes = $this->cbt_tes_user_model->get_by_group();
        $select = '';
        if($query_tes->num_rows()>0){
        	$query_tes = $query_tes->result();
        	foreach ($query_tes as $temp) {
        		$select = $select.'<option value="'.$temp->tes_id.'">'.$temp->tes_nama.'</option>';
        	}
        }
        $data['select_tes'] = $select;
        
        $this->template->display_admin($this->kelompok.'/tes_evaluasi_view', 'Evaluasi Jawaban', $data);
    }

    function simpan_nilai(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('evaluasi-testlog-id', 'Soal','required|strip_tags');
        $this->form_validation->set_rules('evaluasi-nilai-min', 'Nilai Minimal','required|decimal|strip_tags');
        $this->form_validation->set_rules('evaluasi-nilai-max', 'Nilai Maximal','required|decimal|strip_tags');
        $this->form_validation->set_rules('evaluasi-nilai', '','required|numeric|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $nilai = $this->input->post('evaluasi-nilai', TRUE);
            $nilai_min = $this->input->post('evaluasi-nilai-min', TRUE);
            $nilai_max = $this->input->post('evaluasi-nilai-max', TRUE);
            $tessoal_id = $this->input->post('evaluasi-testlog-id', TRUE);

            if($nilai>=$nilai_min AND $nilai<=$nilai_max){
                $data['tessoal_nilai'] = $nilai;
                $data['tessoal_comment'] = 'Sudah di koreksi '.$this->access->get_username();

                $this->cbt_tes_soal_model->update('tessoal_id', $tessoal_id, $data);

                $status['status'] = 1;
                $status['pesan'] = 'Nilai berhasil disimpan ';
            }else{
                $status['status'] = 0;
                $status['pesan'] = 'Nilai tidak boleh dibawah Nilai Minimal dan di atas Nilai Maximal !';
            }
        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
    
    /**
     * Mendapatkan soal dan jawaban berdasarkan tessoal_id
     *
     * @param      <type>  $id     The identifier
     */
    function get_by_id($id=null){
    	$data['data'] = 0;
		if(!empty($id)){
			$query = $this->cbt_modul_model->get_by_kolom('modul_id', $id);
			if($query->num_rows()>0){
				$query = $query->row();
				$data['data'] = 1;
				$data['id'] = $query->modul_id;
				$data['modul'] = $query->modul_nama;
				$data['status'] = $query->modul_aktif;
			}
		}
		echo json_encode($data);
    }
    
    function get_datatable(){
		// variable initialization
		$tes_id = $this->input->get('tes');
		$urutkan = $this->input->get('urutkan');

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
		$query = $this->cbt_tes_user_model->get_datatable_evaluasi($start, $rows, $tes_id, $urutkan);
		$iFilteredTotal = $query->num_rows();
		
		$iTotal= $this->cbt_tes_user_model->get_datatable_evaluasi_count($tes_id, $urutkan)->row()->hasil;
	    
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

            $soal = $temp->soal_detail;
            $soal = str_replace("[base_url]", base_url(), $soal);
            
			$record[] = ++$i;
			
			// Nama siswa dan kelas
			$nama_siswa = isset($temp->user_firstname) && $temp->user_firstname ? $temp->user_firstname : '-';
			$record[] = $nama_siswa;
			$record[] = isset($temp->grup_nama) && $temp->grup_nama ? $temp->grup_nama : '-';
			
            $record[] = $soal;
			// $record[] = '<div style="width:600px;"><pre style="white-space: pre-wrap;word-wrap: break-word;">'.$temp->tessoal_jawaban_text.'</pre></div>';

			$jawaban = $temp->tessoal_jawaban_text;
			// Menambah tag br untuk baris baru
			$jawaban = str_replace("\r","<br />",$jawaban);
			$jawaban = str_replace("\n","<br />",$jawaban);
			
			$record[] = $jawaban;
			
            $record[] = '<a onclick="evaluasi(\''.$temp->tessoal_id.'\',\''.$temp->tes_score_wrong.'\',\''.$temp->tes_score_right.'\')" style="cursor: pointer;" class="btn btn-default btn-xs">Evaluasi</a>';
            

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

	/**
	 * Export data evaluasi jawaban ke Excel
	 */
	function export_excel($tes_id = null, $urutkan = 'soal'){
		if(empty($tes_id)){
			show_error('Parameter tes_id diperlukan');
			return;
		}

		// Get all data without limit
		$query = $this->cbt_tes_user_model->get_all_evaluasi($tes_id, $urutkan);

		if($query->num_rows() == 0){
			show_error('Tidak ada data untuk di-export');
			return;
		}

		// Get test name for filename
		$tes_data = $this->cbt_tes_model->get_by_kolom('tes_id', $tes_id);
		$tes_nama = 'Evaluasi';
		if($tes_data->num_rows() > 0){
			$tes_nama = $tes_data->row()->tes_nama;
		}

		$excel = new Spreadsheet();
		$align = new Alignment();
		$border = new Border();

		$styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => $border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];

		$worksheet = $excel->getActiveSheet();
		
		// Title
		$worksheet->setCellValueByColumnAndRow(1, 1, 'EVALUASI JAWABAN ESSAY');
		$worksheet->mergeCells('A1:E1');
		$worksheet->getStyle('A1:E1')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
		$worksheet->getStyle('A1:E1')->getFont()->setBold(true);
		$worksheet->getStyle('A1:E1')->getFont()->setSize(14);

		$worksheet->setCellValueByColumnAndRow(1, 2, strtoupper($tes_nama));
		$worksheet->mergeCells('A2:E2');
		$worksheet->getStyle('A2:E2')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
		$worksheet->getStyle('A2:E2')->getFont()->setBold(true);

		// Column widths
		$worksheet->getColumnDimension('A')->setWidth(5);
		$worksheet->getColumnDimension('B')->setWidth(25);
		$worksheet->getColumnDimension('C')->setWidth(15);
		$worksheet->getColumnDimension('D')->setWidth(50);
		$worksheet->getColumnDimension('E')->setWidth(50);

		// Headers
		$worksheet->setCellValueByColumnAndRow(1, 4, 'No');
		$worksheet->setCellValueByColumnAndRow(2, 4, 'Nama Siswa');
		$worksheet->setCellValueByColumnAndRow(3, 4, 'Kelas');
		$worksheet->setCellValueByColumnAndRow(4, 4, 'Soal');
		$worksheet->setCellValueByColumnAndRow(5, 4, 'Jawaban');
		$worksheet->getStyle('4')->getFont()->setBold(true);
		$worksheet->getStyle('A4:E4')->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);

		// Data rows
		$query_result = $query->result();
		$row = 5;
		$no = 1;
		foreach ($query_result as $temp) {
			$soal = strip_tags($temp->soal_detail);
			$soal = str_replace("[base_url]", "", $soal);
			
			$jawaban = $temp->tessoal_jawaban_text;
			
			$nama_siswa = isset($temp->user_firstname) && $temp->user_firstname ? $temp->user_firstname : '-';
			$kelas = isset($temp->grup_nama) && $temp->grup_nama ? $temp->grup_nama : '-';

			$worksheet->setCellValueByColumnAndRow(1, $row, $no);
			$worksheet->setCellValueByColumnAndRow(2, $row, $nama_siswa);
			$worksheet->setCellValueByColumnAndRow(3, $row, $kelas);
			$worksheet->setCellValueByColumnAndRow(4, $row, $soal);
			$worksheet->setCellValueByColumnAndRow(5, $row, $jawaban);

			$row++;
			$no++;
		}

		// Apply borders to data area
		$lastRow = $row - 1;
		$worksheet->getStyle('A4:E'.$lastRow)->applyFromArray($styleArray);
		$worksheet->getStyle('A4:A'.$lastRow)->getAlignment()->setHorizontal($align::HORIZONTAL_CENTER);
		
		// Wrap text for soal and jawaban columns
		$worksheet->getStyle('D5:E'.$lastRow)->getAlignment()->setWrapText(true);

		$filename = 'Evaluasi Jawaban - ' . $tes_nama . ' - ' . date('Y-m-d H-i') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$objWriter = new Xlsx($excel);
		$objWriter->save('php://output');
	}
}