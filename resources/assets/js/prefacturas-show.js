'use strict';

document.addEventListener('DOMContentLoaded', function () {
  $('.select2').select2({
    placeholder: 'Seleccionar Rider'
  });

  const forms = document.querySelectorAll('.assign-form');
  forms.forEach(form => {
    form.addEventListener('submit', async function (event) {
      event.preventDefault();

      const itemId = this.dataset.itemId;
      const formData = new FormData(this);
      const riderId = formData.get('rider_id');
      const amount = formData.get('amount');
      const type = formData.get('type');

      if (!riderId) {
        Swal.fire({ icon: 'warning', title: 'Advertencia', text: 'Por favor, selecciona un rider.' });
        return;
      }
      if (!amount || amount <= 0) {
        Swal.fire({ icon: 'warning', title: 'Advertencia', text: 'El monto a asignar debe ser mayor que cero.' });
        return;
      }

      Swal.fire({
        title: 'Asignando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      try {
        const response = await fetch(`/admin/prefacturas/items/${itemId}/assign`, {
          method: 'POST',
          body: JSON.stringify({ rider_id: riderId, amount: amount, type: type }),
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        const data = await response.json();

        if (response.ok) {
          Swal.fire({
            icon: 'success',
            title: '¡Asignado!',
            text: 'El rider ha sido asignado correctamente.',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.reload(); // <--- Esta línea debería forzar la recarga
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error desconocido al asignar.' });
        }
      } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo conectar con el servidor.' });
      }
    });
  });
});
