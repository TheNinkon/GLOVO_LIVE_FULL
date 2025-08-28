'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dateRangePicker = document.getElementById('period');
  const prefacturaForm = document.getElementById('prefactura-form');

  if (dateRangePicker) {
    flatpickr(dateRangePicker, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      locale: {
        rangeSeparator: ' a ',
        weekdays: {
          shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
          longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
        },
        months: {
          shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
          longhand: [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
          ]
        },
        ordinal: function () {
          return '';
        },
        firstDayOfWeek: 1
      }
    });
  }

  if (prefacturaForm) {
    prefacturaForm.addEventListener('submit', async function (event) {
      event.preventDefault();

      const formData = new FormData(this);
      const periodDates = formData.get('period').split(' a ');

      if (periodDates.length !== 2) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Por favor, selecciona un rango de fechas válido.' });
        return;
      }
      formData.append('start_at', periodDates[0]);
      formData.append('end_at', periodDates[1]);
      formData.delete('period');

      Swal.fire({
        title: 'Cargando...',
        text: 'Procesando el archivo, esto puede tardar.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      try {
        const response = await fetch('/admin/prefacturas', {
          method: 'POST',
          body: formData,
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await response.json();

        if (response.ok) {
          Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: data.message,
            timer: 3000
          }).then(() => {
            window.location.href = `/admin/prefacturas/${data.prefactura_id}`;
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error desconocido al cargar.' });
        }
      } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error de Red', text: 'No se pudo conectar con el servidor.' });
      }
    });
  }
});
