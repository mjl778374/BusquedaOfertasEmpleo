<?php
try
{
    include_once "CFormateadorMensajes.php"; // Esto es para desglosar mensajes de error si ocurren (como los relativos a la base de datos)
    $MensajeXDesglosar = "";
 
    include_once "constantesApp.php";
    include_once "CParametrosGet.php";

    $IdRegionGeografica = CParametrosGet::ValidarIdEntero("IdRegion", $NumError);
    if ($NumError != 0)
        $IdRegionGeografica = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $IdProvincia = CParametrosGet::ValidarIdEntero("IdProvincia", $NumError);
    if ($NumError != 0)
        $IdProvincia = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $IdCanton = CParametrosGet::ValidarIdEntero("IdCanton", $NumError);
    if ($NumError != 0)
        $IdCanton = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;

    $ListadoProfesionesOficios = [];
    $AliasProfesionOficio = "";
    $IdProfesionOficio = CParametrosGet::ValidarIdEntero("IdProfesionOficio", $NumError);

    if ($NumError == 0)
        $IdAlias = CParametrosGet::ValidarIdEntero("IdAlias", $NumError);
    else
        $IdAlias = 0;

    // A continuación el código fuente de la implementación
    include_once "CProfesionesOficios.php";
    $ProfesionesOficios = new CProfesionesOficios();
    $ProfesionesOficios->ConsultarXAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Existe, $ObjAlias);

    if ($Existe)
        $AliasProfesionOficio = $ObjAlias->DemeAlias();

    include_once "COfertasEmpleo.php";
    $OfertasEmpleo = new COfertasEmpleo();
    $ListadoOfertasEmpleo = $OfertasEmpleo->DemeTodasOfertasEmpleoDeBusqueda($IdProfesionOficio, $IdRegionGeografica, $IdProvincia, $IdCanton);

    include_once "CRegionesGeograficas.php";
    $RegionesGeograficas = new CRegionesGeograficas();
    $ListadoRegionesGeograficas = $RegionesGeograficas->DemeTodasRegionesGeograficas();

    include_once "CProvincias.php";
    $Provincias = new CProvincias();
    $ListadoProvincias = $Provincias->DemeTodasProvincias();

    include_once "CCantones.php";
    $Cantones = new CCantones();
    $ListadoCantones = $Cantones->DemeTodosCantones();
    // El anterior fue el código fuente de la implementación

    $IdListaRegiones = "IdRegion";
    $NombreListaRegiones = "IdRegion";

    $IdListaProvincias = "IdProvincia";
    $NombreListaProvincias = "IdProvincia";

    $IdListaCantones = "IdCanton";
    $NombreListaCantones = "IdCanton";
} // try
catch (Exception $e)
{
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($e->getMessage());
} // catch (Exception $e)
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <a class="navbar-brand" href="">Menú</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoRegionesGeograficas;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Región Geográfica");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdRegionGeografica;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion=$IdListaRegiones; $NombreListaSeleccion=$NombreListaRegiones; include "componenteListaSeleccion.php" ?>

      </li>
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoProvincias;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Provincia");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdProvincia;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion=$IdListaProvincias; $NombreListaSeleccion=$NombreListaProvincias; include "componenteListaSeleccion.php" ?>
      </li>
      <li class="nav-item">
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoCantones;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Cantón");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdCanton;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion=$IdListaCantones; $NombreListaSeleccion=$NombreListaCantones; include "componenteListaSeleccion.php" ?>
      </li>
    </ul>
<form class="form-inline my-2 my-lg-0" method="post">
      <input class="form-control col-12" type="search" placeholder="Profesión u Oficio" aria-label="Buscar" name="AliasXBuscar" onkeyup="CargarListaAlias(this.value);">
</form>
  </div>
</nav>
<div id="ListaAlias" class="list-group">
</div>
<?php
if ($AliasProfesionOficio != "")
    echo "<h3>" . htmlspecialchars($AliasProfesionOficio) . "</h3>";
?>
<div class="container mt-12">
<div class="row">
  <div class="col-4">
    <div class="list-group" id="list-tab" role="tablist">
<?php
    include "FuncionesUtiles.php";

    $EventoOnLoad = "";

    for($i = 0; $i < count($ListadoOfertasEmpleo); $i++)
    {
        $ObjOfertaEmpleo = $ListadoOfertasEmpleo[$i];
        $ObjNegocio = $ObjOfertaEmpleo->DemeObjNegocio();
        $Nombre = $ObjNegocio->DemeNombre();
        $TituloNegocio = htmlspecialchars($Nombre);
        $Nombre = "Nombre del Negocio o Empresa: " . htmlspecialchars(FormatearTextoJS($Nombre));
        $Direccion = "Dirección: " . htmlspecialchars(FormatearTextoJS($ObjNegocio->DemeDireccion()));
        $Telefonos = "Teléfonos: " . htmlspecialchars(FormatearTextoJS($ObjNegocio->DemeTelefonos()));
        $Canton = "Cantón: " . htmlspecialchars(FormatearTextoJS($ObjNegocio->DemeCanton()));
        $Provincia = "Provincia: " . htmlspecialchars(FormatearTextoJS($ObjNegocio->DemeProvincia()));
        $Region = "Región Geográfica: " . htmlspecialchars(FormatearTextoJS($ObjNegocio->DemeRegion()));
        $FechaVencimiento = "Fecha de Vencimiento: " . htmlspecialchars(FormatearTextoJS($ObjOfertaEmpleo->DemeFechaVencimiento()));
        $LinkOferta = FormatearTextoJS("<a href='" . $ObjOfertaEmpleo->DemeUrlOferta() . "' target='_blank'>Abrir</a");
        $EventoOnClick = "CargarDatosOfertaEmpleo('list-home', '" . $Nombre . "', '" . $Direccion . "', '" . $Telefonos . "', '" . $Canton . "', '" . $Provincia . "', '" .$Region . "', '" .$FechaVencimiento . "', '" .$LinkOferta . "');";

        $ItemActivo = "";
        if ($i == 0)
        {
            $ItemActivo = "active";
            $EventoOnLoad = $EventoOnClick;
        } // if ($i == 0)
?>
      <a class="list-group-item list-group-item-action <?php echo $ItemActivo; ?>" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home" onclick="<?php echo $EventoOnClick;?>"><?php echo $TituloNegocio;?></a>
<?php
    } // for($i = 0; $i < count($ListadoOfertasEmpleo); $i++)
