<?php
include_once "CSQL.php";
include_once "CRegionGeografica.php";

class CRegionesGeograficas extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_REGION_GEOGRAFICA = 50;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXRegionGeografica($IdRegion, &$Existe, &$ObjRegion)
    {
        include_once "CRegionGeografica.php";

        $Consulta = "SELECT Region FROM RegionesGeograficas WHERE IdRegion = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdRegion));
        $Existe = false;
        $ObjRegion = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Region = $ResultadoConsulta[0];
                $ObjRegion = new CRegionGeografica($IdRegion, $Region);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXRegionGeografica($IdRegion, &$Existe, &$ObjRegion)

    public function DemeTodasRegionesGeograficas()
    {
        include_once "CRegionGeografica.php";

        $Consulta = "SELECT IdRegion, Region FROM RegionesGeograficas ORDER BY Region ASC";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, '', array());
        $Retorno = [];

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdRegion = $ResultadoConsulta[0];
                $Region = $ResultadoConsulta[1];
                $ObjRegion = array($IdRegion, $Region);
                $Retorno[] = $ObjRegion;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodasRegionesGeograficas()

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXRegionGeografica");

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodasRegionesGeograficas($PalabrasBusqueda)
    {
        $Retorno = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "SELECT COUNT(1) as NumAciertos, a.IdRegion, a.Region";
        $Consulta = $Consulta . " FROM RegionesGeograficas a, PalabrasXRegionGeografica b";
        $Consulta = $Consulta . " WHERE a.IdRegion = b.IdRegion";
        $Consulta = $Consulta . " AND b.IdPalabra IN (";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        {
            $ArregloParametros[] = $PalabrasMasParecidas[$i];
            $TiposParametros = $TiposParametros . "i";
            $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
        } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . " GROUP BY a.IdRegion, a.Region";
        $Consulta = $Consulta . " ORDER BY NumAciertos DESC, a.Region ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdRegion = $ResultadoConsulta[1];
                $Region = $ResultadoConsulta[2];
                $ObjRegion = new CRegionGeografica($IdRegion, $Region);
                $Retorno[] = $ObjRegion;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodasRegionesGeograficas($PalabrasBusqueda)

    public function AltaRegionGeografica($Region, &$NumError, &$ObjRegion)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL AltaRegionGeografica(?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ssssss', array($Region, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjRegion = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdRegion = $ResultadoConsulta[1];
                $ObjRegion = new CRegionGeografica($IdRegion, $Region);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaRegionGeografica($Region, &$NumError, &$ObjRegion)

    public function CambioRegionGeografica($IdRegion, $Region, &$NumError, &$ObjRegion)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL CambioRegionGeografica(?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssss', array($IdRegion, $Region, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjRegion = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $ObjRegion = new CRegionGeografica($IdRegion, $Region);
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioRegionGeografica($IdRegion, $Region, &$NumError, &$ObjRegion)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodasRegionesGeograficas(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CRegionesGeograficas extends CSQL
?>
