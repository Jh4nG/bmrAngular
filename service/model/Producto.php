<?php 

class ProductoException extends Exception {}

class Producto {
    private $_idProducto;
    private $_nomProducto;
    private $_precioProducto;

    public function __construct($idProducto, $nomProducto, $precioProducto){
        $this->_idProducto = $idProducto;
        $this->_nomProducto = $nomProducto;
        $this->_precioProducto = $precioProducto;
    }

    public function getIdProducto(){
        return $this->_idProducto;
    }

    public function setIdProducto($idProducto){
        if($idProducto !== null && !is_numeric($idProducto)){
            throw new ProductoException("Error en Id de Producto");
        }
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

    public function getPrecioProducto(){
        return $this->_precioProducto;
    }

    public function setPrecioProducto($precioProducto){
        $this->_precioProducto = $precioProducto;
    }

    public function returnProductoAsArray(){
        $Producto = array();
        $Producto['idProducto'] = $this->getIdProducto();
        $Producto['nombreProducto'] = $this->getNomProducto();
        $Producto['precioProducto'] = $this->getPrecioProducto();
        return $Producto;
    }

}
?>