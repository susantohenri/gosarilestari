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
				$views = 'pdfs/export-laporan-petugas-pdf';
				$data = [
					'belum_pilah' => $this->ExportImports->belumPilah(),
					'sampah_terkelola' => $this->ExportImports->sampahTerkelola(),
					'jenis_sampah' => $this->ExportImports->byKategoriSampah(),
					'penghasilan_warga' => $this->ExportImports->penghasilanWarga()
				];
				break;
			case 'Warga':
				$views = 'pdfs/export-laporan-warga-pdf';
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

	public function TemplateImportWarga()
	{
		// Set headers
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="Template Import Warga GOSARI Lestari.csv"');

		// Buka output langsung
		$output = fopen('php://output', 'w');

		// Tulis header
		fputcsv($output, ['username', 'password', 'nama', 'kontak', 'alamat', 'rtrw']);

		// SAMPLE DATA - 1 row contoh
		$sample_row = [
			'ahmadrizki',
			'123456',
			'Ahmad Rizki',
			'081234567890',
			'Jl. Merdeka No. 45',
			'Kembangputihan RT 006'
		];

		// Tulis sample row
		fputcsv($output, $sample_row);

		fclose($output);
		exit;
	}

	public function ImportWarga()
	{
		// Load helper yang diperlukan
		$this->load->helper('file');

		// Cek apakah ada file yang diupload
		if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {
			$this->session->set_flashdata('model_error', 'Silakan pilih file CSV terlebih dahulu');
			redirect(site_url('Warga'));
			return;
		}

		// Validasi tipe file
		$file_ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);
		if (strtolower($file_ext) != 'csv') {
			$this->session->set_flashdata('model_error', 'Hanya file CSV yang diperbolehkan');
			redirect(site_url('Warga'));
			return;
		}

		// Baca file langsung dari temporary path
		$file_tmp = $_FILES['csv_file']['tmp_name'];
		$file = fopen($file_tmp, 'r');

		if (!$file) {
			$this->session->set_flashdata('model_error', 'Tidak bisa membaca file CSV');
			redirect(site_url('Warga'));
			return;
		}

		// Baca header (baris pertama)
		$headers = fgetcsv($file);

		// Validasi header
		$expected_headers = ['username', 'password', 'nama', 'kontak', 'alamat', 'rtrw'];
		$headers = array_map(function ($header) {
			return trim(str_replace("\xEF\xBB\xBF", '', $header));
		}, $headers);

		if ($headers !== $expected_headers) {
			fclose($file);
			$this->session->set_flashdata('model_error', 'Format header tidak sesuai. Harus: ' . implode(', ', $expected_headers));
			redirect(site_url('Warga'));
			return;
		}

		$this->load->model(['Wargas', 'Rtrws']);
		$rtrws = $this->Rtrws->find();
		$rtrw = [];
		foreach ($rtrws as $item) $rtrw[$item->nama] = $item->uuid;
		// Mulai transaction
		$this->db->trans_start();

		$success_count = 0;
		$failed_count = 0;
		$failed_rows = [];
		$row_number = 1; // Mulai dari 1 karena header sudah dilewat

		// Langsung baca dan proses data per baris
		while (($data = fgetcsv($file)) !== FALSE) {
			$row_number++;

			// Skip baris kosong
			if (count($data) < 6 || (count($data) == 1 && empty($data[0]))) {
				continue;
			}

			// Bersihkan data
			$user_data = [
				'username' => trim($data[0]),
				'password' => trim($data[1]),
				'nama' => trim($data[2]),
				'kontak' => trim($data[3]),
				'alamat' => trim($data[4]),
				'rtrw' => $rtrw[trim($data[5])],
			];

			// Validasi dasar
			if (
				empty($user_data['username'])
				|| empty($user_data['password'])
				|| empty($user_data['nama'])
			) {
				$failed_count++;
				$failed_rows[] = $row_number;
				continue;
			}

			// Insert ke database
			if ((array) $this->Wargas->create($user_data)) {
				$success_count++;
			} else {
				$failed_count++;
				$failed_rows[] = $row_number;
			}
		}

		fclose($file); // Tutup file, temporary file otomatis terhapus

		// Commit atau rollback
		if ($failed_count == 0) {
			$this->db->trans_complete();
			$this->session->set_flashdata('success', "Berhasil import $success_count data user");
		} else {
			$this->db->trans_rollback();
			$this->session->set_flashdata('error', "Gagal import! $success_count berhasil, $failed_count gagal. Baris error: " . implode(', ', array_slice($failed_rows, 0, 10)));
		}

		redirect(site_url('Warga'));
	}

	public function TemplateRestokMassal()
	{
		// Set headers
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="Template Restok Massal GOSARI Lestari.csv"');

		// Buka output langsung
		$output = fopen('php://output', 'w');

		// Tulis header
		fputcsv($output, ['kode', 'stok']);

		// SAMPLE DATA - 1 row contoh
		$sample_row = [
			'TRCLF4',
			'24',
		];

		// Tulis sample row
		fputcsv($output, $sample_row);

		fclose($output);
		exit;
	}

	public function RestokMassal()
	{
		// Load helper yang diperlukan
		$this->load->helper('file');

		// Cek apakah ada file yang diupload
		if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {
			$this->session->set_flashdata('model_error', 'Silakan pilih file CSV terlebih dahulu');
			redirect(site_url('ProdukTukar'));
			return;
		}

		// Validasi tipe file
		$file_ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);
		if (strtolower($file_ext) != 'csv') {
			$this->session->set_flashdata('model_error', 'Hanya file CSV yang diperbolehkan');
			redirect(site_url('ProdukTukar'));
			return;
		}

		// Baca file langsung dari temporary path
		$file_tmp = $_FILES['csv_file']['tmp_name'];
		$file = fopen($file_tmp, 'r');

		if (!$file) {
			$this->session->set_flashdata('model_error', 'Tidak bisa membaca file CSV');
			redirect(site_url('ProdukTukar'));
			return;
		}

		// Baca header (baris pertama)
		$headers = fgetcsv($file);

		// Validasi header
		$expected_headers = ['kode', 'stok'];
		$headers = array_map(function ($header) {
			return trim(str_replace("\xEF\xBB\xBF", '', $header));
		}, $headers);

		if ($headers !== $expected_headers) {
			fclose($file);
			$this->session->set_flashdata('model_error', 'Format header tidak sesuai. Harus: ' . implode(', ', $expected_headers));
			redirect(site_url('ProdukTukar'));
			return;
		}

		$this->load->model(['ProdukTukars']);
		$produks = $this->ProdukTukars->find();
		// Mulai transaction
		$this->db->trans_start();

		$success_count = 0;
		$failed_count = 0;
		$failed_rows = [];
		$row_number = 1; // Mulai dari 1 karena header sudah dilewat

		// Langsung baca dan proses data per baris
		while (($data = fgetcsv($file)) !== FALSE) {
			$row_number++;

			// Skip baris kosong
			if (count($data) < 2 || (count($data) == 1 && empty($data[0]))) {
				continue;
			}

			$kode = trim($data[0]);
			$produk = current(array_filter($produks, function ($prod) use ($kode) {
				return $prod->kode === $kode;
			}));

			// Validasi dasar
			if (!$produk) {
				$failed_count++;
				$failed_rows[] = $row_number;
				continue;
			}

			$produk->stok += (int) trim($data[1]);

			// Insert ke database
			if ((array) $this->ProdukTukars->update((array) $produk)) {
				$success_count++;
			} else {
				$failed_count++;
				$failed_rows[] = $row_number;
			}
		}

		fclose($file); // Tutup file, temporary file otomatis terhapus

		// Commit atau rollback
		if ($failed_count == 0) {
			$this->db->trans_complete();
			$this->session->set_flashdata('success', "Berhasil restok $success_count produk tukar");
		} else {
			$this->db->trans_rollback();
			$this->session->set_flashdata('error', "Gagal import! $success_count berhasil, $failed_count gagal. Baris error: " . implode(', ', array_slice($failed_rows, 0, 10)));
		}

		redirect(site_url('ProdukTukar'));
	}

	public function ExportLedgerCsv() {}

	public function ExportLedgerPdf() {}
}
