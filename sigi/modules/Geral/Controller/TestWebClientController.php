<?php
namespace Geral\Controller;

class TestWebClientController extends \Geral\Controller\WebClientController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
       $this->view();
    }
}
?>
