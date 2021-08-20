<?php
include '../head.php';
require_once('../model/Pedido.php');

class PedidoController extends Response{
    private $db;
    private $_idPedidoController = false;

    public function __construct($idPedido = false){
        $nDb = new DB();
        try{
            $this->_idPedidoController = $idPedido;
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
        if(array_key_exists("idPedido", $_GET)){
            $this->_idPedidoController = $_GET['idPedido'];
            if($this->_idPedidoController == '' || !is_numeric($this->_idPedidoController)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400); 
                $this->addMessage("Id de Pedido no válido");
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
            case 'DELETE':
                $this->getDelete();
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
        if(is_numeric($this->_idPedidoController)){
            try {
                $query = $this->db->prepare('SELECT c.id as idcli,c.nombre as nomcli,pr.id as idprod,pr.nombre as nomprod,pc.cantidad 
                FROM pedido_cliente pc,pedido p, cliente c, productos pr
                WHERE pc.id_pedido = p.id
                AND pc.id_producto = pr.id
                AND p.id_cli = c.id
                AND p.id = :idPedido');
                $query->bindParam(':idPedido', $this->_idPedidoController);
                $query->execute();
                $rowCount = $query->rowCount();
                if($rowCount === 0){
                    $this->setSuccess(false);
                    $this->setHttpStatusCode(404);
                    $this->addMessage("Pedido no encontrado");
                    $this->send();
                    exit;
                }
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $Pedido = new Pedido($row['idcli'], $row['nomcli'], $row['idprod'], $row['nomprod'], $row['cantidad']);
                    $PedidoArray[] = $Pedido->returnPedidoAsArray();
                }
                $returnData = array();
                $returnData['nro_filas'] = $rowCount;
                $returnData['pedidos'] = $PedidoArray;
                $this->setSuccess(true);
                $this->setHttpStatusCode(200);
                $this->setData($returnData);
                $this->send();
                exit;
            } catch (PedidoException $ex) {
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
                $query = $this->db->prepare('SELECT c.id as idcli,c.nombre as nomcli,pr.id as idprod,pr.nombre as nomprod,pc.cantidad 
                FROM pedido_cliente pc,pedido p, cliente c, productos pr
                WHERE pc.id_pedido = p.id
                AND pc.id_producto = pr.id
                AND p.id_cli = c.id');
                $query->execute();
                $rowCount = $query->rowCount();
                $PedidosArray = array();
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    $Pedido = new Pedido($row['idcli'], $row['nomcli'], $row['idprod'], $row['nomprod'], $row['cantidad']);
                    $PedidoArray[] = $Pedido->returnPedidoAsArray();
                }
                $returnData = array();
                $returnData['filas_retornadas'] = $rowCount;
                $returnData['pedidos'] = $PedidosArray;
                $this->setSuccess(true);
                $this->setHttpStatusCode(200);
                $this->toCache(true);
                $this->setData($returnData);
                $this->send();
                exit;
            }catch(PedidoException $ex){
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

    private function getDelete(){

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
            if(!isset($jsonData->idCliente)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Id Cliente es obligatorio');
                $this->send();
                exit(); 
            }
            if(!isset($jsonData->idProducto)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Id Producto es obligatorio');
                $this->send();
                exit(); 
            }
            if(!isset($jsonData->cantidad)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Cantidad es obligatorio');
                $this->send();
                exit();
            }

            $query = $this->db->prepare('SELECT * FROM inventario WHERE id_producto = :idProducto');
            $query->bindParam(':idProducto', $jsonData->idProducto, PDO::PARAM_INT);
            $query->execute();
            $obj = $query -> fetchAll(PDO::FETCH_OBJ);
            if(!($obj[0]->cantidad > $jsonData->cantidad)){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('La cantidad del pedido supera a la cantidad en inventario. Vuelva a intentar con una cantidad inferior');
                $this->send();
                exit();
            }
            // Ingreso de registro en tabla pedido
            $query = $this->db->prepare('INSERT INTO pedido (id_cli) values (:idCliente)');
            $query->bindParam(':idCliente', $jsonData->idCliente, PDO::PARAM_INT);
            $query->execute();
            $lastIdPedido = $this->db->lastInsertId();

            // Ingreso del producto al pedido
            $query = $this->db->prepare('INSERT INTO pedido_cliente (id_pedido,id_producto,cantidad) values (:idPedido,:idProducto,:Cantidad)');
            $query->bindParam(':idPedido', $lastIdPedido, PDO::PARAM_INT);
            $query->bindParam(':idProducto', $jsonData->idProducto, PDO::PARAM_INT);
            $query->bindParam(':Cantidad', $jsonData->cantidad, PDO::PARAM_INT);
            $query->execute();
            $rowCount = $query->rowCount();

            $cantActual = $obj[0]->cantidad - $jsonData->cantidad; // Nueva cantidad actual del producto en inventario
            $query = $this->db->prepare('UPDATE inventario SET cantidad = :CantActual WHERE id_producto = :idProducto');
            $query->bindParam(':CantActual', $cantActual, PDO::PARAM_INT);
            $query->bindParam(':idProducto', $jsonData->idProducto, PDO::PARAM_INT);
            $query->execute();
            
            if($rowCount===0){
                $this->setSuccess(false);
                $this->setHttpStatusCode(400);
                $this->addMessage('Falló creación de Pedido');
                $this->send();
                exit();
            }
            $this->setSuccess(true);
            $this->setHttpStatusCode(201);
            $this->addMessage('Pedido creado');
            $this->setData($lastIdPedido);
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

$PedidoController = new PedidoController();