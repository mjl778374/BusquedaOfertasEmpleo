<?php

try
{
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
    // La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
    // transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.

    $ObjUsuario = NULL;
    $UsuarioSesionEsAdmin = false;

    if ($UsuarioSesionIngresoOKApp)
        $ObjUsuario = CSession::DemeObjUsuarioSesion();

    if ($ObjUsuario != NULL)
        $UsuarioSesionEsAdmin = $ObjUsuario->DemeEsAdministrador();
} // try
catch (Exception $e)
{}

if (!$UsuarioSesionIngresoOKApp || $ObjUsuario == NULL || !$UsuarioSesionEsAdmin)
    header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$Usuario = "";
$Cedula = "";
$Nombre = "";
$BitEsAdministrador = 0;
$BitBorrarContrasena = 0;
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Usuario"]))
{
    $SePretendeGuardarInformacion = true;
    $Usuario = $_POST["Usuario"];
    $Cedula = $_POST["Cedula"];
    $Nombre = $_POST["Nombre"];

    if ($_POST["EsAdministrador"] == "on")
        $BitEsAdministrador = 1;

    if ($_POST["BorrarContrasena"] == "on")
        $BitBorrarContrasena = 1;
} // if (isset($_POST["Usuario"]))

try
{
    include_once "CParametrosGet.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdUsuario = CParametrosGet::ValidarIdEntero("IdUsuario", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdUsuario'.");
        elseif ($NumError == 2)
            throw new Exception("'IdUsuario' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdUsuario'.");

        if (strcmp($_GET["IdUsuario"], $IdUsuario) != 0)
            header("Location: " . "usuario.php?Modo=" . $Modo . "&IdUsuario=" . $IdUsuario);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)

// A continuación el código fuente de la implementación
try
{
    $ObjUsuario = NULL;
    include_once "CUsuarios.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $Usuarios = new CUsuarios();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $Usuarios->AltaUsuario($Usuario, $Cedula, $Nombre, $BitEsAdministrador, $NumError, $ObjUsuario);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $Usuarios->CambioUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $BitEsAdministrador, $BitBorrarContrasena, $NumError, $ObjUsuario);

        $BitBorrarContrasena = 0;

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Usuarios = new CUsuarios();
        $Usuarios->ConsultarXUsuario($IdUsuario, $Existe, $ObjUsuario);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

if ($ObjUsuario != NULL)
{
    $IdUsuario = $ObjUsuario->DemeIdUsuario();
    $Usuario = $ObjUsuario->DemeUsuario();
    $Cedula = $ObjUsuario->DemeCedula();
    $Nombre = $ObjUsuario->DemeNombre();
    $BitEsAdministrador = $ObjUsuario->DemeEsAdministrador();
} // if ($ObjUsuario != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
    header("Location: usuario.php?Modo=" . $MODO_CAMBIO . "&IdUsuario=" . $IdUsuario); // Se carga el usuario guardado.

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteUsuarioConIdEspecificado = "No existe el usuario con el id " . $IdUsuario . ".";
$ErrorUsuarioInvalido = "El usuario debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorCedulaInvalida = "La cédula debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorNombreInvalido = "El nombre debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteUsuarioConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe el usuario " . $Usuario . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorUsuarioInvalido);
            elseif ($NumError == 2002)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorCedulaInvalida);
            elseif ($NumError == 2003)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNombreInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaUsuario'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe el usuario " . $Usuario . " con otro id.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteUsuarioConIdEspecificado);
            elseif ($NumError == 3001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorUsuarioInvalido);
            elseif ($NumError == 3002)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorCedulaInvalida);
            elseif ($NumError == 3003)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNombreInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioUsuario'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó el usuario exitosamente.");

$Usuario = htmlspecialchars($Usuario);
$Cedula = htmlspecialchars($Cedula);
$Nombre = htmlspecialchars($Nombre);

$HabilitarBorradoContrasena = "";
$BorradoContrasenaSeleccionado = "";

if (strcmp($Modo, $MODO_ALTA) == 0)
{
    $HabilitarBorradoContrasena = "disabled";
    $BitBorrarContrasena = 1;
} // if (strcmp($Modo, $MODO_ALTA) == 0)

$EsAdministradorSeleccionado = "";

if ($BitEsAdministrador)
    $EsAdministradorSeleccionado = "checked";

if ($BitBorrarContrasena)
    $BorradoContrasenaSeleccionado = "checked";

$MaximoTamanoCampoUsuario = CUsuarios::MAXIMO_TAMANO_CAMPO_USUARIO;
$MaximoTamanoCampoCedula = CUsuarios::MAXIMO_TAMANO_CAMPO_CEDULA;
$MaximoTamanoCampoNombre = CUsuarios::MAXIMO_TAMANO_CAMPO_NOMBRE;

?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Usuario"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Usuario">Usuario</label>
                <input type="text" class="form-control" id="Usuario" name="Usuario" placeholder="Ingrese el usuario" value="<?php echo $Usuario; ?>" maxlength="<?php echo $MaximoTamanoCampoUsuario;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Cedula">Cédula</label>
                <input type="text" class="form-control" id="Cedula" name="Cedula" placeholder="Ingrese la cédula del usuario" value="<?php echo $Cedula; ?>" maxlength="<?php echo $MaximoTamanoCampoCedula;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Nombre">Nombre</label>
                <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Ingrese el nombre del usuario" value="<?php echo $Nombre; ?>" maxlength="<?php echo $MaximoTamanoCampoNombre;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="custom-control custom-checkbox col-8 col-md-6 col-lg-4">
                <input type="checkbox" class="custom-control-input" id="EsAdministrador" name="EsAdministrador" <?php echo $EsAdministradorSeleccionado; ?>>
                <label class="custom-control-label" for="EsAdministrador">Es Administrador</label>
           </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="custom-control custom-checkbox col-8 col-md-6 col-lg-4">
                <input type="checkbox" class="custom-control-input" id="BorrarContrasena" name="BorrarContrasena" <?php echo $HabilitarBorradoContrasena; ?> <?php echo $BorradoContrasenaSeleccionado; ?>>
                <label class="custom-control-label" for="BorrarContrasena">Borrar Contraseña</label>
           </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='usuarios.php';">Regresar</button>
            </div>
        </div>
    </div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</form>
</body>
</html>
