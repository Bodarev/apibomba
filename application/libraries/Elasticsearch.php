<?php
require_once(__DIR__ . '/../../vendor/autoload.php');

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;

class Elasticsearch{

    public $client;
    public $index;
    public $type;

    public function __construct(){
        $this->client = ClientBuilder::create()->build();
        $this->index = 'app_products';
        $this->type = 'product';
    }

    public function index($params) {
        try {
            $params['index'] = $this->index;
            $params['type'] = $this->type;
            return $response = $this->client->index($params);
        } catch (Exception $e) {
            return "Error";
        }
    }

    public function search($params) {
        return $response = $this->client->search($params);
    }

    public function delete() {
        try {
            return $this->client->delete([
                'index' => $this->index
            ]);
        } catch (ClientErrorResponseException $e) {
            if ($e->getCode() === 404) {
                return "Not found";
            }
        }
    }
}
