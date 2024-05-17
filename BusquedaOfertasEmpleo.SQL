DELIMITER ;

DROP TABLE IF EXISTS PalabrasXUsuario;
DROP TABLE IF EXISTS PalabrasXAliasProfesionOficio;
DROP TABLE IF EXISTS PalabrasXRegionGeografica;
DROP TABLE IF EXISTS PalabrasXProvincia;
DROP TABLE IF EXISTS PalabrasXCanton;
DROP TABLE IF EXISTS PalabrasXNegocio;
DROP TABLE IF EXISTS Palabras;
DROP TABLE IF EXISTS CaracteresXPalabraSemejante;
DROP TABLE IF EXISTS PalabrasSemejantes;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS OfertasEmpleo;
DROP TABLE IF EXISTS AliasLibresProfesionesOficios;
DROP TABLE IF EXISTS AliasProfesionesOficios;
DROP TABLE IF EXISTS ProfesionesOficios;
DROP TABLE IF EXISTS RegionesGeograficas;
DROP TABLE IF EXISTS Provincias;
DROP TABLE IF EXISTS Cantones;
DROP TABLE IF EXISTS Negocios;

CREATE TABLE IF NOT EXISTS PalabrasSemejantes (
    IdPalabraSemejante INT NOT NULL,
    PalabraSemejante VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    PRIMARY KEY (IdPalabraSemejante),
    CONSTRAINT IX_PalabraSemejante UNIQUE (PalabraSemejante)
);

CREATE TABLE IF NOT EXISTS CaracteresXPalabraSemejante (
    IdPalabraSemejante INT NOT NULL,
    Caracter VARCHAR(1) NOT NULL,
    PRIMARY KEY (IdPalabraSemejante, Caracter),
    FOREIGN KEY (IdPalabraSemejante) REFERENCES PalabrasSemejantes(IdPalabraSemejante)
);

CREATE TABLE IF NOT EXISTS Palabras (
    IdPalabra INT NOT NULL,
    Palabra VARCHAR(100) NOT NULL,
    EstaLibre BIT NOT NULL,
    IdPalabraSemejante INT NOT NULL,
    PRIMARY KEY (IdPalabra),
    CONSTRAINT IX_Palabra UNIQUE (Palabra),
    FOREIGN KEY (IdPalabraSemejante) REFERENCES PalabrasSemejantes(IdPalabraSemejante)
);

CREATE TABLE IF NOT EXISTS Usuarios (
    IdUsuario INT NOT NULL,
    Usuario VARCHAR(50) NOT NULL,
    Cedula VARCHAR(100) NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    Contrasena VARCHAR(50) NOT NULL,    
    EsAdministrador BIT NOT NULL,
    PRIMARY KEY (IdUsuario),
    CONSTRAINT IX_Usuario UNIQUE (Usuario)
);

CREATE TABLE IF NOT EXISTS PalabrasXUsuario (
    IdUsuario INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdUsuario, IdPalabra),
    FOREIGN KEY (IdUsuario) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS ProfesionesOficios (
    IdProfesionOficio INT NOT NULL,
    PRIMARY KEY (IdProfesionOficio)
);

CREATE TABLE IF NOT EXISTS AliasProfesionesOficios (
    IdProfesionOficio INT NOT NULL,
    IdAlias INT NOT NULL,
    Alias VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdProfesionOficio, IdAlias),
    CONSTRAINT IX_AliasProfesionOficio UNIQUE (Alias),
    FOREIGN KEY (IdProfesionOficio) REFERENCES ProfesionesOficios(IdProfesionOficio)
);

CREATE TABLE IF NOT EXISTS AliasLibresProfesionesOficios (
    IdProfesionOficio INT NOT NULL,
    IdAlias INT NOT NULL,
    PRIMARY KEY (IdProfesionOficio, IdAlias),
    FOREIGN KEY (IdProfesionOficio) REFERENCES ProfesionesOficios(IdProfesionOficio)
);

CREATE TABLE IF NOT EXISTS PalabrasXAliasProfesionOficio (
    IdProfesionOficio INT NOT NULL,
    IdAlias INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdProfesionOficio, IdAlias, IdPalabra),
    FOREIGN KEY (IdProfesionOficio, IdAlias) REFERENCES AliasProfesionesOficios(IdProfesionOficio, IdAlias),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS RegionesGeograficas (
    IdRegion INT NOT NULL,
    Region VARCHAR(50) NOT NULL,
    PRIMARY KEY (IdRegion),
    CONSTRAINT IX_Region UNIQUE (Region)
);

CREATE TABLE IF NOT EXISTS PalabrasXRegionGeografica (
    IdRegion INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdRegion, IdPalabra),
    FOREIGN KEY (IdRegion) REFERENCES RegionesGeograficas(IdRegion),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS Provincias (
    IdProvincia INT NOT NULL,
    Provincia VARCHAR(50) NOT NULL,
    PRIMARY KEY (IdProvincia),
    CONSTRAINT IX_Provincia UNIQUE (Provincia)
);

CREATE TABLE IF NOT EXISTS PalabrasXProvincia (
    IdProvincia INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdProvincia, IdPalabra),
    FOREIGN KEY (IdProvincia) REFERENCES Provincias(IdProvincia),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS Cantones (
    IdCanton INT NOT NULL,
    Canton VARCHAR(50) NOT NULL,
    IdRegionGeografica INT NOT NULL,
    IdProvincia INT NOT NULL,
    PRIMARY KEY (IdCanton),
    CONSTRAINT IX_Canton UNIQUE (Canton),
    FOREIGN KEY (IdRegionGeografica) REFERENCES RegionesGeograficas(IdRegion),
    FOREIGN KEY (IdProvincia) REFERENCES Provincias(IdProvincia)
);

CREATE TABLE IF NOT EXISTS PalabrasXCanton (
    IdCanton INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdCanton, IdPalabra),
    FOREIGN KEY (IdCanton) REFERENCES Cantones(IdCanton),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS Negocios (
    IdNegocio INT NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    IdCanton INT NOT NULL,
    Direccion VARCHAR(150) NOT NULL,
    Telefonos VARCHAR(100) NOT NULL,
    PRIMARY KEY (IdNegocio),
    FOREIGN KEY (IdCanton) REFERENCES Cantones(IdCanton)
);

CREATE TABLE IF NOT EXISTS PalabrasXNegocio (
    IdNegocio INT NOT NULL,
    IdPalabra INT NOT NULL,
    PRIMARY KEY (IdNegocio, IdPalabra),
    FOREIGN KEY (IdNegocio) REFERENCES Negocios(IdNegocio),
    FOREIGN KEY (IdPalabra) REFERENCES Palabras(IdPalabra)
);

CREATE TABLE IF NOT EXISTS OfertasEmpleo (
    IdNegocio INT NOT NULL,
    Consecutivo INT NOT NULL,
    EstaVigente BIT NOT NULL,
    IdProfesionOficio INT NOT NULL,
    FechaVencimiento DATE NOT NULL,
    PRIMARY KEY (IdNegocio, Consecutivo),
    FOREIGN KEY (IdNegocio) REFERENCES Negocios(IdNegocio),
    FOREIGN KEY (IdProfesionOficio) REFERENCES ProfesionesOficios(IdProfesionOficio)
);

