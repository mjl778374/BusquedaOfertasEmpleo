<?php
include_once "CSQL.php";

class CFechasHoras extends CSQL
{
    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function DemeFechaHoraActual()
    {
        $Consulta = "SELECT NOW()";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, '', []);

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $FechaHoraActual = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $FechaHoraActual;
    } // public function DemeFechaHoraActual()

    public function DemeFechaHoy($FormatoFecha)
    {
        $Consulta = "SELECT DATE_FORMAT(NOW(), ?)";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 's', array($FormatoFecha));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $FechaHoy = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $FechaHoy;
    } // public function DemeFechaHoy($FormatoFecha)

    public function AgregarHorasAFechaHora($FechaHora, $NumHoras)
    {
        $Consulta = "SELECT DATE_ADD(?, INTERVAL ? HOUR);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'si', array($FechaHora, $NumHoras));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $Resultado = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Resultado;
    } // public function AgregarHorasAFechaHora($FechaHora, $NumHoras)

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CFechasHoras extends CSQL
?>
