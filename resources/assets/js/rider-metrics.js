'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dateRangePicker = document.getElementById('filter-date-range');
  const applyFiltersBtn = document.getElementById('apply-filters');
  const clearFiltersBtn = document.getElementById('clear-filters');
  const metricsTableBody = document.getElementById('metrics-table-body');
  const paginationLinks = document.getElementById('pagination-links');

  let currentPage = 1;

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

  function getFilters(page = 1) {
    const dates = (dateRangePicker.value || '').split(' a ');
    return {
      date_from: dates[0] || '',
      date_to: dates[1] || dates[0] || '',
      page: page
    };
  }

  async function fetchData(page = 1) {
    const filters = getFilters(page);
    const params = new URLSearchParams(Object.entries(filters).filter(([_, value]) => value !== ''));

    try {
      const response = await fetch(`/rider/metrics/list?${params}`);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || 'Error al cargar los datos.');
      }

      renderTable(data.data || []);
      renderPagination(data);
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message
      });
      renderTable([]);
    }
  }

  function renderTable(metrics) {
    metricsTableBody.innerHTML = '';
    if (!metrics || metrics.length === 0) {
      metricsTableBody.innerHTML = `<tr><td colspan="5" class="text-center">No se encontraron métricas.</td></tr>`;
      return;
    }

    metrics.forEach(metric => {
      const row = `
        <tr>
          <td>${metric.fecha}</td>
          <td>${metric.ciudad || 'N/A'}</td>
          <td>${metric.pedidos_entregados || 0}</td>
          <td>${(metric.horas || 0).toFixed(2)}h</td>
          <td>${(metric.tiempo_promedio || 0).toFixed(1)} min</td>
        </tr>
      `;
      metricsTableBody.insertAdjacentHTML('beforeend', row);
    });
  }

  function renderPagination(data) {
    paginationLinks.innerHTML = '';
    if (!data || !data.links || data.links.length <= 3) return;

    let linksHtml = '<ul class="pagination mb-0">';
    data.links.forEach(link => {
      const page = link.url ? new URL(link.url).searchParams.get('page') : null;
      const label = (link.label || '').replace('&laquo;', '‹').replace('&raquo;', '›');

      linksHtml += `
        <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${page || ''}">
            ${label}
          </a>
        </li>`;
    });
    linksHtml += '</ul>';
    paginationLinks.innerHTML = linksHtml;

    paginationLinks.addEventListener('click', e => {
      e.preventDefault();
      const pageLink = e.target.closest('a.page-link');
      if (pageLink && pageLink.dataset.page) {
        fetchData(pageLink.dataset.page);
      }
    });
  }

  applyFiltersBtn.addEventListener('click', () => fetchData(1));
  clearFiltersBtn.addEventListener('click', () => {
    dateRangePicker.value = '';
    fetchData(1);
  });

  fetchData(1);
});
