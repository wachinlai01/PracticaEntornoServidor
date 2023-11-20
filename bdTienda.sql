CREATE SCHEMA tienda;
USE tienda;

CREATE TABLE productos(
	idProducto INT PRIMARY KEY AUTO_INCREMENT,
	nombreProducto VARCHAR(40) NOT NULL ,
    precio NUMERIC(7,2) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad NUMERIC(5) NOT NULL
);

ALTER TABLE productos
	ADD COLUMN imagen VARCHAR(100) NOT NULL;

CREATE TABLE usuarios(
	usuario VARCHAR(12) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    fechaNacimiento DATE NOT NULL
);

ALTER TABLE usuarios
	ADD COLUMN rol VARCHAR(10) NOT NULL DEFAULT 'cliente';

CREATE TABLE cestas(
	idCesta INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(12),
    precioTotal NUMERIC(7,2) NOT NULL,
    CONSTRAINT Fk_Cestas 
		FOREIGN KEY (usuario)
        REFERENCES usuarios(usuario)
);

CREATE TABLE productosCestas (
	idProducto INT,
    idCesta INT,
    cantidad NUMERIC(2),
    CONSTRAINT pk_productosCestas
		PRIMARY KEY (idProducto,idCesta),
	CONSTRAINT fk_productosCestas_productos
		FOREIGN KEY (idProducto)
        REFERENCES productos(idProducto),
	CONSTRAINT fk_productosCestas_cestas
		FOREIGN KEY (idCesta)
        REFERENCES Cestas(idCesta)
);

CREATE TABLE pedidos(
	idPedido INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(12),
    precioTotal NUMERIC(7,2) NOT NULL,
    fechaPedido DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT Fk_pedidos 
		FOREIGN KEY (usuario)
        REFERENCES usuarios(usuario)
);

CREATE TABLE lineasPedidos(
	lineaPedido NUMERIC(2) NOT NULL,
    idProducto INT,
    idPedido INT,
    precioUnitario NUMERIC(7,2) NOT NULL,
    cantidad NUMERIC(5) NOT NULL,
    CONSTRAINT Fk_lineasPedidos_pedidos 
		FOREIGN KEY (idPedido)
        REFERENCES pedidos(idPedido),
	 CONSTRAINT Fk_lineasPedidos_productos 
		FOREIGN KEY (idProducto)
        REFERENCES productos(idProducto)
);

DROP TABLE lineasPedidos;

COMMIT;
UPDATE usuarios SET rol = "admin" WHERE usuario = 'Wachinlai';
UPDATE productos SET cantidad = 20 WHERE idProducto = 2;
SELECT * FROM productos;
SELECT * FROM usuarios;
SELECT * FROM pedidos;
SELECT * FROM lineasPedidos;
DELETE FROM productos;
-- Para poder borrar
SET SQL_SAFE_UPDATES = 0;