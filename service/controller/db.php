<?php 

class DB {
    private $dbConnection;
    private $db = "bmr";
    private $host = "localhost";
    private $port = "3306";
    private $user = "root";
    private $password = "";

    public function conectarDB(){
        if($this->dbConnection == null){
            $this->dbConnection = 
            new PDO("mysql:dbname={$this->db};host={$this->host};port={$this->port}:charset=utf8",$this->user,$this->password);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $this->dbConnection;
        }
    }
}

?>