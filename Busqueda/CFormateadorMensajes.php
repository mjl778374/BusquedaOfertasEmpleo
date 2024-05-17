<?php
class CFormateadorMensajes
{
    public static function FormatearMensajeError($Mensaje)
    {
        return '<div class="row justify-content-center alert alert-danger" role="alert">' . htmlspecialchars($Mensaje) . '</div>';
    } // public static function FormatearMensajeError($Mensaje)

    public static function FormatearMensajeOK($Mensaje)
    {
        return '<div class="row justify-content-center alert alert-success" role="alert">' . htmlspecialchars($Mensaje) . '</div>';
    } // public static function FormatearMensajeOK($Mensaje)
} // class CFormateadorMensajes
?>

