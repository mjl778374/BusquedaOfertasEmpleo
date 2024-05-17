<?php
class CParametrosGet
{
    public static function DemeNumPagina($ParametroGet, $NumPaginas)
    {
        $NumPagina = 1;

        if (isset($_GET[$ParametroGet]) && is_numeric($_GET[$ParametroGet]) && ceil($_GET[$ParametroGet]) == floor($_GET[$ParametroGet]) && $_GET[$ParametroGet] > 0)
            $NumPagina = intval($_GET[$ParametroGet], 10);

        if ($NumPagina > $NumPaginas)
            $NumPagina = $NumPaginas;

        return $NumPagina;
    } // public static function DemeNumPagina($ParametroGet, $NumPaginas)

    public static function ValidarModo($ParametroGet, &$NumError)
    {
        include "constantesApp.php";
        $Retornar = "";
        $NumError = 0;

        if (!isset($_GET[$ParametroGet]))
            $NumError = 1;

        elseif (strcmp($_GET[$ParametroGet], $MODO_ALTA) != 0 && strcmp($_GET[$ParametroGet], $MODO_CAMBIO) != 0)
            $NumError = 2;

        else
            $Retornar = $_GET[$ParametroGet];

        return $Retornar;
    } // public static function ValidarModo($ParametroGet, $NumError)

    public static function ValidarIdEntero($ParametroGet, &$NumError)
    {
        $Retornar = 0;
        $NumError = 0;

        if (!isset($_GET[$ParametroGet]))
            $NumError = 1;

        elseif (!is_numeric($_GET[$ParametroGet]) || ceil($_GET[$ParametroGet]) != floor($_GET[$ParametroGet]) || $_GET[$ParametroGet] < 0)
            $NumError = 2;

        else
            $Retornar = intval($_GET[$ParametroGet], 10);

        return $Retornar;
    } // public static function ValidarIdEntero($ParametroGet, &$NumError)
} // class CParametrosGet
?>
