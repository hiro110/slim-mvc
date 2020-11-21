<?php
namespace App\Controllers;

use Psr\Container\ContainerInterface;

class BaseController
{
    protected $view;
    protected $db;
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get("view");
        $this->db = $container->get("db");
        // $this->logger = $container->get("logger");
    }

    public function getView(){
        return $this->view;
    }

    public function getDb(){
        return $this->db;
    }
}
