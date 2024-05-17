<?php
class CProvincia
{
    private $IdProvincia = NULL;
    private $Provincia = NULL;

    function __construct($IdProvincia, $Provincia)
    {
        $this->IdProvincia = $IdProvincia;
        $this->Provincia = $Provincia;
    } // function __construct($IdProvincia, $Provincia)

    public function DemeIdProvincia()
    {
        return $this->IdProvincia;
    } // public function DemeIdProvincia()

    public function DemeProvincia()
    {
        return $this->Provincia;
    } // public function DemeProvincia()
} // class CProvincia
?>
