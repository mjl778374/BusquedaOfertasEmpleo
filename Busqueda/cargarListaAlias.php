<?php
$TextoXBuscar = $_GET['TextoXBuscar'];

include_once "CProfesionesOficios.php";
$ProfesionesOficios = new CProfesionesOficios();
$ListadoAlias = $ProfesionesOficios->ConsultarXTodosAliasProfesionesOficios($TextoXBuscar);
?>

<script>
window.parent.VaciarListaAlias();

<?php

include "FuncionesUtiles.php";

for($i = 0; $i < count($ListadoAlias); $i++)
{
    $ProfesionOficio = $ListadoAlias[$i];

    $IdProfesionOficio = $ProfesionOficio->DemeIdProfesionOficio();
    $IdAlias = $ProfesionOficio->DemeIdAlias();
    $Alias = $ProfesionOficio->DemeAlias();

    $Alias = htmlspecialchars(FormatearTextoJS($Alias));
?>
window.parent.AgregarAliasAListaAlias(<?php echo $IdProfesionOficio;?>, <?php echo $IdAlias;?>, "<?php echo $Alias; ?>");
<?php
} // for($i = 0; $i < count($ListadoAlias); $i++)
?>
</script>
