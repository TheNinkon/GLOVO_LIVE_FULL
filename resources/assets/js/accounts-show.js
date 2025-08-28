'use strict';

$(function () {
  const historyTable = $('#assignments-history-table');

  // Comprobar si la tabla existe en la página
  if (historyTable.length) {
    historyTable.DataTable({
      // Se eliminan la mayoría de las opciones ya que la tabla se renderiza con Blade y no con AJAX.
      order: [[1, 'desc']],
      dom:
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
        '<"table-responsive"t>' +
        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }
});
