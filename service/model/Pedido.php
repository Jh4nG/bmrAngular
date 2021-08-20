<?php 

class PedidoException extends Exception {}

class Pedido {
    private $_idCliente;
    private $_nomCliente;
    private $_idProducto;
    private $_nomProducto;
    private $_cantidad;

    public function __construct($idCliente, $nomCliente, $idProducto, $nomProducto, $cantidad){
        $this->_idCliente = $idCliente;
        $this->_nomCliente = $nomCliente;
        $this->_idProducto = $idProducto;
        $this->_nomProducto = $nomProducto;
        $this->_cantidad = $cantidad;
    }

    public function getIdCliente(){
        return $this->_idCliente;
    }

    public function setIdCliente($idCliente){
        if($idCliente !== null && !is_numeric($idCliente)){
            throw new PedidoException("Error en Id de Cliente");
        }
        $this->_idCliente = $idCliente;
    }

    public function getNomCliente(){
        return $this->_nomCliente;
    }

    public function setNomCliente($nomCliente){
        if($nomCliente !== null && strlen($this->idCliente)>50){
            throw new ClienteException("Error en nombre de Cliente");
        }
        $this->_nomCliente = $nomCliente;
    }

    public function getidProducto(){
        return $this->_idProducto;
    }

    public function setidProducto($idProducto){
        $this->_idProducto = $idProducto;
    }

    public function getNomProducto(){
        return $this->_nomProducto;
    }

    public function setNomProducto($nomProducto){
        if($nomProducto !== null && strlen($this->idProducto)>50){
            throw new ProductoException("Error en nombre de Producto");
        }
        $this->_nomProducto = $nomProducto;
    }

    public function getCantidad(){
        return $this->_cantidad;
    }

    public function setCantidad($cantidad){
        $this->_cantidad = $cantidad;
    }

    public function returnPedidoAsArray(){
        $Pedido = array();
        $Pedido['idCliente'] = $this->getIdCliente();
        $Pedido['nomCliente'] = $this->getNomCliente();
        $Pedido['idProducto'] = $this->getidProducto();
        $Pedido['nomProducto'] = $this->getNomProducto();
        $Pedido['cantidad'] = $this->getCantidad();
        return $Pedido;
    }

}
?>