DROP PROCEDURE IF EXISTS DemeSiguientePalabra;
DROP PROCEDURE IF EXISTS DemePalabraSemejante;
DROP PROCEDURE IF EXISTS ValidarContrasena;
DROP PROCEDURE IF EXISTS Encriptar;
DROP PROCEDURE IF EXISTS SonHilerasIdenticasBitXBit;
DROP PROCEDURE IF EXISTS CambiarContrasena;
DROP PROCEDURE IF EXISTS ValidarLogin;
DROP PROCEDURE IF EXISTS IndexarPalabraSemejante;
DROP PROCEDURE IF EXISTS LiberarPalabrasSemejantesNoUtilizadas;
DROP PROCEDURE IF EXISTS IndexarPalabra;
DROP PROCEDURE IF EXISTS LiberarPalabrasNoUtilizadas;

DROP PROCEDURE IF EXISTS IndexarUsuario;
DROP PROCEDURE IF EXISTS IndexarTodosUsuarios;
DROP PROCEDURE IF EXISTS ValidarCamposUsuario;
DROP PROCEDURE IF EXISTS AltaUsuario;
DROP PROCEDURE IF EXISTS CambioUsuario;

DROP PROCEDURE IF EXISTS IndexarAliasProfesionOficio;
DROP PROCEDURE IF EXISTS IndexarTodosAliasProfesionesOficios;
DROP PROCEDURE IF EXISTS ValidarCamposAliasProfesionOficio;
DROP PROCEDURE IF EXISTS AltaProfesionOficio;
DROP PROCEDURE IF EXISTS AltaAliasProfesionOficio;
DROP PROCEDURE IF EXISTS CambioAliasProfesionOficio;

DROP PROCEDURE IF EXISTS IndexarRegionGeografica;
DROP PROCEDURE IF EXISTS IndexarTodasRegionesGeograficas;
DROP PROCEDURE IF EXISTS ValidarCamposRegionGeografica;
DROP PROCEDURE IF EXISTS AltaRegionGeografica;
DROP PROCEDURE IF EXISTS CambioRegionGeografica;

DROP PROCEDURE IF EXISTS IndexarProvincia;
DROP PROCEDURE IF EXISTS IndexarTodasProvincias;
DROP PROCEDURE IF EXISTS ValidarCamposProvincia;
DROP PROCEDURE IF EXISTS AltaProvincia;
DROP PROCEDURE IF EXISTS CambioProvincia;

DROP PROCEDURE IF EXISTS IndexarCanton;
DROP PROCEDURE IF EXISTS IndexarTodosCantones;
DROP PROCEDURE IF EXISTS ValidarCamposCanton;
DROP PROCEDURE IF EXISTS AltaCanton;
DROP PROCEDURE IF EXISTS CambioCanton;

DROP PROCEDURE IF EXISTS IndexarNegocio;
DROP PROCEDURE IF EXISTS IndexarTodosNegocios;
DROP PROCEDURE IF EXISTS ValidarCamposNegocio;
DROP PROCEDURE IF EXISTS AltaNegocio;
DROP PROCEDURE IF EXISTS CambioNegocio;

DROP PROCEDURE IF EXISTS AltaOfertaEmpleo;
DROP PROCEDURE IF EXISTS CambioOfertaEmpleo;

DELIMITER $$

