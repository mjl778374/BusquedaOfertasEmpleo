<?php
class CAliasProfesionOficio
{
    private $IdProfesionOficio = NULL;
    private $IdAlias = NULL;
    private $Alias = NULL;

    function __construct($IdProfesionOficio, $IdAlias, $Alias)
    {
        $this->IdProfesionOficio = $IdProfesionOficio;
        $this->IdAlias = $IdAlias;
        $this->Alias = $Alias;
    } // function __construct($IdProfesionOficio, $IdAlias, $Alias)

    public function DemeIdProfesionOficio()
    {
        return $this->IdProfesionOficio;
    } // public function DemeIdProfesionOficio()

    public function DemeIdAlias()
    {
        return $this->IdAlias;
    } // public function DemeIdAlias()

    public function DemeAlias()
    {
        return $this->Alias;
    } // public function DemeAlias()
} // class CAliasProfesionOficio
?>
