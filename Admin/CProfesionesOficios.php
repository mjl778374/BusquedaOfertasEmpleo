<?php
include_once "CSQL.php";
include_once "CAliasProfesionOficio.php";

class CProfesionesOficios extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_ALIAS = 100;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function DemeTodasProfesionesOficios()
    {
	$Retorno = [];

        $Consulta = "SELECT a.IdProfesionOficio, b.Alias";
        $Consulta = $Consulta . " FROM ProfesionesOficios a, AliasProfesionesOficios b";
        $Consulta = $Consulta . " WHERE a.IdProfesionOficio = b.IdProfesionOficio";
        $Consulta = $Consulta . " AND b.IdAlias in (";
        $Consulta = $Consulta . "     SELECT MIN(c.IdAlias)";
        $Consulta = $Consulta . "     FROM AliasProfesionesOficios c";
        $Consulta = $Consulta . "     WHERE a.IdProfesionOficio = c.IdProfesionOficio";
        $Consulta = $Consulta . " )";
        $Consulta = $Consulta . " ORDER BY b.Alias ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, '', array());
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdProfesionOficio = $ResultadoConsulta[0];
                $Alias = $ResultadoConsulta[1];

                $Retorno[] = array($IdProfesionOficio, $Alias);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodasProfesionesOficios()

    public function DemeTodosAliasDeProfesionOficio($IdProfesionOficio, $IdAliasExcluir)
    {
	$Retorno = [];

        $Consulta = "SELECT a.IdProfesionOficio, b.IdAlias, b.Alias";
        $Consulta = $Consulta . " FROM ProfesionesOficios a, AliasProfesionesOficios b";
        $Consulta = $Consulta . " WHERE a.IdProfesionOficio = b.IdProfesionOficio";
        $Consulta = $Consulta . " AND a.IdProfesionOficio = ?";
        $Consulta = $Consulta . " AND b.IdAlias != ?";
        $Consulta = $Consulta . " ORDER BY b.Alias ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ii', array($IdProfesionOficio, $IdAliasExcluir));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdProfesionOficio = $ResultadoConsulta[0];
                $IdAlias = $ResultadoConsulta[1];
                $Alias = $ResultadoConsulta[2];
                $ObjAlias = new CAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias);

                $Retorno[] = $ObjAlias;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodosAliasDeProfesionOficio($IdProfesionOficio, $IdAliasExcluir)

    public function ConsultarXAliasProfesionOficio($IdProfesionOficio, $IdAlias, &$Existe, &$ObjAlias)
    {
        $ObjAlias = NULL;
        $Existe = false;

        $Consulta = "SELECT a.IdProfesionOficio, b.IdAlias, b.Alias";
        $Consulta = $Consulta . " FROM ProfesionesOficios a, AliasProfesionesOficios b";
        $Consulta = $Consulta . " WHERE a.IdProfesionOficio = b.IdProfesionOficio";
        $Consulta = $Consulta . " AND a.IdProfesionOficio = ?";
        $Consulta = $Consulta . " AND b.IdAlias = ?";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ii', array($IdProfesionOficio, $IdAlias));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $IdProfesionOficio = $ResultadoConsulta[0];
                $IdAlias = $ResultadoConsulta[1];
                $Alias = $ResultadoConsulta[2];
                $ObjAlias = new CAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXAliasProfesionOficio($IdProfesionOficio, $IdAlias, &$Existe, &$ObjAlias)

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXAliasProfesionOficio");

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosAliasProfesionesOficios($PalabrasBusqueda)
    {
        $ResultadosXRetornar = [];

        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "";
        $Consulta = $Consulta . "(";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        {
            $ArregloParametros[] = $PalabrasMasParecidas[$i];
            $TiposParametros = $TiposParametros . "i";

            if ($NumConsultasPalabras == 0)
                $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
        } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";

        $ConsultaPalabras = $Consulta;
        $Consulta = "";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdProfesionOficio, b.IdAlias, b.Alias";
        $Consulta = $Consulta . " FROM ProfesionesOficios a, AliasProfesionesOficios b, PalabrasXAliasProfesionOficio c";
        $Consulta = $Consulta . " WHERE a.IdProfesionOficio = b.IdProfesionOficio";
        $Consulta = $Consulta . " AND a.IdProfesionOficio = c.IdProfesionOficio";
        $Consulta = $Consulta . " AND b.IdAlias = c.IdAlias";
        $Consulta = $Consulta . " AND c.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdProfesionOficio, b.IdAlias, b.Alias";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdProfesionOficio = $ResultadoConsulta[1];
                $IdAlias = $ResultadoConsulta[2];
                $Alias = $ResultadoConsulta[3];

                $GroupBy->AgregarTupla(array($IdProfesionOficio, $IdAlias, $Alias), array(0,1), array(0,1,2), $NumAciertos);
                
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $MaximaCantidad = $GroupBy->DemeMaximaCantidad();
            $TamanoCampoCantidad = strlen($MaximaCantidad);
            
            $ResultadosOrdenados = $GroupBy->OrdenarTuplas(array(array(3,'i',$TamanoCampoCantidad,'desc',$MaximaCantidad), array(2, 's', self::MAXIMO_TAMANO_CAMPO_ALIAS)));

            $ResultadosXRetornar = [];
            foreach ($ResultadosOrdenados as $Clave => $CamposAlias)
                $ResultadosXRetornar[] = new CAliasProfesionOficio($CamposAlias[0], $CamposAlias[1], $CamposAlias[2]);
        } // if ($ConsultaEjecutadaExitosamente)

        return $ResultadosXRetornar;
    } // public function ConsultarXTodosAliasProfesionesOficios($PalabrasBusqueda)

    public function AltaProfesionOficio($Alias, &$NumError, &$ObjAlias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjAlias = NULL;

        $Consulta = "CALL AltaProfesionOficio(?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'ssssss', array($Alias, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdProfesionOficio = $ResultadoConsulta[1];
                $IdAlias = $ResultadoConsulta[2];
                $ObjAlias = new CAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaProfesionOficio($Alias, &$NumError, &$ObjAlias)

    public function AltaAliasProfesionOficio($IdProfesionOficio, $Alias, &$NumError, &$ObjAlias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjAlias = NULL;

        $Consulta = "CALL AltaAliasProfesionOficio(?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssss', array($IdProfesionOficio, $Alias, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdAlias = $ResultadoConsulta[1];
                $ObjAlias = new CAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaAliasProfesionOficio($IdProfesionOficio, $Alias, &$NumError, &$ObjAlias)

    public function CambioAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias, &$NumError, &$ObjAlias)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjAlias = NULL;

        $Consulta = "CALL CambioAliasProfesionOficio(?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iissssss', array($IdProfesionOficio, $IdAlias, $Alias, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];

                if ($NumError == 0)
                    $IdAlias = $ResultadoConsulta[1];

                $ObjAlias = new CAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias, &$NumError, &$ObjAlias)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosAliasProfesionesOficios(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CProfesionesOficios extends CSQL
?>
