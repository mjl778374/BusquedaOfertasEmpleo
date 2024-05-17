<?php
include_once "CSQL.php";
include_once "CNegocio.php";

class CNegocios extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_NOMBRE = 100;
    public const MAXIMO_TAMANO_CAMPO_DIRECCION = 150;
    public const MAXIMO_TAMANO_CAMPO_TELEFONOS = 100;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXNegocio($IdNegocio, &$Existe, &$ObjNegocio)
    {
        $ObjNegocio = NULL;
        $Consulta = "SELECT Nombre, Direccion, Telefonos, IdCanton FROM Negocios WHERE IdNegocio = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdNegocio));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Nombre = $ResultadoConsulta[0];
                $Direccion = $ResultadoConsulta[1];
                $Telefonos = $ResultadoConsulta[2];
                $IdCanton = $ResultadoConsulta[3];
                $ObjNegocio = new CNegocio($IdNegocio, $Nombre, $IdCanton, "", $Direccion, $Telefonos, "", "");
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXNegocio($IdNegocio, &$Existe, &$ObjNegocio)

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXNegocio", array("PalabrasXCanton"));

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosNegocios($PalabrasBusqueda)
    {
        include_once "CCantones.php"; /* Se consultan campos de la clase CCantones */

        $ResultadosXRetornar = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "";
        $Consulta = $Consulta . "(";
        $Consulta = $Consulta . "     SELECT c.IdPalabra";
        $Consulta = $Consulta . "     FROM Palabras c";
        $Consulta = $Consulta . "     WHERE (1 = 0";

        $TiposParametros = "";
        $ArregloParametros = [];

        for($NumConsultasPalabras = 0; $NumConsultasPalabras < 2; $NumConsultasPalabras++)
        {
            for($i = 0; $i < count($PalabrasMasParecidas); $i++)
            {
                $ArregloParametros[] = $PalabrasMasParecidas[$i];
                $TiposParametros = $TiposParametros . "i";

                if ($NumConsultasPalabras == 0)
                    $Consulta = $Consulta . " OR c.IdPalabraSemejante = ?";
            } // for($i = 0; $i < count($PalabrasMasParecidas); $i++)
        } // for($NumConsultasPalabras = 0; $NumConsultasPalabras < 2; $NumConsultasPalabras++)

        $Consulta = $Consulta . ")";
        $Consulta = $Consulta . ")";

        $ConsultaPalabras = $Consulta;
        $Consulta = "";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdNegocio, a.Nombre, c.Canton, 'N' as Tipo";
        $Consulta = $Consulta . " FROM Negocios a, PalabrasXNegocio b, Cantones c";
        $Consulta = $Consulta . " WHERE a.IdNegocio = b.IdNegocio";
        $Consulta = $Consulta . " AND a.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdNegocio, a.Nombre, c.Canton, Tipo";

        $Consulta = $Consulta . " UNION ";

        $Consulta = $Consulta . "SELECT COUNT(1) as NumAciertos, a.IdNegocio, a.Nombre, c.Canton, 'C' as Tipo";
        $Consulta = $Consulta . " FROM Negocios a, PalabrasXCanton b, Cantones c";
        $Consulta = $Consulta . " WHERE b.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND a.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND b.IdPalabra IN " . $ConsultaPalabras;
        $Consulta = $Consulta . " GROUP BY a.IdNegocio, a.Nombre, c.Canton, Tipo";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            include_once "CGroupByCantidad.php";
            $GroupBy = new CGroupByCantidad();
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            $MaxIdNegocio = 0;

            while ($ResultadoConsulta != NULL)
            {
                $NumAciertos = $ResultadoConsulta[0];
                $IdNegocio = $ResultadoConsulta[1];
                $NombreNegocio = $ResultadoConsulta[2];
                $Canton = $ResultadoConsulta[3];

                if ($IdNegocio > $MaxIdNegocio)
                    $MaxIdNegocio = $IdNegocio;

                $GroupBy->AgregarTupla(array($IdNegocio, $NombreNegocio, $Canton), array(0), array(0,1,2), $NumAciertos);
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $TamanoCampoIdNegocio = strlen($MaxIdNegocio);
            $MaximaCantidad = $GroupBy->DemeMaximaCantidad();
            $TamanoCampoCantidad = strlen($MaximaCantidad);

            $ResultadosOrdenados = $GroupBy->OrdenarTuplas(array(array(3,'i',$TamanoCampoCantidad,'desc',$MaximaCantidad), array(1, 's', self::MAXIMO_TAMANO_CAMPO_NOMBRE), array(2, 's', CCantones::MAXIMO_TAMANO_CAMPO_CANTON), array(0, 'i', $TamanoCampoIdNegocio, 'asc')));

            include_once "CNegocio.php";
            $ResultadosXRetornar = [];
            foreach ($ResultadosOrdenados as $Clave => $CamposNegocio)
                $ResultadosXRetornar[] = new CNegocio($CamposNegocio[0], $CamposNegocio[1], 0, $CamposNegocio[2], "", "", "", "");
        } // if ($ConsultaEjecutadaExitosamente)

        return $ResultadosXRetornar;
    } // public function ConsultarXTodosNegocios($PalabrasBusqueda)

    public function AltaNegocio($Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError, &$ObjNegocio)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjNegocio = NULL;
        $Consulta = "CALL AltaNegocio(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'sssisssss', array($Nombre, $Direccion, $Telefonos, $IdCanton, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdNegocio = $ResultadoConsulta[1];
                $ObjNegocio = new CNegocio($IdNegocio, $Nombre, $IdCanton, "", $Direccion, $Telefonos, "", "");
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaNegocio($Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError, &$ObjNegocio)

    public function CambioNegocio($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError, &$ObjNegocio)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjNegocio = NULL;
        $Consulta = "CALL CambioNegocio(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'isssisssss', array($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $ObjNegocio = new CNegocio($IdNegocio, $Nombre, $IdCanton, "", $Direccion, $Telefonos, "", "");
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioNegocio($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, &$NumError, &$ObjNegocio)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosNegocios(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CNegocios extends CSQL
?>
