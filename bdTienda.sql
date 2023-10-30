CREATE SCHEMA tienda;
USE tienda;

CREATE TABLE productos(
	idProducto INT PRIMARY KEY AUTO_INCREMENT,
	nombreProducto VARCHAR(40) NOT NULL ,
    precio NUMERIC(7,2) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad NUMERIC(5) NOT NULL
);