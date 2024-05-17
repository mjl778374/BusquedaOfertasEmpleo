<ul class="navbar-nav mr-auto">
  <li class="nav-item">
    <select class="custom-select" id="<?php echo $IdListaSeleccion;?>" name="<?php echo $NombreListaSeleccion;?>">

<?php
    for ($i = 0; $i < count($ItemesListaSeleccion); $i++)
    {
        $Item = $ItemesListaSeleccion[$i];
        $Seleccionado = "";

        if ($i == 0 || $Item[0] == $IdItemSeleccionado)
            $Seleccionado = "selected";

        echo "<option value=" . $Item[0] . " " . $Seleccionado .">" . htmlspecialchars($Item[1]) . "</option>";
    } // for ($i = 0; $i < count($ItemesListaSeleccion); $i++)
?>
    </select>
  </li>
</ul>
