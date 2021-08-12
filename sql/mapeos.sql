/* ****************************************************************************************** */
/* PARA MENÚ 
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS menu (
    menu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenu (
    submenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
    , detalle TEXT
    , menu INT(11)
    , INDEX(menu)
    , FOREIGN KEY (menu)
        REFERENCES menu (menu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS item (
    item_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , url VARCHAR(50)
    , detalle VARCHAR(100)}
    , INDEX(submenu)
    , FOREIGN KEY (submenu)
        REFERENCES submenu (submenu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS configuracionmenu (
    configuracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , nivel INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenuconfiguracionmenu (
    submenuconfiguracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES submenu (submenu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS itemconfiguracionmenu (
    itemconfiguracionmenu_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES item (item_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS archivo (
    archivo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , url VARCHAR(100)
    , fecha_carga DATE
    , formato VARCHAR(50)
) ENGINE=InnoDb;

/* ****************************************************************************************** */
/* PARA USUARIO DESARROLLADOR
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS usuariodetalle (
    usuariodetalle_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(50)
    , nombre VARCHAR(50)
    , correoelectronico VARCHAR(250)
    , token TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS usuario (
    usuario_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , nivel INT(1)
    , usuariodetalle INT(11)
    , INDEX (usuariodetalle)
    , FOREIGN KEY (usuariodetalle)
        REFERENCES usuariodetalle (usuariodetalle_id)
        ON DELETE CASCADE
    , configuracionmenu INT(11)
    , INDEX (configuracionmenu)
    , FOREIGN KEY (configuracionmenu)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

INSERT INTO usuariodetalle (usuariodetalle_id, apellido, nombre, correoelectronico, token) VALUES
(1, 'Admin', 'admin', 'admin@admin.com', '4850fc35306cb8590e00564f5462e1bb'),
(2, 'Desarrollador', 'Admin', 'infozamba@gmail.com', '2d646827cffbc42da16cee8033e209d4');

INSERT INTO usuario (usuario_id, denominacion, nivel, usuariodetalle, configuracionmenu) VALUES
(1, 'admin', 3, 1, 2),
(2, 'desarrollador', 9, 2, 1);

/* ****************************************************************************************** */
/* PARA OBJETOS DE CONFIGURACIÓN DE ENTIDAD
/* ****************************************************************************************** */
CREATE TABLE IF NOT EXISTS configuracion (
    configuracion_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , razon_social TEXT
    , domicilio_comercial TEXT
    , telefono INT(11)
    , cuit BIGINT(20)
    , ingresos_brutos VARCHAR(50)
    , fecha_inicio_actividad DATE
    , punto_venta INT(11)
) ENGINE=InnoDb;

/* ****************************************************************************************** */
/* PARA OBJETOS DE CASOS DE USO 
/* ****************************************************************************************** */

CREATE TABLE IF NOT EXISTS documentotipo (
    documentotipo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS provincia (
    provincia_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontacto (
    infocontacto_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , valor TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS matriculado (
    matriculado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , apellido TEXT
    , nombre TEXT
    , documento BIGINT(20)
    , domicilio TEXT
    , codigopostal VARCHAR(50)
    , barrio TEXT
    , observacion TEXT
    , provincia INT(11)
    , INDEX (provincia)
    , FOREIGN KEY (provincia)
        REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
    , documentotipo INT(11)
    , INDEX (documentotipo)
    , FOREIGN KEY (documentotipo)
        REFERENCES documentotipo (documentotipo_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS infocontactomatriculado (
    infocontactomatriculado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES matriculado (matriculado_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES infocontacto (infocontacto_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS universidad (
    universidad_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , provincia INT(11)
    , INDEX (provincia)
    , FOREIGN KEY (provincia)
        REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS titulo (
    titulo_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , valor_matricula FLOAT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS matricula (
    matricula_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , matricula VARCHAR(100)
    , fecha_egreso DATE
    , fecha_inscripcion DATE
    , habilitacion VARCHAR(100)
    , tomo VARCHAR(100)
    , estado INT(1)
    , titulo INT(11)
    , INDEX (titulo)
    , FOREIGN KEY (titulo)
        REFERENCES titulo (titulo_id)
        ON DELETE CASCADE
    , universidad INT(11)
    , INDEX (universidad)
    , FOREIGN KEY (universidad)
        REFERENCES universidad (universidad_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS matriculamatriculado (
    matriculamatriculado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES matriculado (matriculado_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES matricula (matricula_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS resolucion (
    resolucion_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , fecha_desde DATE
    , fecha_hasta DATE
    , descuento FLOAT
    , recarga FLOAT
    , detalle TEXT
    , estado INT(1)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS movimientotipopago (
    movimientotipopago_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , numero_movimiento TEXT
    , fecha_vencimiento DATE
    , estado INT(1)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS conceptopago (
    conceptopago_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , tipo INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuentacorrientematriculado (
    cuentacorrientematriculado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , anio INT(4)
    , valor_abonado FLOAT
    , valor_matricula FLOAT
    , fecha DATE
    , hora TIME
    , habilitacion VARCHAR(250)
    , tomo VARCHAR(250)
    , numero_cuota INT(2)
    , total_cuotas INT(2)
    , estado INT(1)
    , movimientotipopago_id INT(11)
    , resolucion_id INT(11)
    , matriculado_id INT(11)
    , matricula_id INT(11)
    , usuario_id INT(11)
    , conceptopago INT(11)
    , INDEX (conceptopago)
    , FOREIGN KEY (conceptopago)
        REFERENCES conceptopago (conceptopago_id)
        ON DELETE SET NULL
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS tipofactura (
    tipofactura_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , afip_id INT(11)
    , nomenclatura VARCHAR(150)
    , denominacion VARCHAR(150)
    , plantilla_impresion TEXT
    , detalle TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS comprobantepago (
    comprobantepago_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , punto_venta INT(4)
    , numero_factura INT(8)
    , fecha DATE
    , hora TIME
    , subtotal FLOAT
    , importe_total FLOAT
    , emitido INT(11)
    , cuentacorrientematriculado_id INT(11)
    , tipofactura INT(11)
    , INDEX (tipofactura)
    , FOREIGN KEY (tipofactura)
        REFERENCES tipofactura (tipofactura_id)
        ON DELETE SET NULL
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS movimientofinanciero (
    movimientofinanciero_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , numero_movimiento BIGINT()
    , fecha_vencimiento DATE
    , importe FLOAT
    , fecha DATE
    , hora TIME
    , detalle TEXT
    , tipomovimiento INT(1)
    , usuario_id INT(11)
    , conceptopago INT(11)
    , INDEX (conceptopago)
    , FOREIGN KEY (conceptopago)
        REFERENCES conceptopago (conceptopago_id)
        ON DELETE SET NULL
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS estado (
    estado_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , detalle TEXT
    , perfil VARCHAR(100)
    , usuario VARCHAR(100)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS estadomatricula (
    estadomatricula_id INT(11) NOT NULL 
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES matricula (matricula_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES estado (estado_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;