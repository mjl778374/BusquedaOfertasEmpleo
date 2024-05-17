<?php
include_once "CSQL.php";

class CPalabrasSemejantes extends CSQL
{
    public const SEPARADOR_TUPLAS = ';';
    public const SEPARADOR_COLUMNAS = ',';

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    private static function AgregarTuplaReemplazo($Tuplas, $AReemplazar, $Reemplazo)
    {
        return $Tuplas . $AReemplazar . self::SEPARADOR_COLUMNAS . $Reemplazo . self::SEPARADOR_TUPLAS;
    } // private static function scm($Dato)

    public static function DemeTuplasReemplazo()
    {
        $Tuplas = "";
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'h', '');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'á', 'a');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'é', 'e');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'í', 'i');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ó', 'o');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ú', 'u');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ü', 'u');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ñ', 'n');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'w', 'v');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'v', 'b');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'x', 's');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'z', 's');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ge', 'je');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'gi', 'ji');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ce', 'se');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'ci', 'si');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'qu', 'k');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'q', 'k');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'c', 'k');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'll', 'y');
        $Tuplas = self::AgregarTuplaReemplazo($Tuplas, 'y', 'i');
        return $Tuplas;
    } // public static function DemeTuplasReemplazo()

    public function DemePalabraSemejante($Palabra)
    {
        $PalabraSemejante = "";
        $Consulta = "CALL DemePalabraSemejante(?, ?, ?, ?, @PalabraSemejante, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ssss', array($Palabra, self::DemeTuplasReemplazo(), self::SEPARADOR_TUPLAS, self::SEPARADOR_COLUMNAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $PalabraSemejante = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $PalabraSemejante;
    } // public function DemePalabraSemejante($Palabra)

    public function DemePalabrasMasParecidas($PalabrasBusqueda, $TablaDondeDebenEstar, $OtrasTablasDondePuedenEstar = [])
    {
        include_once "CPalabras.php";

        $Retorno = [];

        $NuevoIndice = 0;
        $Palabras = new CPalabras();
        $SiguientePalabra = $Palabras->DemeSiguientePalabra($PalabrasBusqueda, 1, $NuevoIndice);

        while (strlen($SiguientePalabra) > 0)
        {
            $PalabrasSemejantes = new CPalabrasSemejantes();
            $PalabraSemejante = $PalabrasSemejantes->DemePalabraSemejante($SiguientePalabra);
            $Palabras = new CPalabras();
            $SiguientePalabra = $Palabras->DemeSiguientePalabra($PalabrasBusqueda, $NuevoIndice, $NuevoIndice);

            $Consulta = "SELECT COUNT(1) AS NumAciertos, a.IdPalabraSemejante";
            $Consulta = $Consulta . " FROM CaracteresXPalabraSemejante a";
            $Consulta = $Consulta . " WHERE 1 = 1";
            
            $TiposParametros = "";
            $ArregloParametros = [];

            if (strlen($PalabraSemejante) == 0)
            {
                $TiposParametros = $TiposParametros . "s";
                $ArregloParametros[] = "";
                $Consulta = $Consulta . " AND a.Caracter = ?";
            } // if (strlen($PalabraSemejante) == 0)
            else
            {
                $Consulta = $Consulta . " AND (1 = 0";

                for($i = 0; $i < strlen($PalabraSemejante); $i++)
                {
                    $TiposParametros = $TiposParametros . "s";
                    $ArregloParametros[] = substr($PalabraSemejante, $i, 1);
                    $Consulta = $Consulta . " OR a.Caracter = ?";
                } // for($i = 0; $i < strlen($PalabraSemejante); $i++)

                $Consulta = $Consulta . ")";
            } // else

            $Consulta = $Consulta . " AND (";
            $Consulta = $Consulta . " a.IdPalabraSemejante IN";
            $Consulta = $Consulta . " (SELECT b.IdPalabraSemejante FROM Palabras b WHERE b.IdPalabra IN (SELECT c.IdPalabra FROM " . $TablaDondeDebenEstar . " c))";

            for($i = 0; $i < count($OtrasTablasDondePuedenEstar); $i++)
            {
                $Consulta = $Consulta . " OR a.IdPalabraSemejante IN";
                $Consulta = $Consulta . " (SELECT b.IdPalabraSemejante FROM Palabras b WHERE b.IdPalabra IN (SELECT c.IdPalabra FROM " . $OtrasTablasDondePuedenEstar[$i] . " c))";
            } // for($i = 0; $i < count($OtrasTablasDondePuedenEstar); $i++)

            $Consulta = $Consulta . ")";
            $Consulta = $Consulta . " GROUP BY a.IdPalabraSemejante";
            $Consulta = $Consulta . " ORDER BY NumAciertos DESC";
            
            $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

            if ($ConsultaEjecutadaExitosamente)
            {
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
                $MaxNumAciertos = -1;
                $NumAciertos = -1;

                while ($ResultadoConsulta != NULL && $MaxNumAciertos == $NumAciertos)
                {
                    if ($MaxNumAciertos == -1)
                        $MaxNumAciertos = $ResultadoConsulta[0];

                    $NumAciertos = $ResultadoConsulta[0];

                    if ($MaxNumAciertos == $NumAciertos)
                    {
                        $IdPalabra = $ResultadoConsulta[1];
                        $Retorno[] = $IdPalabra;
                    } // if ($MaxNumAciertos == $NumAciertos)

                    $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
                } // while ($ResultadoConsulta != NULL && $MaxNumAciertos == $NumAciertos)

                $this->LiberarConjuntoResultados();
            } // if ($ConsultaEjecutadaExitosamente)
        } // while (strlen($SiguientePalabra) > 0)

        return $Retorno;
    } // public function DemePalabrasMasParecidas($PalabrasBusqueda, $TablaDondeDebenEstar, $OtrasTablasDondePuedenEstar = [])

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CPalabrasSemejantes
?>
