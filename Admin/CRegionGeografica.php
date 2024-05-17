<?php
class CRegionGeografica
{
    private $IdRegion = NULL;
    private $Region = NULL;

    function __construct($IdRegion, $Region)
    {
        $this->IdRegion = $IdRegion;
        $this->Region = $Region;
    } // function __construct($IdRegion, $Region)

    public function DemeIdRegion()
    {
        return $this->IdRegion;
    } // public function DemeIdRegion()

    public function DemeRegion()
    {
        return $this->Region;
    } // public function DemeRegion()
} // class CRegionGeografica
?>
