<?php
class CUsuario
{
    private $IdUsuario = NULL;
    private $Usuario = NULL;
    private $Cedula = NULL;
    private $Nombre = NULL;
    private $EsAdministrador = NULL;

    function __construct($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador)
    {
        $this->IdUsuario = $IdUsuario;
        $this->Usuario = $Usuario;
        $this->Cedula = $Cedula;
        $this->Nombre = $Nombre;
        $this->EsAdministrador = $EsAdministrador;
    } // function __construct($IdUsuario, $Usuario, $Cedula, $Nombre, $EsAdministrador)

    public function DemeIdUsuario()
    {
        return $this->IdUsuario;
    } // public function DemeIdUsuario()

    public function DemeUsuario()
    {
        return $this->Usuario;
    } // public function DemeUsuario()

    public function DemeCedula()
    {
        return $this->Cedula;
    } // public function DemeCedula()

    public function DemeNombre()
    {
        return $this->Nombre;
    } // public function DemeNombre()

    public function DemeEsAdministrador()
    {
        return $this->EsAdministrador;
    } // public function DemeEsAdministrador()
} // class CUsuario
?>
