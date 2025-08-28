'use strict';

// Configuración global de AJAX para enviar siempre el token CSRF
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$(function () {
  const dt_table = $('#accounts-table');
  let dataTable;

  // Lógica para la alerta de éxito al cargar la página
  const successMessage = $('.card').data('success-message');
  if (successMessage) {
    Swal.fire({
      icon: 'success',
      title: '¡Hecho!',
      text: successMessage,
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    });
  }

  if (dt_table.length) {
    dataTable = dt_table.DataTable({
      processing: true,
      serverSide: true,
      ajax: '/admin/accounts',
      // ASEGÚRATE DE QUE TIENES ESTAS 7 COLUMNAS
      columns: [
        { data: 'id', name: 'id' },
        { data: 'courier_id', name: 'courier_id' },
        { data: 'email', name: 'email' },
        { data: 'city', name: 'city' },
        { data: 'status', name: 'status' },
        { data: 'assigned_to', name: 'assigned_to', orderable: false, searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ]
    });
  }

  // Lógica para el borrado con SweetAlert2 y AJAX
  $(document).on('click', '.delete-account-btn', function () {
    const deleteUrl = $(this).data('url');
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¡No podrás revertir esta acción!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, ¡eliminar!',
      cancelButtonText: 'Cancelar',
      customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false
    }).then(function (result) {
      if (result.isConfirmed) {
        $.ajax({
          url: deleteUrl,
          type: 'DELETE',
          success: function (response) {
            if (dataTable) dataTable.ajax.reload();
            Swal.fire({
              icon: 'success',
              title: '¡Eliminado!',
              text: response.message,
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
          },
          error: function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar la cuenta.' });
          }
        });
      }
    });
  });
});