CREATE PROCEDURE DemeSiguientePalabra(IN HileraXAnalizar VARCHAR(500), IN CaracteresValidos VARCHAR(500), IN IndiceInicial INT, OUT SiguientePalabra VARCHAR(500), OUT NuevoIndice INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NuevoIndice = IndiceInicial;
  SET SiguientePalabra = '';

  WHILE NuevoIndice <= CHAR_LENGTH(HileraXAnalizar) AND POSITION(SUBSTRING(HileraXAnalizar, NuevoIndice, 1) IN CaracteresValidos) < 1 DO
    SET NuevoIndice = NuevoIndice + 1;
  END WHILE;

  WHILE NuevoIndice <= CHAR_LENGTH(HileraXAnalizar) AND POSITION(SUBSTRING(HileraXAnalizar, NuevoIndice, 1) IN CaracteresValidos) >= 1 DO
    SET SiguientePalabra = CONCAT(SiguientePalabra, SUBSTRING(HileraXAnalizar, NuevoIndice, 1));
    SET NuevoIndice = NuevoIndice + 1;
  END WHILE;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT SiguientePalabra, NuevoIndice;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE DemePalabraSemejante(IN Palabra VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), OUT PalabraSemejante VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
/* Advertencia: La hilera 'TuplasReemplazos' debe contener espacios en blanco donde se requiera, pues este procedimiento no reemplaza ninguno */

  SET PalabraSemejante = LOWER(Palabra);
  SET TuplasReemplazos = LOWER(TuplasReemplazos);
  /* Los anteriores dos reemplazos son para que no se distinga entre caracteres en minúscula y mayúscula */

  SET @Tupla = SUBSTRING_INDEX(TuplasReemplazos, SeparadorTuplas, 1);
  
  WHILE CHAR_LENGTH(@Tupla) >= 1 DO
    SET @AReemplazar = SUBSTRING_INDEX(@Tupla, SeparadorColumnas, 1);
    
    IF CHAR_LENGTH(@AReemplazar) >= 1 AND INSTR(PalabraSemejante, @AReemplazar) >= 1 THEN
      SET @Reemplazo = SUBSTRING(@Tupla, CHAR_LENGTH(@AReemplazar) + CHAR_LENGTH(SeparadorColumnas) + 1);
      SET PalabraSemejante = REPLACE(PalabraSemejante, @AReemplazar, @Reemplazo);
    END IF;

    SET TuplasReemplazos = SUBSTRING(TuplasReemplazos, CHAR_LENGTH(@Tupla) + CHAR_LENGTH(SeparadorTuplas) + 1);
    SET @Tupla = SUBSTRING_INDEX(TuplasReemplazos, SeparadorTuplas, 1);
  END WHILE;
      
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT PalabraSemejante;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE ValidarContrasena(IN Contrasena VARCHAR(500), OUT NumError INT, IN LongitudMinimaContrasena INT, IN CaracteresEspeciales VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  
  SET @CaracteresAlfabeticos = "abcdefghijklmnñopqrstuvwxyzáéíóúü";
  SET @CaracteresAlfabeticosMayuscula = UPPER(@CaracteresAlfabeticos);
  SET @CaracteresAlfabeticosMinuscula = LOWER(@CaracteresAlfabeticos);
  SET @DigitosDecimales = "0123456789";
  
  SET @ContadorCaracteresAlfabeticosMayuscula = 0;
  SET @ContadorCaracteresAlfabeticosMinuscula = 0;
  SET @ContadorDigitosDecimales = 0;
  SET @ContadorCaracteresEspeciales = 0;
  
  IF (CHAR_LENGTH(Contrasena) < LongitudMinimaContrasena) THEN
    SET NumError = 1;
  ELSE
    SET @Indice = 1;

    WHILE @Indice <= CHAR_LENGTH(Contrasena) DO
      SET @SiguienteCaracter = MID(Contrasena, @Indice, 1);
    
      IF SUBSTRING_INDEX(@CaracteresAlfabeticosMayuscula, @SiguienteCaracter, 1) != @CaracteresAlfabeticosMayuscula THEN
        SET @ContadorCaracteresAlfabeticosMayuscula = @ContadorCaracteresAlfabeticosMayuscula + 1;
      
      ELSEIF SUBSTRING_INDEX(@CaracteresAlfabeticosMinuscula, @SiguienteCaracter, 1) != @CaracteresAlfabeticosMinuscula THEN
        SET @ContadorCaracteresAlfabeticosMinuscula = @ContadorCaracteresAlfabeticosMinuscula + 1;

      ELSEIF SUBSTRING_INDEX(@DigitosDecimales, @SiguienteCaracter, 1) != @DigitosDecimales THEN
        SET @ContadorDigitosDecimales = @ContadorDigitosDecimales + 1;

      ELSEIF SUBSTRING_INDEX(CaracteresEspeciales, @SiguienteCaracter, 1) != CaracteresEspeciales THEN
        SET @ContadorCaracteresEspeciales = @ContadorCaracteresEspeciales + 1;
      
      END IF;

      SET @Indice = @Indice + 1;
    END WHILE;
    
    IF @ContadorCaracteresAlfabeticosMayuscula < 1 OR @ContadorCaracteresAlfabeticosMinuscula < 1 OR @ContadorDigitosDecimales < 1 OR @ContadorCaracteresEspeciales < 1 THEN
      SET NumError = 2;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
      SELECT NumError;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE Encriptar(IN HileraXEncriptar VARCHAR(500), IN CodigoEncriptacion VARCHAR(500), OUT HileraEncriptada VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET HileraEncriptada = '';
  
  IF CHAR_LENGTH(CodigoEncriptacion) > 0 THEN
    SET @MaximoNumeroCaracteresXCodificar = POWER(2, 7);
    SET @IndiceActual = 0;
    
    WHILE @IndiceActual < CHAR_LENGTH(HileraXEncriptar) DO
      SET @PosicionEnCodigoEncriptacion = MOD(@IndiceActual, CHAR_LENGTH(CodigoEncriptacion)) + 1;
      SET @SiguienteCaracterEnCodigoEncriptacion = MID(CodigoEncriptacion, @PosicionEnCodigoEncriptacion, 1);
      SET @SiguienteCaracterEnHileraXEncriptar = MID(HileraXEncriptar, @IndiceActual + 1, 1);
      SET @SiguienteCodigoEnHileraEncriptada = ORD(@SiguienteCaracterEnCodigoEncriptacion) + ORD(@SiguienteCaracterEnHileraXEncriptar);
      SET @SiguienteCodigoEnHileraEncriptada = MOD(@SiguienteCodigoEnHileraEncriptada, @MaximoNumeroCaracteresXCodificar);
      SET @SiguienteCaracterEnHileraEncriptada = CHAR(@SiguienteCodigoEnHileraEncriptada);
      SET HileraEncriptada = CONCAT(HileraEncriptada, @SiguienteCaracterEnHileraEncriptada);
      SET @IndiceActual = @IndiceActual + 1;
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT HileraEncriptada;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE SonHilerasIdenticasBitXBit(IN HileraComparar1 VARCHAR(500), IN HileraComparar2 VARCHAR(500), OUT SonIguales BIT(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET SonIguales = 1;
  
  IF CHAR_LENGTH(HileraComparar1) != CHAR_LENGTH(HileraComparar2) THEN
    SET SonIguales = 0;
  ELSE
    SET @IndiceActual = 1;
    
    WHILE SonIguales = 1 AND @IndiceActual <= CHAR_LENGTH(HileraComparar1) DO
      SET @SiguienteCaracterEnHilera1 = MID(HileraComparar1, @IndiceActual, 1);
      SET @SiguienteCaracterEnHilera2 = MID(HileraComparar2, @IndiceActual, 1);
      
      IF ORD(@SiguienteCaracterEnHilera1) != ORD(@SiguienteCaracterEnHilera2) THEN
          SET SonIguales = 0;
      END IF;
      
      SET @IndiceActual = @IndiceActual + 1;
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT SonIguales;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE CambiarContrasena(IN UnIdUsuario INT, IN ContrasenaAnterior VARCHAR(500), IN NuevaContrasena VARCHAR(500), IN ConfirmacionNuevaContrasena VARCHAR(500), OUT NumError INT, IN CodigoEncriptacion VARCHAR(500), IN LongitudMinimaContrasena INT, IN CaracteresEspeciales VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET NumError = 0;
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
  
  CALL Encriptar(ContrasenaAnterior, CodigoEncriptacion, @ContrasenaAnteriorEncriptada, 0);
  
  SELECT COUNT(1) FROM Usuarios WHERE IdUsuario = UnIdUsuario AND Contrasena = @ContrasenaAnteriorEncriptada INTO @CantidadUsuarios;
  
  IF @CantidadUsuarios != 1 THEN
    SET NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    
    CALL SonHilerasIdenticasBitXBit(NuevaContrasena, ConfirmacionNuevaContrasena, @SonIguales, 0);
    
    IF @SonIguales = 0 THEN
      SET NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      
      CALL ValidarContrasena(NuevaContrasena, NumError, LongitudMinimaContrasena, CaracteresEspeciales, 0);
      
      IF NumError != 0 THEN
        SET NumError = @NumBaseError + NumError;
      END IF;
    END IF;
  END IF;
  
  IF NumError = 0 THEN
    CALL Encriptar(NuevaContrasena, CodigoEncriptacion, @NuevaContrasenaEncriptada, 0);
    UPDATE Usuarios SET Contrasena = @NuevaContrasenaEncriptada WHERE IdUsuario = UnIdUsuario;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;

  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE ValidarLogin(IN UnUsuario VARCHAR(500), IN UnaContrasena VARCHAR(500), IN CodigoEncriptacion VARCHAR(500), OUT UsuarioContrasenaExiste BIT(1), OUT UnIdUsuario INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET UsuarioContrasenaExiste = 0;
  CALL Encriptar(UnaContrasena, CodigoEncriptacion, @UnaContrasenaEncriptada, 0);
  
  SELECT COUNT(1), MIN(IdUsuario) FROM Usuarios WHERE Usuario = UnUsuario AND Contrasena = @UnaContrasenaEncriptada INTO @CantidadUsuarios, UnIdUsuario;
  
  IF @CantidadUsuarios = 1 THEN
    SET UsuarioContrasenaExiste = 1;
  END IF;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT UsuarioContrasenaExiste, UnIdUsuario;
  END IF;
END;

DELIMITER $$

CREATE PROCEDURE IndexarPalabraSemejante(IN UnaPalabraSemejante VARCHAR(500), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SET @ExistePalabraSemejante = 1;
  SELECT MIN(IdPalabraSemejante) FROM PalabrasSemejantes WHERE PalabraSemejante = UnaPalabraSemejante INTO @IdPalabraSemejante;
  
  IF ISNULL(@IdPalabraSemejante) = 1 THEN
    SELECT MIN(IdPalabraSemejante) FROM PalabrasSemejantes WHERE EstaLibre = 1 INTO @IdPalabraSemejante;
  END IF;
  
  IF ISNULL(@IdPalabraSemejante) = 1 THEN
    SET @ExistePalabraSemejante = 0;
    SELECT MAX(IdPalabraSemejante) FROM PalabrasSemejantes INTO @IdPalabraSemejante;
      
    IF ISNULL(@IdPalabraSemejante) = 1 THEN
      SET @IdPalabraSemejante = 0;
    END IF;
    
    SET @IdPalabraSemejante = @IdPalabraSemejante + 1;
  END IF;
  
  IF @ExistePalabraSemejante = 0 THEN
    INSERT INTO PalabrasSemejantes(IdPalabraSemejante, PalabraSemejante, EstaLibre) VALUES(@IdPalabraSemejante, UnaPalabraSemejante, 0);
  ELSE
    UPDATE PalabrasSemejantes SET PalabraSemejante = UnaPalabraSemejante, EstaLibre = 0 WHERE IdPalabraSemejante = @IdPalabraSemejante;
  END IF;
    
  DELETE FROM CaracteresXPalabraSemejante WHERE IdPalabraSemejante = @IdPalabraSemejante;

  IF CHAR_LENGTH(UnaPalabraSemejante) = 0 THEN
    INSERT INTO CaracteresXPalabraSemejante(IdPalabraSemejante, Caracter) VALUES(@IdPalabraSemejante, '');
  ELSE
    SET @IndiceActual = 1;
    SET @CaracteresInsertados = "";
      
    WHILE @IndiceActual <= CHAR_LENGTH(UnaPalabraSemejante) DO
      SET @SiguienteCaracter = MID(UnaPalabraSemejante, @IndiceActual, 1);
        
      IF INSTR(@CaracteresInsertados, @SiguienteCaracter) < 1 THEN
        INSERT INTO CaracteresXPalabraSemejante(IdPalabraSemejante, Caracter) VALUES(@IdPalabraSemejante, @SiguienteCaracter);
        SET @CaracteresInsertados = CONCAT(@CaracteresInsertados, @SiguienteCaracter);
      END IF;
        
      SET @IndiceActual = @IndiceActual + 1;
    END WHILE;
  END IF;
      
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdPalabraSemejante, a.PalabraSemejante, a.EstaLibre, b.Caracter
    FROM PalabrasSemejantes a, CaracteresXPalabraSemejante b
    WHERE a.IdPalabraSemejante = b.IdPalabraSemejante
    AND a.PalabraSemejante = UnaPalabraSemejante;
  END IF;
  
  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE LiberarPalabrasSemejantesNoUtilizadas()
BEGIN
  START TRANSACTION;
  
  UPDATE PalabrasSemejantes
  SET EstaLibre = 1
  WHERE NOT IdPalabraSemejante IN (SELECT IdPalabraSemejante FROM Palabras WHERE EstaLibre = 0);
  
  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE IndexarPalabra(IN UnaPalabra VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SET @ExistePalabra = 1;
  SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = UnaPalabra INTO @IdPalabra;
  
  IF ISNULL(@IdPalabra) = 1 THEN
    SELECT MIN(IdPalabra) FROM Palabras WHERE EstaLibre = 1 INTO @IdPalabra;
  END IF;
  
  IF ISNULL(@IdPalabra) = 1 THEN
    SET @ExistePalabra = 0;
    SELECT MAX(IdPalabra) FROM Palabras INTO @IdPalabra;
      
    IF ISNULL(@IdPalabra) = 1 THEN
      SET @IdPalabra = 0;
    END IF;
    
    SET @IdPalabra = @IdPalabra + 1;
  END IF;

  CALL DemePalabraSemejante(UnaPalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, @PalabraSemejante, 0);
  CALL IndexarPalabraSemejante(@PalabraSemejante, 0);

  SELECT MIN(IdPalabraSemejante) FROM PalabrasSemejantes WHERE PalabraSemejante = @PalabraSemejante INTO @IdPalabraSemejante;

  IF @ExistePalabra = 0 THEN
    INSERT INTO Palabras(IdPalabra, Palabra, EstaLibre, IdPalabraSemejante) VALUES(@IdPalabra, UnaPalabra, 0, @IdPalabraSemejante);
  ELSE
    UPDATE Palabras SET Palabra = UnaPalabra, EstaLibre = 0, IdPalabraSemejante = @IdPalabraSemejante WHERE IdPalabra = @IdPalabra;
  END IF;
  
  CALL LiberarPalabrasSemejantesNoUtilizadas();

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdPalabra, a.Palabra, a.EstaLibre, b.IdPalabraSemejante, b.PalabraSemejante, b.EstaLibre, c.Caracter
    FROM Palabras a, PalabrasSemejantes b, CaracteresXPalabraSemejante c
    WHERE a.IdPalabraSemejante = b.IdPalabraSemejante
    AND b.IdPalabraSemejante = c.IdPalabraSemejante
    AND a.Palabra = UnaPalabra;
  END IF;
  
  COMMIT;
END;

DELIMITER $$

CREATE PROCEDURE LiberarPalabrasNoUtilizadas()
BEGIN
  START TRANSACTION;
  
  UPDATE Palabras
  SET EstaLibre = 1
  WHERE NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXUsuario)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXAliasProfesionOficio)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXRegionGeografica)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXProvincia)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXCanton)
  AND NOT IdPalabra IN (SELECT IdPalabra FROM PalabrasXNegocio);

  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarUsuario(IN UnIdUsuario INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXUsuario WHERE IdUsuario = UnIdUsuario;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(CONCAT(Usuario, SeparadorPalabras, Cedula, SeparadorPalabras, Nombre)) FROM Usuarios WHERE IdUsuario = UnIdUsuario INTO @CantidadUsuarios, @PalabrasXIndexar;
  
  IF (@CantidadUsuarios = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXUsuario(IdUsuario, IdPalabra) VALUES(UnIdUsuario, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdUsuario, a.Usuario, a.Cedula, a.Nombre, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Usuarios a, PalabrasXUsuario b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdUsuario = b.IdUsuario
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdUsuario = UnIdUsuario;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosUsuarios(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE UsuariosXIndexar
  SELECT IdUsuario
  FROM Usuarios
  ORDER BY IdUsuario ASC;

  SELECT MIN(IdUsuario) FROM UsuariosXIndexar INTO @IdUsuario;
  
  WHILE ISNULL(@IdUsuario) = 0 DO
    CALL IndexarUsuario(@IdUsuario, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdUsuario) FROM UsuariosXIndexar WHERE IdUsuario > @IdUsuario INTO @IdUsuario;
  END WHILE;
  
  DROP TEMPORARY TABLE UsuariosXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdUsuario, a.Usuario, a.Cedula, a.Nombre, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Usuarios a, PalabrasXUsuario b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdUsuario = b.IdUsuario
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposUsuario(IN UnUsuario VARCHAR(50), IN UnaCedula VARCHAR(100), IN UnNombre VARCHAR(100), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnUsuario, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  ELSE
    CALL DemeSiguientePalabra(UnaCedula, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

    IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
      SET NumError = 2;
    ELSE
      CALL DemeSiguientePalabra(UnNombre, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

      IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
        SET NumError = 3;
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaUsuario(IN UnUsuario VARCHAR(50), IN UnaCedula VARCHAR(100), IN UnNombre VARCHAR(100), BitEsAdministrador BIT(1), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdUsuario) FROM Usuarios WHERE Usuario = UnUsuario INTO @IdUsuario;
  
  IF ISNULL(@IdUsuario) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposUsuario(UnUsuario, UnaCedula, UnNombre, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      SELECT MAX(IdUsuario) FROM Usuarios INTO @IdUsuario;
  
      IF ISNULL(@IdUsuario) = 1 THEN
        SET @IdUsuario = 0;
      END IF;
    
      SET @IdUsuario = @IdUsuario + 1;

      INSERT INTO Usuarios(IdUsuario, Usuario, Cedula, Nombre, Contrasena, EsAdministrador) VALUES(@IdUsuario, UnUsuario, UnaCedula, UnNombre, '', BitEsAdministrador);
      CALL IndexarUsuario(@IdUsuario, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdUsuario;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioUsuario(IN UnIdUsuario INT, IN UnUsuario VARCHAR(50), IN UnaCedula VARCHAR(100), IN UnNombre VARCHAR(100), IN BitEsAdministrador BIT(1), BorrarContrasena BIT(1), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
   
  SET @NumError = 0;
  SELECT MIN(IdUsuario) FROM Usuarios WHERE IdUsuario <> UnIdUsuario AND Usuario = UnUsuario INTO @IdUsuario;
  
  IF ISNULL(@IdUsuario) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdUsuario) FROM Usuarios WHERE IdUsuario = UnIdUsuario INTO @IdUsuario;
    
    IF ISNULL(@IdUsuario) = 1 THEN
      SET @NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposUsuario(UnUsuario, UnaCedula, UnNombre, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        UPDATE Usuarios SET Usuario = UnUsuario, Cedula = UnaCedula, Nombre = UnNombre, EsAdministrador = BitEsAdministrador WHERE IdUsuario = @IdUsuario;
      
        IF BorrarContrasena = 1 THEN
          UPDATE Usuarios SET Contrasena = '' WHERE IdUsuario = @IdUsuario;
        END IF;
      
        CALL IndexarUsuario(@IdUsuario, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;    
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarAliasProfesionOficio(IN UnIdProfesionOficio INT, IN UnIdAlias INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXAliasProfesionOficio WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = UnIdAlias;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Alias) FROM AliasProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = UnIdAlias INTO @CantidadAlias, @PalabrasXIndexar;
  
  IF (@CantidadAlias = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXAliasProfesionOficio(IdProfesionOficio, IdAlias, IdPalabra) VALUES(UnIdProfesionOficio, UnIdAlias, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdProfesionOficio, a.IdAlias, a.Alias, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM AliasProfesionesOficios a, PalabrasXAliasProfesionOficio b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdProfesionOficio = b.IdProfesionOficio
    AND a.IdAlias = b.IdAlias
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdProfesionOficio = UnIdProfesionOficio
    AND a.IdAlias = UnIdAlias;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosAliasProfesionesOficios(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE AliasProfesionesOficiosXIndexar
  SELECT IdProfesionOficio, IdAlias
  FROM AliasProfesionesOficios
  ORDER BY IdProfesionOficio ASC, IdAlias ASC;

  SET @IdProfesionOficio = 0;
  SET @IdAlias = 0;

  WHILE ISNULL(@IdProfesionOficio) = 0 AND ISNULL(@IdAlias) = 0 DO
    SELECT MIN(IdProfesionOficio) FROM AliasProfesionesOficiosXIndexar INTO @IdProfesionOficio;

    IF ISNULL(@IdProfesionOficio) = 0 THEN
      SELECT MIN(IdAlias) FROM AliasProfesionesOficiosXIndexar WHERE IdProfesionOficio = @IdProfesionOficio INTO @IdAlias;
    END IF;

    IF ISNULL(@IdProfesionOficio) = 0 AND ISNULL(@IdAlias) = 0 THEN
      CALL IndexarAliasProfesionOficio(@IdProfesionOficio, @IdAlias, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      DELETE FROM AliasProfesionesOficiosXIndexar WHERE IdProfesionOficio = @IdProfesionOficio AND IdAlias = @IdAlias;
    END IF;
  END WHILE;
  
  DROP TEMPORARY TABLE AliasProfesionesOficiosXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdProfesionOficio, a.IdAlias, a.Alias, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM AliasProfesionesOficios a, PalabrasXAliasProfesionOficio b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdProfesionOficio = b.IdProfesionOficio
    AND a.IdAlias = b.IdAlias
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposAliasProfesionOficio(IN UnAlias VARCHAR(100), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnAlias, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1; /* Si el alias no tiene caracteres válidos se indica el error número 1 */
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaProfesionOficio(IN UnAlias VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdProfesionOficio), MIN(IdAlias) FROM AliasProfesionesOficios WHERE Alias = UnAlias INTO @IdProfesionOficio, @IdAlias;
  
  IF ISNULL(@IdAlias) = 0 THEN
    SET @NumError = @NumBaseError + 1; /* Si se encuentra un alias con el nombre enviado como parámetro, se indica el error */

  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposAliasProfesionOficio(UnAlias, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      SELECT MAX(IdProfesionOficio) FROM ProfesionesOficios INTO @IdProfesionOficio;
  
      IF ISNULL(@IdProfesionOficio) = 1 THEN
        SET @IdProfesionOficio = 0;
      END IF;
    
      SET @IdProfesionOficio = @IdProfesionOficio + 1;
      SET @IdAlias = 1;

      INSERT INTO ProfesionesOficios(IdProfesionOficio) VALUES(@IdProfesionOficio);
      INSERT INTO AliasProfesionesOficios(IdProfesionOficio, IdAlias, Alias) VALUES(@IdProfesionOficio, @IdAlias, UnAlias);
      CALL IndexarAliasProfesionOficio(@IdProfesionOFicio, @IdAlias, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdProfesionOficio, @IdAlias;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE AltaAliasProfesionOficio(IN UnIdProfesionOficio INT, IN UnAlias VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdProfesionOficio) FROM ProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio INTO @IdProfesionOficioConsulta;

  IF ISNULL(@IdProfesionOficioConsulta) = 1 THEN
    SET @NumError = @NumBaseError + 1;
    /* Si no se encuentra la profesión u oficio enviada como parámetro, se indica el error */
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    SELECT MIN(IdProfesionOficio), MIN(IdAlias) FROM AliasProfesionesOficios WHERE Alias = UnAlias AND IdProfesionOficio != UnIdProfesionOficio INTO @IdProfesionOficioConsulta, @IdAlias;
  
    IF ISNULL(@IdProfesionOficioConsulta) = 0 THEN
      SET @NumError = @NumBaseError + 1;
      /* Si se encuentra una profesión u oficio distinta de la enviada como parámetro con un alias con el nombre enviado como parámetro, se indica el error */
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposAliasProfesionOficio(UnAlias, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        SELECT MIN(IdAlias) FROM AliasProfesionesOficios WHERE Alias = UnAlias AND IdProfesionOficio = UnIdProfesionOficio INTO @IdAlias;

        IF ISNULL(@IdAlias) = 0 THEN
          UPDATE AliasProfesionesOficios SET Alias = UnAlias WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = @IdAlias;
          /* Si se encuentra el alias, entonces se actualiza el nombre, para manejar el caso de que se cambien minúsculas y mayúsculas, por ejemplo */
        ELSE
          SELECT MIN(IdAlias) FROM AliasLibresProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio INTO @IdAlias;
  
          IF ISNULL(@IdAlias) = 0 THEN
            /* Si se encuentra un alias libre en la misma profesión u oficio, se reusa */
            DELETE FROM AliasLibresProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = @IdAlias;
            INSERT INTO AliasProfesionesOficios(IdProfesionOficio, IdAlias, Alias) VALUES(UnIdProfesionOficio, @IdAlias, UnAlias);
          ELSE
            SELECT MAX(IdAlias) FROM AliasProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio INTO @IdAlias;

            IF ISNULL(@IdAlias) = 1 THEN
              SET @IdAlias = 0;
            END IF;

            SET @IdAlias = @IdAlias + 1;
            INSERT INTO AliasProfesionesOficios(IdProfesionOficio, IdAlias, Alias) VALUES(UnIdProfesionOficio, @IdAlias, UnAlias);
          END IF;
        END IF;

        CALL IndexarAliasProfesionOficio(UnIdProfesionOFicio, @IdAlias, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdAlias;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioAliasProfesionOficio(IN UnIdProfesionOficio INT, IN UnIdAlias INT, IN UnAlias VARCHAR(100), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  SET @RetornarResultadosEnSelect = RetornarResultadosEnSelect;
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdProfesionOficio) FROM ProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio INTO @IdProfesionOficioConsulta;

  IF ISNULL(@IdProfesionOficioConsulta) = 1 THEN
    SET @NumError = @NumBaseError + 1;
    /* Si no se encuentra la profesión u oficio enviada como parámetro, se indica el error */
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    SELECT MIN(IdProfesionOficio), MIN(IdAlias) FROM AliasProfesionesOficios WHERE Alias = UnAlias AND IdProfesionOficio != UnIdProfesionOficio INTO @IdProfesionOficioConsulta, @IdAlias;
  
    IF ISNULL(@IdProfesionOficioConsulta) = 0 THEN
      SET @NumError = @NumBaseError + 1;
      /* Si se encuentra una profesión u oficio distinta de la enviada como parámetro con un alias con el nombre enviado como parámetro, se indica el error */
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposAliasProfesionOficio(UnAlias, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        SELECT MIN(IdAlias) FROM AliasProfesionesOficios WHERE Alias = UnAlias AND IdProfesionOficio = UnIdProfesionOficio INTO @IdNuevoAlias;
        SELECT MIN(IdAlias) FROM AliasProfesionesOficios WHERE IdAlias = UnIdAlias AND IdProfesionOficio = UnIdProfesionOficio INTO @IdAliasOriginal;
        SET @IndexarAlias = 1;

        IF ISNULL(@IdAliasOriginal) = 0 AND ISNULL(@IdNuevoAlias) = 1 THEN
          SET @IdNuevoAlias = @IdAliasOriginal;
        END IF;

        IF ISNULL(@IdAliasOriginal) = 1 AND ISNULL(@IdNuevoAlias) = 0 THEN
          SET @IdAliasOriginal = @IdNuevoAlias;
        END IF;

        IF ISNULL(@IdAliasOriginal) = 0 AND ISNULL(@IdNuevoAlias) = 0 THEN
          IF @IdAliasOriginal != @IdNuevoAlias THEN
            /* Se elimina el alias original de la tabla de alias y se inserta en la tabla de alias libres. También se elimina de la tabla de palabras por alias */
            DELETE FROM PalabrasXAliasProfesionOficio WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = @IdAliasOriginal;
            DELETE FROM AliasProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = @IdAliasOriginal;
            INSERT INTO AliasLibresProfesionesOficios(IdProfesionOficio, IdAlias) VALUES(UnIdProfesionOficio, @IdAliasOriginal);
          END IF;

          UPDATE AliasProfesionesOficios SET Alias = UnAlias WHERE IdProfesionOficio = UnIdProfesionOficio AND IdAlias = @IdNuevoAlias;
          /* Se actualiza el nombre del alias, cuyo id no cambia. Esto se hace por ejemplo para manejar el caso de cambio de mayúsculas y minúsculas en el nombre */
        END IF;

        IF ISNULL(@IdNuevoAlias) = 1 AND ISNULL(@IdAliasOriginal) = 1 THEN
          /* Si no existe el alias, se inserta */
          CALL AltaAliasProfesionOficio(UnIdProfesionOficio, UnAlias, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, RetornarResultadosEnSelect);
          SET @IndexarAlias = 0;
          SET @RetornarResultadosEnSelect = 0 /* Se retornan los resultados, si se solicita, en el procedimiento invocado como parámetro y no en este */;
        END IF;

        IF @IndexarAlias THEN
          CALL IndexarAliasProfesionOficio(UnIdProfesionOFicio, @IdNuevoAlias, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
        END IF;
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;
  END IF;
  
  IF @RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdNuevoAlias;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarRegionGeografica(IN UnIdRegion INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXRegionGeografica WHERE IdRegion = UnIdRegion;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Region) FROM RegionesGeograficas WHERE IdRegion = UnIdRegion INTO @CantidadRegiones, @PalabrasXIndexar;

  IF (@CantidadRegiones = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXRegionGeografica(IdRegion, IdPalabra) VALUES(UnIdRegion, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdRegion, a.Region, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM RegionesGeograficas a, PalabrasXRegionGeografica b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdRegion = b.IdRegion
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdRegion = UnIdRegion;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodasRegionesGeograficas(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE RegionesGeograficasXIndexar
  SELECT IdRegion
  FROM RegionesGeograficas
  ORDER BY IdRegion ASC;

  SELECT MIN(IdRegion) FROM RegionesGeograficasXIndexar INTO @IdRegion;
  
  WHILE ISNULL(@IdRegion) = 0 DO
    CALL IndexarRegionGeografica(@IdRegion, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdRegion) FROM RegionesGeograficasXIndexar WHERE IdRegion > @IdRegion INTO @IdRegion;
  END WHILE;
  
  DROP TEMPORARY TABLE RegionesGeograficasXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdRegion, a.Region, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM RegionesGeograficas a, PalabrasXRegionGeografica b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdRegion = b.IdRegion
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposRegionGeografica(IN UnaRegion VARCHAR(50), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnaRegion, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaRegionGeografica(IN UnaRegion VARCHAR(50), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdRegion) FROM RegionesGeograficas WHERE Region = UnaRegion INTO @IdRegion;
  
  IF ISNULL(@IdRegion) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposRegionGeografica(UnaRegion, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      SELECT MAX(IdRegion) FROM RegionesGeograficas INTO @IdRegion;
  
      IF ISNULL(@IdRegion) = 1 THEN
        SET @IdRegion = 0;
      END IF;
    
      SET @IdRegion = @IdRegion + 1;

      INSERT INTO RegionesGeograficas(IdRegion, Region) VALUES(@IdRegion, UnaRegion);
      CALL IndexarRegionGeografica(@IdRegion, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdRegion;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioRegionGeografica(IN UnIdRegion INT, IN UnaRegion VARCHAR(50), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
   
  SET @NumError = 0;
  SELECT MIN(IdRegion) FROM RegionesGeograficas WHERE IdRegion <> UnIdRegion AND Region = UnaRegion INTO @IdRegion;
  
  IF ISNULL(@IdRegion) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdRegion) FROM RegionesGeograficas WHERE IdRegion = UnIdRegion INTO @IdRegion;
    
    IF ISNULL(@IdRegion) = 1 THEN
      SET @NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposRegionGeografica(UnaRegion, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        UPDATE RegionesGeograficas SET Region = UnaRegion WHERE IdRegion = @IdRegion;      
        CALL IndexarRegionGeografica(@IdRegion, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;    
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarProvincia(IN UnIdProvincia INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXProvincia WHERE IdProvincia = UnIdProvincia;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Provincia) FROM Provincias WHERE IdProvincia = UnIdProvincia INTO @CantidadProvincias, @PalabrasXIndexar;

  IF (@CantidadProvincias = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXProvincia(IdProvincia, IdPalabra) VALUES(UnIdProvincia, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdProvincia, a.Provincia, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Provincias a, PalabrasXProvincia b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdProvincia = b.IdProvincia
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdProvincia = UnIdProvincia;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodasProvincias(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE ProvinciasXIndexar
  SELECT IdProvincia
  FROM Provincias
  ORDER BY IdProvincia ASC;

  SELECT MIN(IdProvincia) FROM ProvinciasXIndexar INTO @IdProvincia;
  
  WHILE ISNULL(@IdProvincia) = 0 DO
    CALL IndexarProvincia(@IdProvincia, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdProvincia) FROM ProvinciasXIndexar WHERE IdProvincia > @IdProvincia INTO @IdProvincia;
  END WHILE;
  
  DROP TEMPORARY TABLE ProvinciasXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdProvincia, a.Provincia, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Provincias a, PalabrasXProvincia b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdProvincia = b.IdProvincia
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposProvincia(IN UnaProvincia VARCHAR(50), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnaProvincia, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaProvincia(IN UnaProvincia VARCHAR(50), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdProvincia) FROM Provincias WHERE Provincia = UnaProvincia INTO @IdProvincia;
  
  IF ISNULL(@IdProvincia) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposProvincia(UnaProvincia, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      SELECT MAX(IdProvincia) FROM Provincias INTO @IdProvincia;
  
      IF ISNULL(@IdProvincia) = 1 THEN
        SET @IdProvincia = 0;
      END IF;
    
      SET @IdProvincia = @IdProvincia + 1;

      INSERT INTO Provincias(IdProvincia, Provincia) VALUES(@IdProvincia, UnaProvincia);
      CALL IndexarProvincia(@IdProvincia, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdProvincia;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioProvincia(IN UnIdProvincia INT, IN UnaProvincia VARCHAR(50), IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
   
  SET @NumError = 0;
  SELECT MIN(IdProvincia) FROM Provincias WHERE IdProvincia <> UnIdProvincia AND Provincia = UnaProvincia INTO @IdProvincia;
  
  IF ISNULL(@IdProvincia) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdProvincia) FROM Provincias WHERE IdProvincia = UnIdProvincia INTO @IdProvincia;
    
    IF ISNULL(@IdProvincia) = 1 THEN
      SET @NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposProvincia(UnaProvincia, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        UPDATE Provincias SET Provincia = UnaProvincia WHERE IdProvincia = @IdProvincia;
        CALL IndexarProvincia(@IdProvincia, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;    
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarCanton(IN UnIdCanton INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXCanton WHERE IdCanton = UnIdCanton;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Canton) FROM Cantones WHERE IdCanton = UnIdCanton INTO @CantidadCantones, @PalabrasXIndexar;
  
  IF (@CantidadCantones = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXCanton(IdCanton, IdPalabra) VALUES(UnIdCanton, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdCanton, a.Canton, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Cantones a, PalabrasXCanton b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdCanton = b.IdCanton
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdCanton = UnIdCanton;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosCantones(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE CantonesXIndexar
  SELECT IdCanton
  FROM Cantones
  ORDER BY IdCanton ASC;

  SELECT MIN(IdCanton) FROM CantonesXIndexar INTO @IdCanton;
  
  WHILE ISNULL(@IdCanton) = 0 DO
    CALL IndexarCanton(@IdCanton, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdCanton) FROM CantonesXIndexar WHERE IdCanton > @IdCanton INTO @IdCanton;
  END WHILE;
  
  DROP TEMPORARY TABLE CantonesXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdCanton, a.Canton, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Cantones a, PalabrasXCanton b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdCanton = b.IdCanton
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposCanton(IN UnCanton VARCHAR(50), IN UnIdRegionGeografica INT, IN UnIdProvincia INT, IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnCanton, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN
    SET NumError = 1;
  ELSE
    SELECT MIN(IdRegion) FROM RegionesGeograficas WHERE IdRegion = UnIdRegionGeografica INTO @IdRegionGeografica;
  
    IF ISNULL(@IdRegionGeografica) = 1 THEN
      SET NumError = 2;
    ELSE
      SELECT MIN(IdProvincia) FROM Provincias WHERE IdProvincia = UnIdProvincia INTO @IdProvincia;
  
      IF ISNULL(@IdProvincia) = 1 THEN
        SET NumError = 3;
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaCanton(IN UnCanton VARCHAR(50), IN UnIdRegionGeografica INT, IN UnIdProvincia INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdCanton) FROM Cantones WHERE Canton = UnCanton INTO @IdCanton;
  
  IF ISNULL(@IdCanton) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposCanton(UnCanton, UnIdRegionGeografica, UnIdProvincia, CaracteresValidos, @NumError, 0);

    IF @NumError = 0 THEN
      SELECT MAX(IdCanton) FROM Cantones INTO @IdCanton;
  
      IF ISNULL(@IdCanton) = 1 THEN
        SET @IdCanton = 0;
      END IF;
    
      SET @IdCanton = @IdCanton + 1;

      INSERT INTO Cantones(IdCanton, Canton, IdRegionGeografica, IdProvincia) VALUES(@IdCanton, UnCanton, UnIdRegionGeografica, UnIdProvincia);
      CALL IndexarCanton(@IdCanton, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    ELSE
      SET @NumError = @NumBaseError + @NumError;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdCanton;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioCanton(IN UnIdCanton INT, IN UnCanton VARCHAR(50), IN UnIdRegionGeografica INT, IN UnIdProvincia INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;

  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;
   
  SET @NumError = 0;
  SELECT MIN(IdCanton) FROM Cantones WHERE IdCanton <> UnIdCanton AND Canton = UnCanton INTO @IdCanton;
  
  IF ISNULL(@IdCanton) = 0 THEN
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdCanton) FROM Cantones WHERE IdCanton = UnIdCanton INTO @IdCanton;
    
    IF ISNULL(@IdCanton) = 1 THEN
      SET @NumError = @NumBaseError + 1;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      CALL ValidarCamposCanton(UnCanton, UnIdRegionGeografica, UnIdProvincia, CaracteresValidos, @NumError, 0);

      IF @NumError = 0 THEN
        UPDATE Cantones SET Canton = UnCanton, IdRegionGeografica = UnIdRegionGeografica, IdProvincia = UnIdProvincia WHERE IdCanton = @IdCanton;      
        CALL IndexarCanton(@IdCanton, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      ELSE
        SET @NumError = @NumBaseError + @NumError;
      END IF;
    END IF;    
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarNegocio(IN UnIdNegocio INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
  
  DELETE FROM PalabrasXNegocio WHERE IdNegocio = UnIdNegocio;
  CALL LiberarPalabrasNoUtilizadas();
  
  SELECT COUNT(1), LOWER(Nombre) FROM Negocios WHERE IdNegocio = UnIdNegocio INTO @CantidadNegocios, @PalabrasXIndexar;
  
  IF (@CantidadNegocios = 1) THEN
    SET @PalabrasIndexadas = '';
    
    CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
    
    WHILE CHAR_LENGTH(@SiguientePalabra) > 0 DO
      SET @SiguientePalabraXIndexarConSeparadoresPalabras = CONCAT(SeparadorPalabras, @SiguientePalabra, SeparadorPalabras);
      
      IF POSITION(@SiguientePalabraXIndexarConSeparadoresPalabras IN @PalabrasIndexadas) < 1 THEN
        CALL IndexarPalabra(@SiguientePalabra, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, 0);
        SELECT MIN(IdPalabra) FROM Palabras WHERE Palabra = @SiguientePalabra INTO @IdPalabra;
        INSERT INTO PalabrasXNegocio(IdNegocio, IdPalabra) VALUES(UnIdNegocio, @IdPalabra);
        SET @PalabrasIndexadas = CONCAT(@PalabrasIndexadas, @SiguientePalabraXIndexarConSeparadoresPalabras);
      END IF;
      
      CALL DemeSiguientePalabra(@PalabrasXIndexar, CaracteresValidos, @NuevoIndice, @SiguientePalabra, @NuevoIndice, 0);
    END WHILE;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdNegocio, a.Nombre, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Negocios a, PalabrasXNegocio b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdNegocio = b.IdNegocio
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante
    AND a.IdNegocio = UnIdNegocio;
  END IF;
  
  COMMIT;
END;

DELIMITER $$
 
CREATE PROCEDURE IndexarTodosNegocios(IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  CREATE TEMPORARY TABLE NegociosXIndexar
  SELECT IdNegocio
  FROM Negocios
  ORDER BY IdNegocio ASC;

  SELECT MIN(IdNegocio) FROM NegociosXIndexar INTO @IdNegocio;
  
  WHILE ISNULL(@IdNegocio) = 0 DO
    CALL IndexarNegocio(@IdNegocio, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    SELECT MIN(IdNegocio) FROM NegociosXIndexar WHERE IdNegocio > @IdNegocio INTO @IdNegocio;
  END WHILE;
  
  DROP TEMPORARY TABLE NegociosXIndexar;

  IF RetornarResultadosEnSelect = 1 THEN
    SELECT a.IdNegocio, a.Nombre, c.IdPalabra, c.Palabra, c.EstaLibre, d.IdPalabraSemejante, d.PalabraSemejante, d.EstaLibre, e.Caracter
    FROM Negocios a, PalabrasXNegocio b, Palabras c, PalabrasSemejantes d, CaracteresXPalabraSemejante e
    WHERE a.IdNegocio = b.IdNegocio
    AND b.IdPalabra = c.IdPalabra
    AND c.IdPalabraSemejante = d.IdPalabraSemejante
    AND d.IdPalabraSemejante = e.IdPalabraSemejante;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE ValidarCamposNegocio(IN UnNombre VARCHAR(100), IN UnaDireccion VARCHAR(150), IN UnosTelefonos VARCHAR(100), IN CaracteresValidos VARCHAR(500), OUT NumError INT, IN RetornarResultadosEnSelect BIT(1))
BEGIN
  SET NumError = 0;
  CALL DemeSiguientePalabra(UnNombre, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);
  
  IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN /* Si el nombre no tiene palabras válidas */
    SET NumError = 1;
  ELSE
    CALL DemeSiguientePalabra(UnaDireccion, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

    IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN /* Si la dirección no tiene palabras válidas */
      SET NumError = 2;

    ELSE
      CALL DemeSiguientePalabra(UnosTelefonos, CaracteresValidos, 1, @SiguientePalabra, @NuevoIndice, 0);

      IF CHAR_LENGTH(@SiguientePalabra) = 0 THEN /* Si los teléfonos no tienen palabras válidas */
        SET NumError = 3;
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT NumError;
  END IF;
END;

DELIMITER $$
 
CREATE PROCEDURE AltaNegocio(IN UnNombre VARCHAR(100), IN UnaDireccion VARCHAR(150), IN UnosTelefonos VARCHAR(100), IN UnIdCanton INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  CALL ValidarCamposNegocio(UnNombre, UnaDireccion, UnosTelefonos, CaracteresValidos, @NumError, 0);

  IF @NumError != 0 THEN
    SET @NumError = @NumBaseError + @NumError;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    SELECT MIN(IdCanton) FROM Cantones WHERE IdCanton = UnIdCanton INTO @IdCantonConsulta;

    IF ISNULL(@IdCantonConsulta) = 1 THEN /* Si no existe el cantón */
      SET @NumError = @NumBaseError + 1;

    ELSE
      SELECT MAX(IdNegocio) FROM Negocios INTO @IdNegocio;
  
      IF ISNULL(@IdNegocio) = 1 THEN
        SET @IdNegocio = 0;
      END IF;
    
      SET @IdNegocio = @IdNegocio + 1;

      INSERT INTO Negocios(IdNegocio, Nombre, Direccion, Telefonos, IdCanton) VALUES(@IdNegocio, UnNombre, UnaDireccion, UnosTelefonos, UnIdCanton);
      CALL IndexarNegocio(@IdNegocio, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdNegocio;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioNegocio(IN UnIdNegocio INT, IN UnNombre VARCHAR(100), IN UnaDireccion VARCHAR(150), IN UnosTelefonos VARCHAR(100), IN UnIdCanton INT, IN CaracteresValidos VARCHAR(500), IN TuplasReemplazos VARCHAR(500), IN SeparadorTuplas VARCHAR(1), IN SeparadorColumnas VARCHAR(1), IN SeparadorPalabras VARCHAR(1), IN RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SELECT MIN(IdNegocio) FROM Negocios WHERE IdNegocio = UnIdNegocio INTO @IdNegocio;
  
  IF ISNULL(@IdNegocio) = 1 THEN /* Si no existe el negocio */
    SET @NumError = @NumBaseError + 1;
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
    CALL ValidarCamposNegocio(UnNombre, UnaDireccion, UnosTelefonos, CaracteresValidos, @NumError, 0);

    IF @NumError != 0 THEN
      SET @NumError = @NumBaseError + @NumError;
    ELSE
      SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;
      SELECT MIN(IdCanton) FROM Cantones WHERE IdCanton = UnIdCanton INTO @IdCantonConsulta;

      IF ISNULL(@IdCantonConsulta) = 1 THEN /* Si no existe el cantón */
        SET @NumError = @NumBaseError + 1;
      ELSE
        UPDATE Negocios SET Nombre = UnNombre, Direccion = UnaDireccion, Telefonos = UnosTelefonos, IdCanton = UnIdCanton WHERE IdNegocio = UnIdNegocio;
        CALL IndexarNegocio(@IdNegocio, CaracteresValidos, TuplasReemplazos, SeparadorTuplas, SeparadorColumnas, SeparadorPalabras, 0);
      END IF;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @IdNegocio;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE AltaOfertaEmpleo(IN UnIdNegocio INT, IN BitEstaVigente BIT(1), IN UnIdProfesionOficio INT, IN UnaFechaVencimiento DATE, RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(IdNegocio) FROM Negocios WHERE IdNegocio = UnIdNegocio INTO @IdNegocioConsulta;
  
  IF ISNULL(@IdNegocioConsulta) = 1 THEN
    SET @NumError = @NumBaseError + 1; /* No existe el id del negocio */
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdProfesionOficio) FROM ProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio INTO @IdProfesionOficioConsulta;

    IF ISNULL(@IdProfesionOficioConsulta) = 1 THEN
      SET @NumError = @NumBaseError + 1; /* No existe el id de la profesión u oficio */
    ELSE
      SELECT MAX(Consecutivo) FROM OfertasEmpleo WHERE IdNegocio = UnIdNegocio INTO @Consecutivo;
  
      IF ISNULL(@Consecutivo) = 1 THEN
        SET @Consecutivo = 0;
      END IF;
    
      SET @Consecutivo = @Consecutivo + 1;

      INSERT INTO OfertasEmpleo(IdNegocio, Consecutivo, EstaVigente, IdProfesionOficio, FechaVencimiento) VALUES(UnIdNegocio, @Consecutivo, BitEstaVigente, UnIdProfesionOficio, UnaFechaVencimiento);
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError, @Consecutivo;
  END IF;
  
  COMMIT; 
END;

DELIMITER $$
 
CREATE PROCEDURE CambioOfertaEmpleo(IN UnIdNegocio INT, IN UnConsecutivo INT, IN BitEstaVigente BIT(1), IN UnIdProfesionOficio INT, IN UnaFechaVencimiento DATE, RetornarResultadosEnSelect BIT(1))
BEGIN
  START TRANSACTION;
   
  SET @IncrementosNumBaseError = 1000;
  SET @NumBaseError = @IncrementosNumBaseError;

  SET @NumError = 0;
  SELECT MIN(Consecutivo) FROM OfertasEmpleo WHERE IdNegocio = UnIdNegocio AND Consecutivo = UnConsecutivo INTO @ConsecutivoConsulta;
  
  IF ISNULL(@ConsecutivoConsulta) = 1 THEN
    SET @NumError = @NumBaseError + 1; /* No existe el registro buscado */
  ELSE
    SET @NumBaseError = @NumBaseError + @IncrementosNumBaseError;

    SELECT MIN(IdProfesionOficio) FROM ProfesionesOficios WHERE IdProfesionOficio = UnIdProfesionOficio INTO @IdProfesionOficioConsulta;

    IF ISNULL(@IdProfesionOficioConsulta) = 1 THEN
      SET @NumError = @NumBaseError + 1; /* No existe el id de la profesión u oficio */
    ELSE
      UPDATE OfertasEmpleo
      SET EstaVigente = BitEstaVigente, IdProfesionOficio = UnIdProfesionOficio, FechaVencimiento = UnaFechaVencimiento
      WHERE IdNegocio = UnIdNegocio AND Consecutivo = UnConsecutivo;
    END IF;
  END IF;
  
  IF RetornarResultadosEnSelect = 1 THEN
    SELECT @NumError;
  END IF;
  
  COMMIT; 
END;