?>
    </div>
  </div>
  <div class="col-8">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list"></div>
    </div>
  </div>
</div>
</div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
<script>
<?php
if ($EventoOnLoad != "") // Requiere que el evento termine con ";"
    echo $EventoOnLoad;
?>

function CargarDatosOfertaEmpleo(IdLista, NombreNegocio, DireccionNegocio, TelefonosNegocio, CantonNegocio, ProvinciaNegocio, RegionNegocio, FechaVencimiento, LinkOferta)
{
    var Lista = document.getElementById(IdLista);
    Lista.innerHTML = EnvolverDentroParrafo(NombreNegocio) + EnvolverDentroParrafo(DireccionNegocio) + EnvolverDentroParrafo(TelefonosNegocio) + EnvolverDentroParrafo(CantonNegocio) + EnvolverDentroParrafo(ProvinciaNegocio) + EnvolverDentroParrafo(RegionNegocio) + EnvolverDentroParrafo(FechaVencimiento) + EnvolverDentroParrafo(LinkOferta);
} // function CargarDatosOfertaEmpleo(IdLista, NombreNegocio, DireccionNegocio, TelefonosNegocio, CantonNegocio, ProvinciaNegocio, RegionNegocio, FechaVencimiento, LinkOferta)

function EnvolverDentroParrafo(Texto)
{
    return "<br>" + Texto + "</br>";
} // function EnvolverDentroParrafo(Texto)

function OpcionSeleccionada(IdListaSeleccion)
{
    var ListaSeleccion = document.getElementById(IdListaSeleccion);
    return ListaSeleccion.options[ListaSeleccion.selectedIndex].value;
} // function OpcionSeleccionada(IdListaSeleccion)

function SeleccionarAlias(iIdProfesionOficio, iIdAlias)
{
    var m_sHref = "<?php echo $URL_PAGINA_INGRESO; ?>?IdProfesionOficio=" + iIdProfesionOficio + "&IdAlias=" + iIdAlias;
    var m_sIdRegion = OpcionSeleccionada("<?php echo $IdListaRegiones;?>");
    var m_sIdProvincia = OpcionSeleccionada("<?php echo $IdListaProvincias;?>");
    var m_sIdCanton = OpcionSeleccionada("<?php echo $IdListaCantones;?>");

    if (m_sIdRegion != "<?php echo $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;?>")
        m_sHref = m_sHref + "&IdRegion=" + m_sIdRegion;

    if (m_sIdProvincia != "<?php echo $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;?>")
        m_sHref = m_sHref + "&IdProvincia=" + m_sIdProvincia;

    if (m_sIdCanton != "<?php echo $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;?>")
        m_sHref = m_sHref + "&IdCanton=" + m_sIdCanton;

    window.location.href = m_sHref;
} // function SeleccionarAlias(iIdProfesionOficio, iIdAlias)

function VaciarListaAlias()
{
    document.getElementById("ListaAlias").innerHTML = "";
} // function VaciarListaAlias()

function AgregarAliasAListaAlias(iIdProfesionOficio, iIdAlias, sAlias)
{
    var m_sHTML = document.getElementById("ListaAlias").innerHTML;
    m_sHTML = m_sHTML + '<button type="button" class="list-group-item list-group-item-action" onclick="SeleccionarAlias(' + iIdProfesionOficio + ", " + iIdAlias + ');">' + sAlias + '</button>';
    document.getElementById("ListaAlias").innerHTML = m_sHTML;
} // function AgregarAliasAListaAlias(iIdProfesionOficio, iIdAlias, sAlias)

function CargarListaAlias(sTextoXBuscar)
{
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "\\", "+"); // La función "ReemplazarTodo" se encuentra en el archivo "FuncionesUtiles.js" que se carga desde "encabezados.php"
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "?", "+");
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, "&", "+");
    sTextoXBuscar = ReemplazarTodo(sTextoXBuscar, " ", "+");

    var m_sHref = 'cargarListaAlias.php?TextoXBuscar=' + sTextoXBuscar;
    window.fraProcesar.location.href = m_sHref;
} // function CargarListaAlias(sTextoXBuscar)
</script>
<?php
$AnchoFrame = 0;
$AltoFrame = 0;

if ($MOSTRAR_CONSULTAS_SQL)
{
    $AnchoFrame = "100%";
    $AltoFrame = 400;
} // if ($MOSTRAR_CONSULTAS_SQL)
?>
<iframe name="fraProcesar" width="<?php echo $AnchoFrame;?>" height="<?php echo $AltoFrame;?>"></iframe>
</html>
