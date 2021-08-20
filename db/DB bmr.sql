CREATE DATABASE bmr;
USE bmr;

CREATE TABLE productos(
	id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    precio BIGINT NOT NULL
);

CREATE TABLE inventario(
    id INT  PRIMARY KEY AUTO_INCREMENT,
    f_vencimiento DATE NOT NULL,
    lote VARCHAR(50) NOT NULL,
    cantidad INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos (id)
);

CREATE TABLE cliente(
    id BIGINT PRIMARY KEY NOT NULL,
    nombre VARCHAR (150) NOT NULL
);

CREATE TABLE pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cli BIGINT NOT NULL,
    FOREIGN KEY (id_cli) REFERENCES cliente (id)
);

CREATE TABLE pedido_cliente (
    id INT AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantida INT NOT NULL,
    PRIMARY KEY (id,id_pedido,id_producto),
    FOREIGN KEY (id_pedido) REFERENCES pedido (id),
    FOREIGN KEY (id_producto) REFERENCES productos (id)
    
);

/* Inserción de Clientes Base*/
INSERT INTO cliente(id,nombre)
VALUES(1233,'Jhon González'),
(11444,'Pepito Pérez'),
(111333,'Pedro García');

/* Inserción de Productos Base*/
INSERT INTO productos(nombre,precio) 
VALUES('Leche',3600),
('Queso',7000),
('Jamón',8200);

/* Inserción de Inventario Base*/
INSERT INTO inventario(f_vencimiento,lote,cantidad,id_producto)
VALUES ('2022-01-30','LT0001',15,1),
('2021-12-10','LT0001',10,2),
('2021-11-04','LT0001',20,3);