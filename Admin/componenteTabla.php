<div class="container mt-4">
<table class="table">
  <thead class="thead-dark">
    <tr>
    <?php
    for ($i = 0; $i < count($EncabezadoTabla); $i++)
        echo '<th scope="col">' . $EncabezadoTabla[$i] . '</th>';
?>
    </tr>
  </thead>
  <tbody>
    <?php
    for ($i = 0; $i < count($Filas); $i++)
    {
        echo "<tr>";
        $Fila = $Filas[$i];

        if (count($Fila) >= 2)
            echo '<th scope="row"><a href="' . htmlspecialchars($Fila[0]) . '">' . htmlspecialchars($Fila[1]) . '</a></th>';

        for ($j = 2; $j < count($Fila); $j++)
            echo "<td>" . htmlspecialchars($Fila[$j]) . "</td>";

        echo "</tr>";
    } // for ($i = 0; $i < count($Filas); $i++)
?>
  </tbody>
</table>
</div>
