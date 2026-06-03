<?php defined('BASEPATH') or exit('No direct script access allowed');

class ExportImport extends MY_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function ExportLaporan()
	{
		$this->load->library('pdf');
		$date = date('d-m-Y');
		$this->pdf->filename = "Laporan GO-SARI Lestari {$date}.pdf";

		switch ($this->session->userdata('role_name')) {
			case 'Petugas':
				$views = 'export-laporan-petugas-pdf';
				$data = [
					'belum_pilah' => $this->ExportImports->belumPilah(),
					'sampah_terkelola' => $this->ExportImports->sampahTerkelola(),
					'jenis_sampah' => $this->ExportImports->byKategoriSampah(),
					'penghasilan_warga' => $this->ExportImports->penghasilanWarga()
				];
				break;
			case 'Warga':
				$views = 'export-laporan-warga-pdf';
				$this->load->model(['Rtrws']);
				$rtrw = $this->Rtrws->findOne($this->session->userdata('rtrw'));
				$data = [
					'warga' => [
						'nama' => $this->session->userdata('nama'),
						'rtrw' => $rtrw['nama']
					],
					'berat_kategori_sampah' => $this->ExportImports->beratKategoriSampah(),
					'pendapatan_kategori_sampah' => $this->ExportImports->pendapatanKategoriSampah(),
					'iuran' => $this->ExportImports->iuranPerBulan(),
				];
				break;
			default:
				$views = '';
				$data = [];
				break;
		}

		$this->pdf->load_view(
			$views,
			$data,
			// 'A4',
			// 'potrait',
			// false
		);
	}

	public function TemplateImportWarga() {}

	public function ImportWarga() {}

	public function TemplateRestokMassal() {}

	public function RestokMassal() {}

	public function ExportLedgerCsv() {}

	public function ExportLedgerPdf() {}
}
