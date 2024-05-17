<?php
class COfertaEmpleo
{
    private const URL_ARCHIVOS_OFERTAS = "localhost:8081/Ofertas";
    private const CARPETA_DISCO_ARCHIVOS_OFERTAS = "/var/www/html/BusquedaOfertasEmpleo/Busqueda/Ofertas";
    public const EXTENSION_ARCHIVOS_OFERTAS = "pdf";
    public const TIPO_ARCHIVOS_X_SUBIR_ACEPTADO = "application/pdf";

    private $IdNegocio = NULL;
    private $Consecutivo = NULL;
    private $EstaVigente = NULL;
    private $IdProfesionOficio = NULL;
    private $AliasProfesionOficio = NULL;
    private $FechaVencimiento = NULL;
    private $ObjNegocio = NULL;

    function __construct($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, $AliasProfesionOficio, $FechaVencimiento)
    {
        $this->IdNegocio = $IdNegocio;
        $this->Consecutivo = $Consecutivo;
        $this->EstaVigente = $EstaVigente;
        $this->IdProfesionOficio = $IdProfesionOficio;
        $this->AliasProfesionOficio = $AliasProfesionOficio;
        $this->FechaVencimiento = $FechaVencimiento;
    } // function __construct($IdNegocio, $Consecutivo, $EstaVigente, $IdProfesionOficio, $AliasProfesionOficio, $FechaVencimiento)

    public function FijarObjNegocio($ObjNegocio)
    {
        $this->ObjNegocio = $ObjNegocio;
    } // public function FijarObjNegocio($ObjNegocio)

    public function DemeObjNegocio()
    {
        return $this->ObjNegocio;
    } // public function DemeObjNegocio()

    private function DemeNombreArchivoOferta()
    {
        return $this->DemeIdNegocio() . "_" . $this->DemeConsecutivo() . "." . $this::EXTENSION_ARCHIVOS_OFERTAS;
    } // private function DemeNombreArchivoOferta()

    public function DemeUrlOferta()
    {
        return $this::URL_ARCHIVOS_OFERTAS . "/" . $this->DemeNombreArchivoOferta();
    } // public function DemeUrlOferta()

    public function SubirArchivoOferta($RutaOrigen, $NumErrorAlSubir, $TipoArchivoXSubir, &$NumError)
    {
        $NumError = 0;

        if ($NumErrorAlSubir != UPLOAD_ERR_OK)
            $NumError = 1002;
        elseif ($this::TIPO_ARCHIVOS_X_SUBIR_ACEPTADO != $TipoArchivoXSubir)
            $NumError = 1001;
        elseif (!move_uploaded_file($RutaOrigen, $this::CARPETA_DISCO_ARCHIVOS_OFERTAS . "/" . $this->DemeNombreArchivoOferta()))
            $NumError = 1002;
    } // public function SubirArchivoOferta($RutaOrigen, $NumErrorAlSubir, $TipoArchivoXSubir, &$NumError)

    public function DemeIdNegocio()
    {
        return $this->IdNegocio;
    } // public function DemeIdNegocio()

    public function DemeConsecutivo()
    {
        return $this->Consecutivo;
    } // public function DemeConsecutivo()

    public function DemeEstaVigente()
    {
        return $this->EstaVigente;
    } // public function DemeEstaVigente()

    public function DemeIdProfesionOficio()
    {
        return $this->IdProfesionOficio;
    } // public function DemeIdProfesionOficio()

    public function DemeAliasProfesionOficio()
    {
        return $this->AliasProfesionOficio;
    } // public function DemeAliasProfesionOficio()

    public function DemeFechaVencimiento()
    {
        return $this->FechaVencimiento;
    } // public function DemeFechaVencimiento()
} // class COfertaEmpleo
?>
