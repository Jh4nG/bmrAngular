<?php
include '../head.php';
require_once('../model/Producto.php');

class ProductoController extends Response{
    private $db;
    private $_idProductoController = false;

    public function __construct($idProducto = false){
        $nDb = new DB();
        try{
            $this->_idProductoController = $idProducto;
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
        if(array_key_exists("idProducto", $_GET)){
            $this->_idProductoController = $_GET['idProducto'];
            if($this->_idProductoController == '' || !is_numeric($this->_idProductoController)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400); 
                $this->addMessage("Id de Producto no válido");
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
        if(is_numeric($this->_idProductoController)){
            try {
                $query = $this->db->prepare('select * from productos where id = :idProducto');
                $query->bindParam(':idProducto', $this->_idProductoController);
                $query->execute();
                $rowCount = $query->rowCount();
                if($rowCount === 0){
                    $this->setSuccess(false);
                    $this->setHttpStatusCode(404);
                    $this->addMessage("Producto no encontrado");
                    $this->send();
                    exit;
                }
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $Producto = new Producto($row['id'], $row['nombre'], $row['precio']);
                    $ProductoArray[] = $Producto->returnProductoAsArray();
                }
                $returnData = array();
                $returnData['nro_filas'] = $rowCount;
                $returnData['productos'] = $ProductoArray;
                $this->setSuccess(true);
                $this->setHttpStatusCode(200);
                $this->setData($returnData);
                $this->send();
                exit;
            } catch (ProductoException $ex) {
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
                $query = $this->db->prepare('select * from productos');
                $query->execute();
                $rowCount = $query->rowCount();
                $ProductosArray = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $Producto = new Producto($row['id'], $row['nombre'], $row['precio']);
                    $ProductosArray[] = $Producto->returnProductoAsArray();
                }
                $returnData = array();
                $returnData['filas_retornadas'] = $rowCount;
                $returnData['productos'] = $ProductosArray;
                $this->setSuccess(true);
                $this->setHttpStatusCode(200);
                $this->toCache(true);
                $this->setData($returnData);
                $this->send();
                exit;
            }catch(ProductoException $ex){
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

$ProductoController = new ProductoController();