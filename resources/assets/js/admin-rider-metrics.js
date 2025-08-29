import $ from 'jquery';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/l10n/es.js';

$(document).ready(function () {
  const riderId = window.location.pathname.split('/')[3];
  if (!riderId || isNaN(riderId)) {
    console.error('No se pudo encontrar el ID del rider en la URL.');
    return;
  }

  const metricsApiUrl = `/admin/api/riders/${riderId}/metrics/list`;
  const kpisApiUrl = `/admin/api/riders/${riderId}/metrics/kpis`;

  const kpisContainer = $('#kpis-container');
  const tableBody = $('#metrics-table-body');
  const paginationContainer = $('#pagination-container');
  const filtersForm = $('#metrics-filters-form');
  const filterButton = $('#filter-button');

  flatpickr('#date_range', {
    mode: 'range',
    locale: 'es',
    dateFormat: 'Y-m-d'
  });

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

  const daysOfWeek = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

  function formatHours(decimalHours) {
    const hours = Math.floor(decimalHours);
    const minutes = Math.round((decimalHours - hours) * 60);
    return `${hours} H ${minutes} Min`;
  }

  function formatMinutes(decimalMinutes) {
    const minutes = Math.floor(decimalMinutes);
    const seconds = Math.round((decimalMinutes - minutes) * 60);
    return `${minutes} Min ${seconds} Seg`;
  }

  function formatMoney(amount, currency = '€', negativeInParens = false) {
    let formatted = new Intl.NumberFormat('es-ES', { style: 'decimal', minimumFractionDigits: 2 }).format(amount);
    if (negativeInParens && amount < 0) {
      return `(${formatted.replace('-', '')} ${currency})`;
    }
    return `${formatted} ${currency}`;
  }

  function fetchKpis(params = {}) {
    $.ajax({
      url: kpisApiUrl,
      data: params,
      success: function (data) {
        let kpisHtml = `
                    <div class="col-lg-3 col-sm-6 mb-4">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-package ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">${data.total_orders}</h4>
                                </div>
                                <p class="mb-1">Pedidos Entregados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-4">
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
                    <div class="col-lg-3 col-sm-6 mb-4">
                        <div class="card card-border-shadow-warning h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-clock ti-md"></i></span>
                                    </div>
                                    <h4 class="ms-1 mb-0">${formatHours(data.total_hours)}</h4>
                                </div>
                                <p class="mb-1">Horas Totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-4">
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
                    <div class="col-lg-3 col-sm-6 mt-4">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0 me-2">${formatMoney(data.ganancia_total, '€')}</h5>
                                    <small>Ganancia Bruta</small>
                                </div>
                                <div class="card-icon"><span class="badge bg-label-success rounded-circle p-2"><i class="ti ti-arrow-up-right ti-sm"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mt-4">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0 me-2">${formatMoney(data.costo_total, '€', true)}</h5>
                                    <small>Costo Operativo</small>
                                </div>
                                <div class="card-icon"><span class="badge bg-label-danger rounded-circle p-2"><i class="ti ti-arrow-down-right ti-sm"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 mt-4">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="card-title mb-0">
                                    <h5 class="mb-0 me-2">${formatMoney(data.utilidad, '€')}</h5>
                                    <small>Ganancia Neta</small>
                                </div>
                                <div class="card-icon"><span class="badge bg-label-info rounded-circle p-2"><i class="ti ti-currency-euro ti-sm"></i></span></div>
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

            const netClass = metric.utilidad_neta >= 0 ? 'text-success' : 'text-danger';
            const netAmount =
              metric.utilidad_neta >= 0
                ? `${formatMoney(metric.utilidad_neta, '€')}`
                : `${formatMoney(metric.utilidad_neta, '€', true)}`;

            let vehicleIcon = '';
            if (metric.transport === 'BICYCLE') {
              vehicleIcon = '<i class="ti ti-bike"></i>';
            } else if (metric.transport === 'MOTORBIKE') {
              vehicleIcon = '<i class="ti ti-motorbike"></i>';
            }

            tableRows += `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${dayOfWeek}</td>
                                <td>${metric.courier_id}</td>
                                <td>${metric.ciudad}</td>
                                <td>${vehicleIcon} ${metric.transport}</td>
                                <td>${metric.pedidos_entregados}</td>
                                <td>${formatHours(metric.horas)}</td>
                                <td>${(metric.pedidos_entregados / metric.horas).toFixed(2) || 0}</td>
                                <td>${metric.cancelados}</td>
                                <td>${metric.reasignaciones}</td>
                                <td>${metric.no_show_percentage}%</td>
                                <td>${metric.ineligible_percentage}%</td>
                                <td>${formatMinutes(metric.tiempo_promedio)}</td>
                                <td>${formatMoney(metric.ganancia_bruta, '€')}</td>
                                <td>${formatMoney(metric.costo_operativo, '€', true)}</td>
                                <td class="${netClass}">${netAmount}</td>
                            </tr>
                        `;
          });
        } else {
          tableRows = `<tr><td colspan="16" class="text-center">No hay datos disponibles.</td></tr>`;
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

  // Lógica para ocultar/mostrar el panel de perfil
  const profileSidebar = $('#profile-sidebar');
  const profileContent = $('#profile-content');
  $('.toggle-profile-sidebar').on('click', function () {
    profileSidebar.toggle();
    profileContent.toggleClass('col-xl-8 col-lg-7 col-12');
  });

  // Carga inicial
  if ($('#metrics-tab').hasClass('active')) {
    filtersForm.trigger('submit');
  }
});
