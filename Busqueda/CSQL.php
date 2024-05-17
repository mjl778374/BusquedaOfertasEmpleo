<?php
class CSQL
{
    private const HOST = 'localhost';
    private const USUARIO = 'root';
    private const CONTRASENA = 'Eldododerita22.';
    private const BD = 'BusquedaOfertasEmpleo';

    private $MySQLi = NULL;
    private $ResultadoConsulta = NULL;

    function __construct()
    {
        $this->MySQLi = new mysqli(self::HOST, self::USUARIO, self::CONTRASENA, self::BD);

        if ($this->MySQLi->connect_error)
            throw new Exception("Error de Conexión (" . $this->$MySQLi->connect_errno . ") " . $this->$MySQLi->connect_error);
    } // function __construct()

    private static function scm($Dato)
    {
        $Dato = str_replace("\\", "\\\\", $Dato);
        $Dato = str_replace("'", "''", $Dato);
        return "'" . $Dato . "'";
    } // private static function scm($Dato)

    protected function EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros, $MostrarConsulta = 0)
    {
        include "constantesApp.php"; // Se requiere incluir este archivo con el fin de verificar si el usuario quiere forzar que se vean las consultas SQL en pantalla
        $ConsultaOriginal = $Consulta;

        $Consulta = "";
        $Comodin = "?";

        $Error = "Error en el método 'CSQL.EjecutarConsulta'. ";
        $ErrorNumComodines = $Error . "El número de comodines (" . $Comodin . ") no coincide con el número de parámetros";

        if (strlen($TiposParametros) != count($ArregloParametros))
            throw new Exception($Error . "El número de tipos de parámetros no coincide con el tamaño del arreglo de parámetros");

        for($i = 0; $i < strlen($TiposParametros); $i++)
        {
            $Tipo = substr($TiposParametros, $i, 1);

            if ($Tipo == "i")
                $CaracteresReemplazo = $ArregloParametros[$i];

            elseif ($Tipo == "s")
                $CaracteresReemplazo = self::scm($ArregloParametros[$i]);

            else
                throw new Exception($Error . "El tipo de parámetro " . $Tipo . " no es válido");

            $PosicionReemplazo = strpos($ConsultaOriginal, $Comodin);

            if ($PosicionReemplazo !== FALSE)
            {
                if ($PosicionReemplazo >= 1)
                    $Consulta = $Consulta . substr($ConsultaOriginal, 0, $PosicionReemplazo);

                $Consulta = $Consulta . $CaracteresReemplazo;
                $ConsultaOriginal = substr($ConsultaOriginal, $PosicionReemplazo + 1);
            } // if ($PosicionReemplazo !== FALSE)

            else
                throw new Exception($ErrorNumComodines);                
        } // for($i = 0; $i < strlen($TiposParametros); $i++)

        $PosicionReemplazo = strpos($ConsultaOriginal, $Comodin);

        if ($PosicionReemplazo !== FALSE)
            throw new Exception($ErrorNumComodines);

        $Consulta = $Consulta . $ConsultaOriginal;
        $ConsultaOriginal = "";

        if ($MostrarConsulta || $MOSTRAR_CONSULTAS_SQL)
            echo "<br>" . $Consulta;

        $this->ResultadoConsulta = $this->MySQLi->query($Consulta);

        if (!$this->ResultadoConsulta)
            throw new Exception($this->MySQLi->error);

        return $this->ResultadoConsulta;
    } // protected function EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros, $MostrarConsulta = 0)

    protected function DemeSiguienteResultadoConsulta()
    {
        return $this->ResultadoConsulta->fetch_array(MYSQLI_BOTH);
    } // protected function DemeSiguienteResultadoConsulta()

    protected function LiberarConjuntoResultados()
    {
        $this->ResultadoConsulta->close();
    } // protected function LiberarConjuntoResultados()

    protected function IniciarTransaccion()
    {
        $this->MySQLi->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    } // protected function IniciarTransaccion()

    protected function AplicarTransaccion()
    {
        $this->MySQLi->commit();
    } // protected function AplicarTransaccion()

    function __destruct()
    {
        $this->MySQLi->close();
    } // function __destruct()
} // class CSQL
?>
