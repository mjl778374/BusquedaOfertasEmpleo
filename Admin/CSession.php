<?php
session_start();

class CSession
{
    public static function Inicializar()
    {
        $_SESSION["IdUsuario"] = NULL;
    } // public static function Inicializar()

    public static function FijarInicioSesionValido($ObjUsuario)
    {
        include_once "CUsuario.php";
        $_SESSION["IdUsuario"] = $ObjUsuario->DemeIdUsuario();
        $_SESSION["HoraSegundosUltimoAccesoSesion"] = time();
    } // public static function FijarInicioSesionValido($ObjUsuario)

    public static function UsuarioSesionIngresoOK()
    {
        include "constantesApp.php";
        
        if (isset($_SESSION["IdUsuario"]) && $_SESSION["IdUsuario"] != NULL && self::TiempoTranscurridoSegundosDesdeUltimoAccesoSesion() <= $MAX_TIEMPO_TRANSCURRIDO_SEGUNDOS_DESDE_ULTIMO_ACCESO_SESION)
            return true;
        else
            return false;
    } // public static function UsuarioSesionIngresoOK()

    private static function TiempoTranscurridoSegundosDesdeUltimoAccesoSesion()
    {
        $TiempoTranscurridoSegundos = time() - $_SESSION["HoraSegundosUltimoAccesoSesion"];
        $_SESSION["HoraSegundosUltimoAccesoSesion"] = time();
        return $TiempoTranscurridoSegundos;
    } // private static function TiempoTranscurridoSegundosDesdeUltimoAccesoSesion()

    private static function DemeIdUsuarioSesion()
    {
        return $_SESSION["IdUsuario"];
    } // private static function DemeIdUsuarioSesion()

    public static function DemeObjUsuarioSesion()
    {
        include_once "CUsuario.php";
        include_once "CUsuarios.php";

        $Existe = false;
        $IdUsuario = self::DemeIdUsuarioSesion();

        if (isset($IdUsuario) && $IdUsuario != NULL)
        {
            $Usuarios = new CUsuarios();
            $Usuarios->ConsultarXUsuario($IdUsuario, $Existe, $ObjUsuario);
        } // if (isset($IdUsuario) && $IdUsuario != NULL)
;
        if (!isset($IdUsuario) || $IdUsuario == NULL)
            throw new Exception("No ha iniciado sesión");
        else if (!$Existe)
            throw new Exception("No existe el usuario con el id " . $IdUsuario);
        else
            return $ObjUsuario;
    } // public static function DemeObjUsuario()
} // class CSession
?>
