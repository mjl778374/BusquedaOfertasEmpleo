<?php
if (strlen($ParametrosURL) > 0)
    $URL = $URL . $ParametrosURL . "&";
else
    $URL = $URL . "?";
?>

<form method="get">
<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
<?php
    $AnteriorPaginaActiva = "";

    if ($NumPaginaActual <= 1)
        $AnteriorPaginaActiva = "disabled";
?>
    <li class="page-item <?php echo $AnteriorPaginaActiva; ?>">
      <?php echo "<a class='page-link' href='" . $URL . "NumPagina=" . ($NumPaginaActual - 1) . "'>Anterior</a>"; ?>
    </li>
<?php
    for ($i = 1; $i <= $NumPaginas; $i++)
    {
        if ($NumPaginaActual == $i)
            echo "<li class='page-item active' aria-current='page'>";
        else
            echo "<li class='page-item'>";

        echo "<a class='page-link' href='" . $URL . "NumPagina=" . $i . "'>" . $i . "</a></li>";
    } // for ($i = 1; $i <= $NumPaginas; $i++)

    $SiguientePaginaActiva = "";

    if ($NumPaginaActual >= $NumPaginas)
        $SiguientePaginaActiva = "disabled";
?>
    <li class="page-item <?php echo $SiguientePaginaActiva; ?>">
      <?php echo "<a class='page-link' href='" . $URL . "NumPagina=" . ($NumPaginaActual + 1) . "'>Siguiente</a>"; ?>
    </li>
</nav>
</form>
