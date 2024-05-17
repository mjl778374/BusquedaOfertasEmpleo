<?php
include_once "CSQL.php";
include_once "COfertaEmpleo.php";

class COfertasEmpleo extends CSQL
{
    function __construct()
    {
        parent::__construct();
    } // function __construct()

    public function ConsultarXOfertaEmpleo($IdNegocio, $Consecutivo, &$Existe, &$ObjOfertaEmpleo)
    {
        include "constantesApp.php";

        $ObjOfertaEmpleo = NULL;
        $Consulta = "SELECT IdProfesionOficio, DATE_FORMAT(FechaVencimiento, ?), EstaVigente FROM OfertasEmpleo WHERE IdNegocio = ? AND Consecutivo = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'sii', array($FORMATO_FECHAS_SQL, $IdNegocio, $Consecutivo));
        $Existe = false;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $IdProfesionOficio = $ResultadoConsulta[0];
                $FechaVencimiento = $ResultadoConsulta[1];
                $BitEstaVigente = $ResultadoConsulta[2];
                $ObjOfertaEmpleo = new COfertaEmpleo($IdNegocio, $Consecutivo, $BitEstaVigente, $IdProfesionOficio, "", $FechaVencimiento);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXOfertaEmpleo($IdNegocio, $Consecutivo, &$Existe, &$ObjOfertaEmpleo)

    public function DemeTodasOfertasEmpleoDeNegocio($IdNegocio)
    {
        include "constantesApp.php";

        $Consulta = "SELECT a.Consecutivo, a.EstaVigente, a.IdProfesionOficio, DATE_FORMAT(a.FechaVencimiento, ?), b.Alias";
        $Consulta = $Consulta . " FROM OfertasEmpleo a, AliasProfesionesOficios b";
        $Consulta = $Consulta . " WHERE a.IdProfesionOficio = b.IdProfesionOficio";
        $Consulta = $Consulta . " AND b.IdAlias in (SELECT MIN(c.IdAlias) FROM AliasProfesionesOficios c WHERE c.IdProfesionOficio = b.IdProfesionOficio)";
        $Consulta = $Consulta . " AND a.IdNegocio = ?";
        $Consulta = $Consulta . " ORDER BY a.Consecutivo DESC";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'si', array($FORMATO_FECHAS_DESGLOSE, $IdNegocio));
        $Retorno = [];

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $Consecutivo = $ResultadoConsulta[0];
                $EstaVigente = $ResultadoConsulta[1];
                $IdProfesionOficio = $ResultadoConsulta[2];
                $FechaVencimiento = $ResultadoConsulta[3];
                $AliasProfesionOficio = $ResultadoConsulta[4];
                $ObjOfertaEmpleo = new COfertaEmpleo($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, $AliasProfesionOficio, $FechaVencimiento);
                $Retorno[] = $ObjOfertaEmpleo;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodasOfertasEmpleoDeNegocio($IdNegocio)

    public function DemeTodasOfertasEmpleoDeBusqueda($IdProfesionOficio, $IdRegion, $IdProvincia, $IdCanton)
    {
        include "constantesApp.php";
        include_once "CNegocio.php";

        $Consulta = "SELECT a.IdNegocio, a.Consecutivo, a.EstaVigente, DATE_FORMAT(a.FechaVencimiento, ?), b.Nombre, b.Direccion, b.Telefonos, c.Canton, d.Provincia, e.Region";
        $Consulta = $Consulta . " FROM OfertasEmpleo a, Negocios b, Cantones c, Provincias d, RegionesGeograficas e";
        $Consulta = $Consulta . " WHERE a.IdNegocio = b.IdNegocio";
        $Consulta = $Consulta . " AND b.IdCanton = c.IdCanton";
        $Consulta = $Consulta . " AND c.IdProvincia = d.IdProvincia";
        $Consulta = $Consulta . " AND c.IdRegionGeografica = e.IdRegion";
        $Consulta = $Consulta . " AND a.EstaVigente = 1";
        $Consulta = $Consulta . " AND a.FechaVencimiento >= DATE_FORMAT(NOW(), ?)";
        $Consulta = $Consulta . " AND a.IdProfesionOficio = ?";

        $TiposParametros = 'ssi';
        $ArregloParametros = array($FORMATO_FECHAS_DESGLOSE, $FORMATO_FECHAS_SQL, $IdProfesionOficio);

        if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
        {
            $Consulta = $Consulta . " AND (1 = 0";

            if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
            {
                $Consulta = $Consulta . " OR c.IdRegionGeografica = ?";
                $TiposParametros = $TiposParametros . 'i';
                $ArregloParametros[] = $IdRegion;
            } // if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

            if ($IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
            {
                $Consulta = $Consulta . " OR c.IdProvincia = ?";
                $TiposParametros = $TiposParametros . 'i';
                $ArregloParametros[] = $IdProvincia;
            } // if ($IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

            if ($IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
            {
                $Consulta = $Consulta . " OR c.IdCanton = ?";
                $TiposParametros = $TiposParametros . 'i';
                $ArregloParametros[] = $IdCanton;
            } // if ($IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

            $Consulta = $Consulta . ")";
        } // if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdCanton !=         } // if ($IdRegion != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdProvincia != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION || $IdCanton != $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)

        $Consulta = $Consulta . " ORDER BY a.FechaVencimiento ASC, b.Nombre ASC";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);
        $Retorno = [];

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdNegocio = $ResultadoConsulta[0];
                $Consecutivo = $ResultadoConsulta[1];
                $EstaVigente = $ResultadoConsulta[2];
                $FechaVencimiento = $ResultadoConsulta[3];
                $NombreNegocio = $ResultadoConsulta[4];
                $DireccionNegocio = $ResultadoConsulta[5];
                $TelefonosNegocio = $ResultadoConsulta[6];
                $Canton = $ResultadoConsulta[7];
                $Provincia = $ResultadoConsulta[8];
                $Region = $ResultadoConsulta[9];
                $ObjOfertaEmpleo = new COfertaEmpleo($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, "", $FechaVencimiento);
                $ObjNegocio = new CNegocio($IdNegocio, $NombreNegocio, 0, $Canton, $DireccionNegocio, $TelefonosNegocio, $Provincia, $Region);
                $ObjOfertaEmpleo->FijarObjNegocio($ObjNegocio);
                $Retorno[] = $ObjOfertaEmpleo;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function DemeTodasOfertasEmpleoDeBusqueda($IdProfesionOficio, $IdRegion, $IdProvincia, $IdCanton)

    public function AltaOfertaEmpleo($IdNegocio, $EstaVigente, $IdProfesionOficio, $FechaVencimiento, &$NumError, &$ObjOfertaEmpleo)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjOfertaEmpleo = NULL;
        $Consulta = "CALL AltaOfertaEmpleo(?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iiis', array($IdNegocio, $EstaVigente, $IdProfesionOficio, $FechaVencimiento));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $Consecutivo = $ResultadoConsulta[1];
                $ObjOfertaEmpleo = new COfertaEmpleo($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, "", $FechaVencimiento);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaOfertaEmpleo($IdNegocio, $EstaVigente, $IdProfesionOficio, $FechaVencimiento, &$NumError, &$ObjOfertaEmpleo)

    public function CambioOfertaEmpleo($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, $FechaVencimiento, &$NumError, &$ObjOfertaEmpleo)
    {
        include "constantesApp.php";
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $ObjOfertaEmpleo = NULL;
        $Consulta = "CALL CambioOfertaEmpleo(?, ?, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'iiiis', array($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, $FechaVencimiento));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $ObjOfertaEmpleo = new COfertaEmpleo($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, "", $FechaVencimiento);
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioOfertaEmpleo($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, $FechaVencimiento, &$NumError, &$ObjOfertaEmpleo)

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class COfertasEmpleo extends CSQL
?>
