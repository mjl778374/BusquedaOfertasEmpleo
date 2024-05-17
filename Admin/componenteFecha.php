<script>
  $(function() {
    $("#<?php echo $IdControl;?>").datepicker();
  } );

  $(function() {
      $("#<?php echo $IdControl;?>").datepicker("option", "dateFormat", "<?php echo $FormatoFecha;?>");
  });

  $(function() {
      $("#<?php echo $IdControl;?>").change(function() {
          $("#<?php echo $IdControlCopia;?>").val($(this).val());
      });
  });

  $(function() {
      $("#<?php echo $IdControl;?>").datepicker("setDate", "<?php echo $FechaInicial;?>");
      $("#<?php echo $IdControlCopia;?>").val($("#<?php echo $IdControl;?>").val());
  });
</script>
