<?php
$SePretendeIniciarSesion = false;
$SeInicioSesionOK = false;

if (isset($_POST["UsuarioLogin"]))
{
    $SePretendeIniciarSesion = true;
    $Usuario = $_POST["UsuarioLogin"];
    $Contrasena = $_POST["ContrasenaLogin"];
} // if (isset($_POST["UsuarioLogin"]))

$NumError = 0;
$MensajeOtroError = "";

// A continuación el código fuente de la implementación
include_once "CSession.php";
CSession::Inicializar();

include_once "CUsuario.php";
include_once "CUsuarios.php";

if ($SePretendeIniciarSesion)
{
    try
    {
        $Usuarios = new CUsuarios();
        $Usuarios->ValidarLogin($Usuario, $Contrasena, $Existe, $ObjUsuario);

        if (!$Existe)
            $NumError = 2;
        else
        {
            CSession::FijarInicioSesionValido($ObjUsuario);
            $SeInicioSesionOK = true;
        } // else
    } // try
    catch (Exception $e)
    {
        $NumError = 1;
        $MensajeOtroError = $e->getMessage();
    } // catch (Exception $e)
} // if ($SePretendeIniciarSesion)
// El anterior fue el código fuente de la implementación

$MensajeXDesglosar = "";

if ($NumError != 0)
{
    include_once "CFormateadorMensajes.php";

    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);

    else if ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Usuario o contraseña incorrecta.");
} // if ($NumError != 0)
elseif ($SeInicioSesionOK)
    header("Location: " . "main.php"); // Se redirecciona a la página principal de la aplicación
?>
<?php
$MaximoTamanoCampoUsuario = CUsuarios::MAXIMO_TAMANO_CAMPO_USUARIO;
$MaximoTamanoCampoContrasena = CUsuarios::MAXIMO_TAMANO_CAMPO_CONTRASENA;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Usuario">Usuario</label>
                <input type="text" class="form-control" id="UsuarioLogin" name="UsuarioLogin" placeholder="Ingrese su usuario" maxlength="<?php echo $MaximoTamanoCampoUsuario;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Contrasena">Contraseña</label>
                <input type="password" class="form-control" id="ContrasenaLogin" name="ContrasenaLogin" placeholder="Ingrese su contraseña" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Enviar</button>
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
