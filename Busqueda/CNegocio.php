<?php
class CNegocio
{
    private $IdNegocio = NULL;
    private $Nombre = NULL;
    private $IdCanton = NULL;
    private $Canton = NULL;
    private $Direccion = NULL;
    private $Telefonos = NULL;
    private $Provincia = NULL;
    private $Region = NULL;

    function __construct($IdNegocio, $Nombre, $IdCanton, $Canton, $Direccion, $Telefonos, $Provincia, $Region)
    {
        $this->IdNegocio = $IdNegocio;
        $this->Nombre = $Nombre;
        $this->IdCanton = $IdCanton;
        $this->Canton = $Canton;
        $this->Direccion = $Direccion;
        $this->Telefonos = $Telefonos;
        $this->Provincia = $Provincia;
        $this->Region = $Region;
    } // function __construct($IdNegocio, $Nombre, $IdCanton, $Canton, $Direccion, $Telefonos, $Provincia, $Region)

    public function DemeIdNegocio()
    {
        return $this->IdNegocio;
    } // public function DemeIdNegocio()

    public function DemeNombre()
    {
        return $this->Nombre;
    } // public function DemeNombre()

    public function DemeIdCanton()
    {
        return $this->IdCanton;
    } // public function DemeIdCanton()

    public function DemeCanton()
    {
        return $this->Canton;
    } // public function DemeCanton()

    public function DemeDireccion()
    {
        return $this->Direccion;
    } // public function DemeDireccion()

    public function DemeTelefonos()
    {
        return $this->Telefonos;
    } // public function DemeTelefonos()

    public function DemeProvincia()
    {
        return $this->Provincia;
    } // public function DemeProvincia()

    public function DemeRegion()
    {
        return $this->Region;
    } // public function DemeRegion()
} // class CNegocio
?>
