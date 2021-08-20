<?php 

class ClienteException extends Exception {}

class Cliente {
    private $_idCliente;
    private $_nomCliente;

    public function __construct($idCliente, $nomCliente){
        $this->_idCliente = $idCliente;
        $this->_nomCliente = $nomCliente;
    }

    public function getIdCliente(){
        return $this->_idCliente;
    }

    public function setIdCliente($idCliente){
        if($idCliente !== null && !is_numeric($idCliente)){
            throw new ClienteException("Error en Id de Cliente");
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

    public function returnClienteAsArray(){
        $Cliente = array();
        $Cliente['idCliente'] = $this->getIdCliente();
        $Cliente['nombreCliente'] = $this->getNomCliente();
        return $Cliente;
    }

}
?>