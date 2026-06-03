<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;

class Pdf extends Dompdf
{
    /**
     * Default filename
     *
     * @var string
     */
    public $filename;

    public function __construct()
    {
        parent::__construct();

        $this->filename = 'Export-Assets.pdf';
    }

    /**
     * Get CodeIgniter instance
     *
     * @return CI_Controller
     */
    protected function ci()
    {
        return get_instance();
    }

    /**
     * Generate PDF from CI View
     *
     * @param string $view
     * @param array  $data
     * @param string $paper
     * @param string $orientation portrait|landscape
     * @param bool   $download true=download, false=preview
     * @return void
     */
    public function load_view(
        $view,
        $data = [],
        $paper = 'A4',
        $orientation = 'portrait',
        $download = true
    ) {
        $html = $this->ci()->load->view($view, $data, true);

        $this->loadHtml($html);
        $this->setPaper($paper, $orientation);

        $this->render();

        $this->stream($this->filename, [
            'Attachment' => $download
        ]);

        exit;
    }
}
