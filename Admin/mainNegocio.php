<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$ParametrosGet = "";
$comodin = "?";

if (isset($_GET["Modo"]))
{
    $ParametrosGet = $ParametrosGet . $comodin . 'Modo=' . $_GET["Modo"];
    $comodin = "&";
} // if (isset($_GET["Modo"]))

if (isset($_GET["IdNegocio"]))
{
    $ParametrosGet = $ParametrosGet . $comodin . 'IdNegocio=' . $_GET["IdNegocio"];
    $comodin = "&";
} // if (isset($_GET["IdNegocio"]))
?>
<frameset rows="40%,*">
   <frame src="negocio.php<?php echo $ParametrosGet;?>">
   <frame src="ofertasEmpleo.php<?php echo $ParametrosGet;?>">
</frameset>
