<?php
namespace Geral\Controller;

class TestFileRepositoryController extends \Geral\Controller\AbstractPrivateController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->addVarView('apps', \Geral\Model\Config::getAppNames());
        $this->view();
    }
}
?>
