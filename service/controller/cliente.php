<?php
include '../head.php';
require_once('../model/Cliente.php');

class ClienteController extends Response{
    private $db;
    private $_idClienteController = false;

    public function __construct($idCliente = false){
        $nDb = new DB();
        try{
            $this->_idClienteController = $idCliente;
            $this->db = $nDb->conectarDB();
        }catch(PDOException $ex){
            $this->setSuccess(false);
            $this->setHttpStatusCode(500);
            $this->addMessage("Error de conexión a la BD");
            $this->send();
            exit;
        }
        $this->init();
    }

    private function init(){
        if(array_key_exists("idCliente", $_GET)){
            $this->_idClienteController = $_GET['idCliente'];
            if($this->_idClienteController == '' || !is_numeric($this->_idClienteController)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400); 
                $this->addMessage("Id de Cliente no válido");
                $this->send();
                exit;
            }
        }
        $this->executeProcess();
    }

    private function executeProcess(){
        switch($_SERVER['REQUEST_METHOD']){
            case 'GET':
                $this->getRequest();
                break;
            default :
                $this->setSuccess(false);
                $this->setHttpStatusCode(500); 
                $this->addMessage("Request Method no encontrado.");
                $this->send();
                break;
        }
    }

    private function getRequest(){
        if(is_numeric($this->_idClienteController)){
            try {
                $query = $this->db->prepare('select * from cliente where id = :idCliente');
                $query->bindParam(':idCliente', $this->_idClienteController);
                $query->execute();
                $rowCount = $query->rowCount();
                if($rowCount === 0){
                    $this->setSuccess(false);
                    $this->setHttpStatusCode(404);
                    $this->addMessage("Cliente no encontrado");
                    $this->send();
                    exit;
                }
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $cliente = new Cliente($row['id'], $row['nombre']);
                    $clienteArray[] = $cliente->returnClienteAsArray();
                }
                $returnData = array();
                $returnData['nro_filas'] = $rowCount;
                $returnData['clientes'] = $clienteArray;
                $this->setSuccess(true);
                $this->setHttpStatusCode(200);
                $this->setData($returnData);
                $this->send();
                exit;
            } catch (ClienteException $ex) {
                $this->setSuccess(false); $this->setHttpStatusCode(500);
                $this->addMessage($ex->getMessage());
                $this->send();
                exit;
            }catch(PDOException $ex){
                $this->setSuccess(false);
                $this->setHttpStatusCode(500);
                $this->addMessage("Error conectando a Base de Datos");
                $this->send();
                exit;
            }
        }else{
            try{
                $query = $this->db->prepare('select * from cliente');
                $query->execute();
                $rowCount = $query->rowCount();
                $clientesArray = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $cliente = new Cliente($row['id'], $row['nombre']);
                    $clientesArray[] = $cliente->returnClienteAsArray();
                }
                $returnData = array();
                $returnData['filas_retornadas'] = $rowCount;
                $returnData['clientes'] = $clientesArray;
                $this->setSuccess(true);
                $this->setHttpStatusCode(200);
                $this->toCache(true);
                $this->setData($returnData);
                $this->send();
                exit;
            }catch(ClienteException $ex){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage($ex->getMessage());
                $this->send(); 
                exit;
            }catch(PDOException $ex){
                $this->setSuccess(false);
                $this->setHttpStatusCode(500);
                $this->addMessage("Error conectando a Base de Datos");
                $this->send();
                exit;
            }
        }
    }
}

$ClienteController = new ClienteController();