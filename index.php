<?php
$debug = stripos($_SERVER['HTTP_HOST'], 'local') !== FALSE;

define("DEGUB", $debug);

header('Content-Type: application/json');
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Authorization, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Allow-Credentials: true");


// API
class API
{
    private $post;
    private $get;

    private $result = [];
    private $status = 'ok';
    private $error = '';

    private $debug = [];

    public function __construct()
    {
        $json = file_get_contents('php://input');
        $this->post = json_decode($json, 1);
        $this->get = $_GET;

        try {
            $this->init();
        } catch (Exception $e) {
            $this->status = 'error';
            $this->error = $e->getMessage();
        }

        $this->debug['get'] = $this->get;
        $this->debug['post'] = $this->post;
    }

    private function init()
    {
        $get_method = $this->get['method'] ?: $this->post['method'];

        $this->debug['method'] = 'api_' . $get_method;

        if (!$get_method || !method_exists($this, 'api_' . $get_method)) throw new Exception('метод не найден');

        $method = 'api_' . $get_method;
        $this->$method();
    }

    private function api_check()
    {
        throw new Exception('tratata');
    }

    public function apply()
    {
        $params = Array();

        $params['status'] = $this->status;

        if (DEBUG) $params['debug'] = $this->debug;

        if ($this->status == 'ok') {
            $params['result'] = $this->result;
        } elseif ($this->status == 'error') {
            $params['error'] = $this->error;
        }

        print json_encode($params);
    }
}

$API = new API;
$API->apply();