<?php
class CCanton
{
    private $IdCanton = NULL;
    private $Canton = NULL;
    private $IdProvincia = NULL;
    private $Provincia = NULL;
    private $IdRegion = NULL;
    private $Region = NULL;

    function __construct($IdCanton, $Canton, $IdProvincia, $Provincia, $IdRegion, $Region)
    {
        $this->IdCanton = $IdCanton;
        $this->Canton = $Canton;
        $this->IdProvincia = $IdProvincia;
        $this->Provincia = $Provincia;
        $this->IdRegion = $IdRegion;
        $this->Region = $Region;
    } // function __construct($IdCanton, $Canton, $IdProvincia, $Provincia, $IdRegion, $Region)

    public function DemeIdCanton()
    {
        return $this->IdCanton;
    } // public function DemeIdCanton()

    public function DemeCanton()
    {
        return $this->Canton;
    } // public function DemeCanton()

    public function DemeIdRegion()
    {
        return $this->IdRegion;
    } // public function DemeIdRegion()

    public function DemeRegion()
    {
        return $this->Region;
    } // public function DemeRegion()

    public function DemeIdProvincia()
    {
        return $this->IdProvincia;
    } // public function DemeIdProvincia()

    public function DemeProvincia()
    {
        return $this->Provincia;
    } // public function DemeProvincia()
} // class CCanton
?>
