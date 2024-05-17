<?php
include_once "constantesApp.php";
include_once "CSession.php";

try
{
    $UsuarioSesionEsAdmin = false;
    $ObjUsuarioSesion = CSession::DemeObjUsuarioSesion();

    if ($ObjUsuarioSesion != NULL)
        $UsuarioSesionEsAdmin = $ObjUsuarioSesion->DemeEsAdministrador();
} // try
catch (Exception $e)
{}

$MostrarMenuBuscar = false;

if ($FormularioActivo == "Usuarios" || $FormularioActivo == "Usuario")
{
    $MenuUsuariosActivo = "active";

    if ($FormularioActivo == "Usuarios")
    {
        $MostrarMenuBuscar = true;
        $NombreMenuBuscar = "TextoXBuscar_Usuarios";
    } // if ($FormularioActivo == "Usuarios")
} // if ($FormularioActivo == "Usuarios" || $FormularioActivo == "Usuario")
elseif ($FormularioActivo == "ProfesionesOficios" || $FormularioActivo == "ProfesionOficio")
{
    $MenuProfesionesOficiosActivo = "active";

    if ($FormularioActivo == "ProfesionesOficios")
    {
        $MostrarMenuBuscar = true;
        $NombreMenuBuscar = "TextoXBuscar_ProfesionesOficios";
    } // if ($FormularioActivo == "ProfesionesOficios")
} // elseif ($FormularioActivo == "ProfesionesOficios" || $FormularioActivo == "ProfesionOficio")
else if($FormularioActivo == "Negocios" || $FormularioActivo == "Negocio")
{
    if ($FormularioActivo == "Negocios")
        $MostrarMenuBuscar = true;

    $MenuNegociosActivo = "active";
    $NombreMenuBuscar = "Negocios_TextoXBuscar";
} // else if($FormularioActivo == "Negocios" || $FormularioActivo == "Negocio")
else if($FormularioActivo == "RegionesGeograficas" || $FormularioActivo == "RegionGeografica" || $FormularioActivo == "Provincias" || $FormularioActivo == "Provincia" || $FormularioActivo == "Cantones" || $FormularioActivo == "Canton")
{
    $MenuOrganizacionTerritorialActivo = "active";

    if($FormularioActivo == "RegionesGeograficas" || $FormularioActivo == "RegionGeografica")
        $MenuRegionesGeograficasActivo = "active";

    else if($FormularioActivo == "Provincias" || $FormularioActivo == "Provincia")
        $MenuProvinciasActivo = "active";

    else if($FormularioActivo == "Cantones" || $FormularioActivo == "Canton")
        $MenuCantonesActivo = "active";

    if($FormularioActivo == "RegionesGeograficas" || $FormularioActivo == "Provincias" || $FormularioActivo == "Cantones")
    {
        $MostrarMenuBuscar = true;

        if ($FormularioActivo == "RegionesGeograficas")
            $NombreMenuBuscar = "TextoXBuscar_RegionesGeograficas";

        else if($FormularioActivo == "Provincias")
            $NombreMenuBuscar = "TextoXBuscar_Provincias";

        else if($FormularioActivo == "Cantones")
            $NombreMenuBuscar = "TextoXBuscar_Cantones";
    } // if($FormularioActivo == "RegionesGeograficas" || $FormularioActivo == "Provincias" || $FormularioActivo == "Cantones")
} // else if($FormularioActivo == "RegionesGeograficas" || $FormularioActivo == "RegionGeografica" || $FormularioActivo == "Provincias" ...
else if($FormularioActivo == "CambiarContrasena")
    $MenuCambiarContrasenaActivo = "active";
else if($FormularioActivo == "IndexarTodo")
    $MenuIndexarTodoActivo = "active";
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <a class="navbar-brand" href="main.php" target="_top">Menú</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php echo $MenuCambiarContrasenaActivo ?>">
        <a class="nav-link" href="cambiarContrasena.php" target="_top">Cambiar Contraseña</a>
      </li>
<?php if ($UsuarioSesionEsAdmin) { ?>
      <li class="nav-item <?php echo $MenuUsuariosActivo ?>">
        <a class="nav-link" href="usuarios.php" target="_top">Usuarios</a>
      </li>
<?php } // if ($UsuarioSesionEsAdmin) ?>
<li class="nav-item dropdown <?php echo $MenuOrganizacionTerritorialActivo ?>">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Organización Territorial
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item <?php echo $MenuRegionesGeograficasActivo ?>" href="regionesGeograficas.php" target="_top">Regiones Geográficas</a>
          <a class="dropdown-item <?php echo $MenuProvinciasActivo ?>" href="provincias.php" target="_top">Provincias</a>
          <a class="dropdown-item <?php echo $MenuCantonesActivo ?>" href="cantones.php" target="_top">Cantones</a>
        </div>
      </li>
      <li class="nav-item <?php echo $MenuProfesionesOficiosActivo ?>">
        <a class="nav-link" href="profesionesOficios.php" target="_top">Profesiones y Oficios</a>
      </li>
<li class="nav-item <?php echo $MenuNegociosActivo ?>">
        <a class="nav-link" href="negocios.php" target="_top">Negocios o Empresas</a>
      </li>
<?php if ($UsuarioSesionEsAdmin) { ?>
      <li class="nav-item <?php echo $MenuIndexarTodoActivo ?>">
        <a class="nav-link" href="indexarTodo.php" target="_top">Indexar Todo</a>
      </li>
<?php } // if ($UsuarioSesionEsAdmin) ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $URL_PAGINA_INGRESO; ?>" target="_top">Salir</a>
      </li>
    </ul>
<?php if ($MostrarMenuBuscar) { ?>
    <form class="form-inline my-2 my-lg-0" method="post" onsubmit="form_onsubmit(this, this.<?php echo $NombreMenuBuscar; ?>.value, '<?php echo $URLFormularioActivo; ?>');">
      <input class="form-control mr-sm-2" type="search" placeholder="Buscar" aria-label="Buscar" name="<?php echo $NombreMenuBuscar; ?>">
      <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Buscar</button>
    </form>
<?php } // if ($MostrarMenuBuscar) ?>
  </div>
</nav>
<?php if ($MostrarMenuBuscar) { ?>
<script>
function form_onsubmit(unForm, ValorCampoBusqueda, URLRedireccionar)
{
    var TextoXBuscar = ValorCampoBusqueda;
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, '?', ' '); // La función "ReemplazarTodo" se encuentra en el archivo "FuncionesUtiles.js" que se carga desde "encabezados.php"
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, '&', ' ');
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, '  ', ' ');
    TextoXBuscar = TextoXBuscar.trim();
    TextoXBuscar = ReemplazarTodo(TextoXBuscar, ' ', '+');
    unForm.action = URLRedireccionar + '?TextoXBuscar=' + TextoXBuscar;
} // function form_onsubmit(unForm, ValorCampoBusqueda, URLRedireccionar)
</script>
<?php } // if ($MostrarMenuBuscar) ?>
