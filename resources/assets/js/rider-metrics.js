import $ from 'jquery';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/l10n/es.js';

$(document).ready(function () {
  const metricsApiUrl = `/rider/api/metrics/list`;
  const kpisApiUrl = `/rider/api/metrics/kpis`;

  const kpisContainer = $('#kpis-container');
  const tableBody = $('#metrics-table-body');
  const paginationContainer = $('#pagination-container');
  const filtersForm = $('#metrics-filters-form');
  const filterButton = $('#filter-button');

  // Inicializar Flatpickr
  flatpickr('#date_range', {
    mode: 'range',
    locale: 'es',
    dateFormat: 'Y-m-d'
  });

  // Nombres de los días de la semana
  const daysOfWeek = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

  /**
   * Formatea las horas decimales a "X H Y Min"
   * @param {number} decimalHours
   * @returns {string}
   */
  function formatHours(decimalHours) {
    if (!decimalHours) return '0 H 0 Min';
    const hours = Math.floor(decimalHours);
    const minutes = Math.round((decimalHours - hours) * 60);
    return `${hours} H ${minutes} Min`;
  }

  /**
   * Formatea los minutos decimales a "X Min Y Seg"
   * @param {number} decimalMinutes
   * @returns {string}
   */
  function formatMinutes(decimalMinutes) {
    if (!decimalMinutes) return '0 Min 0 Seg';
    const minutes = Math.floor(decimalMinutes);
    const seconds = Math.round((decimalMinutes - minutes) * 60);
    return `${minutes} Min ${seconds} Seg`;
  }

  function fetchKpis(params = {}) {
    $.ajax({
      url: kpisApiUrl,
      data: params,
      success: function (data) {
        let kpisHtml = `
                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-package ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">${data.total_orders}</h4>
                                </div>
                                <p class="mb-1">Total de Pedidos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-border-shadow-success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-success"><i class="ti ti-rocket ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">${data.avg_ratio}</h4>
                                </div>
                                <p class="mb-1">Eficiencia (Pedidos/Hora)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-border-shadow-danger h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-clock ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">${formatHours(data.total_hours)}</h4>
                                </div>
                                <p class="mb-1">Horas Trabajadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-border-shadow-info h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-info"><i class="ti ti-hourglass-low ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">${formatMinutes(data.avg_cdt)}</h4>
                                </div>
                                <p class="mb-1">CDT Promedio</p>
                            </div>
                        </div>
                    </div>
                `;
        kpisContainer.html(kpisHtml);
      },
      error: function (xhr) {
        console.error('Error fetching KPIs:', xhr);
      }
    });
  }

  function fetchTableData(page = 1, params = {}) {
    const updatedParams = { ...params, page: page };
    $.ajax({
      url: metricsApiUrl,
      data: updatedParams,
      success: function (data) {
        let tableRows = '';
        if (data.data.length > 0) {
          data.data.forEach(metric => {
            const date = new Date(metric.fecha);
            const formattedDate = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
            const dayOfWeek = daysOfWeek[date.getDay()];

            tableRows += `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${dayOfWeek}</td>
                                <td>${metric.pedidos_entregados}</td>
                                <td>${formatHours(metric.horas)}</td>
                                <td>${(metric.pedidos_entregados / metric.horas).toFixed(2) || 0}</td>
                                <td>${metric.cancelados}</td>
                                <td>${metric.reasignaciones}</td>
                                <td>${formatMinutes(metric.tiempo_promedio)}</td>
                            </tr>
                        `;
          });
        } else {
          tableRows = `<tr><td colspan="8" class="text-center">No hay datos disponibles.</td></tr>`;
        }
        tableBody.html(tableRows);
        renderPagination(data);
      },
      error: function (xhr) {
        console.error('Error fetching table data:', xhr);
      }
    });
  }

  function renderPagination(data) {
    const totalPages = data.last_page;
    let paginationHtml = `<ul class="pagination">`;
    for (let i = 1; i <= totalPages; i++) {
      const activeClass = i === data.current_page ? 'active' : '';
      paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
    }
    paginationHtml += `</ul>`;
    paginationContainer.html(paginationHtml);
  }

  // Lógica para capturar los valores del formulario como un objeto
  function getFilterParams() {
    const dateRange = $('#date_range').val().split(' a ');
    const dateFrom = dateRange[0] || '';
    const dateTo = dateRange[1] || '';

    return {
      date_from: dateFrom,
      date_to: dateTo,
      transport: $('#transport').val()
    };
  }

  // Eventos
  filtersForm.on('submit', function (e) {
    e.preventDefault();
    const params = getFilterParams();
    fetchKpis(params);
    fetchTableData(1, params);
  });

  filterButton.on('click', function (e) {
    e.preventDefault();
    filtersForm.trigger('submit');
  });

  paginationContainer.on('click', '.page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    const params = getFilterParams();
    fetchTableData(page, params);
  });

  // Carga inicial
  filtersForm.trigger('submit');
});
