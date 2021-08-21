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
            case 'POST':
                $this->getPost();
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

    private function getPost(){
        try{
            if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Content Type no corresponde a formato JSON');
                $this->send();
                exit();
            } 
            $rawPOSTData = file_get_contents('php://input');
            if(!$jsonData = json_decode($rawPOSTData)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Request Body no corresponde a formato JSON');
                $this->send();
                exit();
            }
            if(!isset($jsonData->nombreProducto)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Nombre es obligatorio');
                $this->send();
                exit(); 
            }
            if(!isset($jsonData->cantidadProducto)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Cantidad es obligatorio');
                $this->send();
                exit(); 
            }
            if(!isset($jsonData->loteProducto)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Lote es obligatorio');
                $this->send();
                exit();
            }
            if(!isset($jsonData->fechaProducto)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Fecha Vencimiento es obligatorio');
                $this->send();
                exit();
            }
            if(!isset($jsonData->precioProducto)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Precio es obligatorio');
                $this->send();
                exit();
            }

            $query = $this->db->prepare('INSERT INTO productos(nombre,precio) VALUES(:nomProducto,:precioProducto)');
            $query->bindParam(':nomProducto', $jsonData->nombreProducto, PDO::PARAM_STR);
            $query->bindParam(':precioProducto', $jsonData->precioProducto, PDO::PARAM_INT);
            $query->execute();
            $lastIdProducto = $this->db->lastInsertId();

            $query = $this->db->prepare('INSERT INTO inventario(f_vencimiento,lote,cantidad,id_producto) 
                                    VALUES(:fechaProducto,:loteProducto,:cantidadProducto,:idProducto)');
            $query->bindParam(':fechaProducto', $jsonData->fechaProducto, PDO::PARAM_INT);
            $query->bindParam(':loteProducto', $jsonData->loteProducto, PDO::PARAM_INT);
            $query->bindParam(':cantidadProducto', $jsonData->cantidadProducto, PDO::PARAM_INT);
            $query->bindParam(':idProducto', $lastIdProducto, PDO::PARAM_STR);
            $query->execute();
            
            $rowCount = $query->rowCount();

            if($rowCount===0){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Falló creación de producto y agregación a inventario.');
                $this->send();
                exit();
            }
            $this->setSuccess(true);
            $this->setHttpStatusCode(201);
            $this->addMessage('Producto agregado al inventario correctamenteo.');
            $this->setData($lastIdProducto);
            $this->send();
            exit();
        }catch(PedidoException $ex){
            $this->setSuccess(false);
            $this->setHttpStatusCode(400);
            $this->addMessage($ex->getMessage());
            $this->send();
            exit();
        }catch(PDOException $ex){
            $this->setSuccess(false);
            $this->setHttpStatusCode(500);
            $this->addMessage('Falló conexión a BD');
            $this->send();
            exit();
        }
    }
}

$ProductoController = new ProductoController();