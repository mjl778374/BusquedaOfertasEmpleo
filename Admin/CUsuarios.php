<?php
include_once "CSQL.php";
include_once "CUsuario.php";

class CUsuarios extends CSQL
{
    public const MAXIMO_TAMANO_CAMPO_USUARIO = 50;
    public const MAXIMO_TAMANO_CAMPO_CEDULA = 100;
    public const MAXIMO_TAMANO_CAMPO_NOMBRE = 100;
    public const MAXIMO_TAMANO_CAMPO_CONTRASENA = 50;

    function __construct()
    {
        parent::__construct();
    } // function __construct()

    private static function DemeCodigoEncriptacion()
    {
        return "X&7a";
    } // private static function DemeCodigoEncriptacion()

    public function ValidarLogin($Usuario, $Contrasena, &$Existe, &$ObjUsuario)
    {
        include_once "CUsuario.php";
        $Consulta = "CALL ValidarLogin(?, ?, ?, @UsuarioContrasenaExiste, @UnIdUsuario, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'sss', array($Usuario, $Contrasena, self::DemeCodigoEncriptacion()));

        $ObjUsuario = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = $ResultadoConsulta[0];
                $IdUsuario = $ResultadoConsulta[1];

                if ($Existe)
                {
                    $Usuarios = new CUsuarios();
                    $Usuarios->ConsultarXUsuario($IdUsuario, $Existe, $ObjUsuario);
                } // if ($Existe)
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ValidarLogin($Usuario, $Contrasena, &$Existe, &$ObjUsuario)

    public function CambiarContrasena($IdUsuario, $ContrasenaActual, $NuevaContrasena, $ConfirmacionNuevaContrasena, &$NumError, &$LongitudMinimaContrasena, &$CaracteresEspeciales)
    {
        $LongitudMinimaContrasena = 5;
        $CaracteresEspeciales = "%!&;).?-/(¡,*#@$:+<";
        $Consulta = "CALL CambiarContrasena(?, ?, ?, ?, @NumError, ?, ?, ?, 1);";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'issssis', array($IdUsuario, $ContrasenaActual, $NuevaContrasena, $ConfirmacionNuevaContrasena, self::DemeCodigoEncriptacion(), $LongitudMinimaContrasena, $CaracteresEspeciales));

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambiarContrasena($IdUsuario, $ContrasenaActual, $NuevaContrasena, $ConfirmacionNuevaContrasena, &$NumError, &$LongitudMinimaContrasena, ...

    public function ConsultarXUsuario($IdUsuario, &$Existe, &$ObjUsuario)
    {
        include_once "CUsuario.php";

        $Consulta = "SELECT Usuario, Cedula, Nombre, EsAdministrador FROM Usuarios WHERE IdUsuario = ?";
        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'i', array($IdUsuario));
        $Existe = false;
        $ObjUsuario = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $Existe = true;
                $Usuario = $ResultadoConsulta[0];
                $Cedula = $ResultadoConsulta[1];
                $Nombre = $ResultadoConsulta[2];
                $EsAdministrador = $ResultadoConsulta[3];
                $ObjUsuario = new CUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function ConsultarXUsuario($IdUsuario, &$Existe, &$ObjUsuario)

    private function DemePalabrasMasParecidas($PalabrasBusqueda)
    {
        include_once "CPalabrasSemejantes.php";

        $PalabrasSemejantes = new CPalabrasSemejantes();
        $Retorno = $PalabrasSemejantes->DemePalabrasMasParecidas($PalabrasBusqueda, "PalabrasXUsuario");

        return $Retorno;
    } // private function DemePalabrasMasParecidas($PalabrasBusqueda)

    public function ConsultarXTodosUsuarios($PalabrasBusqueda)
    {
        $Retorno = [];
        $PalabrasMasParecidas = $this->DemePalabrasMasParecidas($PalabrasBusqueda);

        $Consulta = "SELECT COUNT(1) as NumAciertos, a.IdUsuario, a.Usuario, a.Cedula, a.Nombre, a.EsAdministrador";
        $Consulta = $Consulta . " FROM Usuarios a, PalabrasXUsuario b";
        $Consulta = $Consulta . " WHERE a.IdUsuario = b.IdUsuario";
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
        $Consulta = $Consulta . " GROUP BY a.IdUsuario, a.Usuario, a.Cedula, a.Nombre, a.EsAdministrador";
        $Consulta = $Consulta . " ORDER BY NumAciertos DESC, a.Usuario ASC";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, $TiposParametros, $ArregloParametros);

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            while ($ResultadoConsulta != NULL)
            {
                $IdUsuario = $ResultadoConsulta[1];
                $Usuario = $ResultadoConsulta[2];
                $Cedula = $ResultadoConsulta[3];
                $Nombre = $ResultadoConsulta[4];
                $EsAdministrador = $ResultadoConsulta[5];
                $ObjUsuario = new CUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador);
                $Retorno[] = $ObjUsuario;
                $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();
            } // while ($ResultadoConsulta != NULL)
        } // if ($ConsultaEjecutadaExitosamente)

        return $Retorno;
    } // public function ConsultarXTodosUsuarios($PalabrasBusqueda)

    public function AltaUsuario($Usuario, $Cedula, $Nombre, $EsAdministrador, &$NumError, &$ObjUsuario)
    {

        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";

        $Consulta = "CALL AltaUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'sssisssss', array($Usuario, $Cedula, $Nombre, $EsAdministrador, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjUsuario = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
            {
                $NumError = $ResultadoConsulta[0];
                $IdUsuario = $ResultadoConsulta[1];
                $ObjUsuario = new CUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador);
            } // if ($ResultadoConsulta != NULL)

            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function AltaUsuario($Usuario, $Cedula, $Nombre, $EsAdministrador, &$NumError, &$ObjUsuario)

    public function CambioUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador, $BorrarContrasena, &$NumError, &$ObjUsuario)
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL CambioUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1);";

        $ConsultaEjecutadaExitosamente = $this->EjecutarConsulta($Consulta, 'isssiisssss', array($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador, $BorrarContrasena, CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));

        $ObjUsuario = NULL;

        if ($ConsultaEjecutadaExitosamente)
        {
            $ResultadoConsulta = $this->DemeSiguienteResultadoConsulta();

            if ($ResultadoConsulta != NULL)
                $NumError = $ResultadoConsulta[0];

            $ObjUsuario = new CUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador);
            $this->LiberarConjuntoResultados();
        } // if ($ConsultaEjecutadaExitosamente)
    } // public function CambioUsuario($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador, $BorrarContrasena, &$NumError, &$ObjUsuario)

    public function IndexarTodo()
    {
        include_once "CPalabras.php";
        include_once "CPalabrasSemejantes.php";
        $Consulta = "CALL IndexarTodosUsuarios(?, ?, ?, ?, ?, 0);";
        $this->EjecutarConsulta($Consulta, 'sssss', array(CPalabras::DemeCaracteresValidos(), CPalabrasSemejantes::DemeTuplasReemplazo(), CPalabrasSemejantes::SEPARADOR_TUPLAS, CPalabrasSemejantes::SEPARADOR_COLUMNAS, CPalabras::SEPARADOR_PALABRAS));
    } // public function IndexarTodo()

    function __destruct()
    {
        parent::__destruct();
    } // function __destruct()
} // class CUsuarios extends CSQL
?>
