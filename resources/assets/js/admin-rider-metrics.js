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

  // Manejo de pestañas: inicializa los scripts solo si la pestaña de métricas está visible
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const target = $(e.target).attr('href');
    if (target === '#metrics-tab') {
      initializeMetricsScripts();
    }
  });

  function initializeMetricsScripts() {
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

    function fetchKpis(params) {
      $.ajax({
        url: kpisApiUrl,
        data: params,
        success: function (data) {
          let kpisHtml = `
                        <div class="col-lg-3 col-sm-6 mb-4"><div class="card"><div class="card-body"><div class="d-flex justify-content-between"><div class="d-flex align-items-center gap-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-primary"><i class="ti ti-truck ti-sm"></i></span></div><div class="card-info"><p class="card-title mb-0 me-2">$${data.costo_total}</p><h6 class="card-subtitle">Costo Total</h6></div></div></div></div></div></div>
                        <div class="col-lg-3 col-sm-6 mb-4"><div class="card"><div class="card-body"><div class="d-flex justify-content-between"><div class="d-flex align-items-center gap-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-currency-euro ti-sm"></i></span></div><div class="card-info"><p class="card-title mb-0 me-2">$${data.ganancia_total}</p><h6 class="card-subtitle">Ganancia Total</h6></div></div></div></div></div></div>
                        <div class="col-lg-3 col-sm-6 mb-4"><div class="card"><div class="card-body"><div class="d-flex justify-content-between"><div class="d-flex align-items-center gap-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-info"><i class="ti ti-chart-bar ti-sm"></i></span></div><div class="card-info"><p class="card-title mb-0 me-2">${data.total_orders}</p><h6 class="card-subtitle">Pedidos Totales</h6></div></div></div></div></div></div>
                        <div class="col-lg-3 col-sm-6 mb-4"><div class="card"><div class="card-body"><div class="d-flex justify-content-between"><div class="d-flex align-items-center gap-3"><div class="avatar"><span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-trending-up ti-sm"></i></span></div><div class="card-info"><p class="card-title mb-0 me-2">${data.avg_ratio}</p><h6 class="card-subtitle">Ratio Entrega/Hora</h6></div></div></div></div></div></div>
                    `;
          kpisContainer.html(kpisHtml);
        },
        error: function (xhr) {
          console.error('Error fetching KPIs:', xhr);
        }
      });
    }

    function fetchTableData(page = 1, params = {}) {
      params.page = page;
      $.ajax({
        url: metricsApiUrl,
        data: params,
        success: function (data) {
          let tableRows = '';
          if (data.data.length > 0) {
            data.data.forEach(metric => {
              tableRows += `
                                <tr>
                                    <td>${metric.fecha}</td>
                                    <td>${metric.courier_id}</td>
                                    <td>${metric.ciudad}</td>
                                    <td>${metric.transport}</td>
                                    <td>${metric.pedidos_entregados}</td>
                                    <td>${metric.horas}</td>
                                    <td>${metric.cancelados}</td>
                                    <td>${metric.reasignaciones}</td>
                                    <td>${metric.tiempo_promedio}</td>
                                </tr>
                            `;
            });
          } else {
            tableRows = `<tr><td colspan="9" class="text-center">No hay datos disponibles.</td></tr>`;
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

    filtersForm.on('submit', function (e) {
      e.preventDefault();
      const params = filtersForm.serialize();
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
      const params = filtersForm.serialize();
      fetchTableData(page, params);
    });

    // Carga inicial al activar la pestaña
    fetchKpis();
    fetchTableData();
  }

  // Carga inicial si la pestaña ya está activa por defecto
  if ($('#metrics-tab').hasClass('active')) {
    initializeMetricsScripts();
  }
});
