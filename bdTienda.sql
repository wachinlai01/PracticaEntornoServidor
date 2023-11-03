CREATE SCHEMA tienda;
USE tienda;

CREATE TABLE productos(
	idProducto INT PRIMARY KEY AUTO_INCREMENT,
	nombreProducto VARCHAR(40) NOT NULL ,
    precio NUMERIC(7,2) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    cantidad NUMERIC(5) NOT NULL
);

CREATE TABLE usuarios(
	usuario VARCHAR(12) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    fechaNacimiento DATE NOT NULL
);

CREATE TABLE Cestas(
	idCesta INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(12),
    cantidad NUMERIC(2) NOT NULL,
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

SELECT * FROM usuarios;