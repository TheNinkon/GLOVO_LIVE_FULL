'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dateRangePicker = document.getElementById('filter-date-range');
  const perPageSelect = document.getElementById('per-page-select');

  const cityFilter = document.getElementById('filter-city');
  const transportFilter = document.getElementById('filter-transport');
  const courierIdFilter = document.getElementById('filter-courier-id');
  const riderNameFilter = document.getElementById('filter-rider-name'); // NUEVO
  const weekdayFilter = document.getElementById('filter-weekday');
  const costOrderFilter = document.getElementById('filter-cost-order');
  const costHourFilter = document.getElementById('filter-cost-hour');

  const applyFiltersBtn = document.getElementById('apply-filters');
  const clearFiltersBtn = document.getElementById('clear-filters');
  const syncButton = document.getElementById('sync-button');

  const tableBody = document.getElementById('metrics-table-body');
  const paginationLinks = document.getElementById('pagination-links');

  const kpiOrders = document.getElementById('kpi-total-orders');
  const kpiRatio = document.getElementById('kpi-avg-ratio');
  const kpiHours = document.getElementById('kpi-total-hours');
  const kpiCdt = document.getElementById('kpi-avg-cdt');
  const kpiGanancia = document.getElementById('kpi-ganancia-total');
  const kpiCosto = document.getElementById('kpi-costo-total');
  const kpiUtilidad = document.getElementById('kpi-utilidad');

  // CORRECCIÓN: Configuración de la localización de flatpickr
  const fp = flatpickr(dateRangePicker, {
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

  function getFilters(page = 1) {
    const dates = (dateRangePicker.value || '').split(' a ');
    return {
      date_from: dates[0] || '',
      date_to: dates[1] || dates[0] || '',
      city: cityFilter.value,
      transport: transportFilter.value,
      courier_id: courierIdFilter.value,
      rider_name: riderNameFilter.value,
      weekday: weekdayFilter.value,
      cost_per_order: costOrderFilter.value,
      cost_per_hour: costHourFilter.value,
      per_page: perPageSelect ? perPageSelect.value : '50',
      page: page
    };
  }

  async function fetchData(page = 1) {
    const filters = getFilters(page);
    const params = new URLSearchParams(Object.entries(filters).filter(([_, value]) => value !== '' && value != null));

    showLoading(true);
    try {
      const [metricsRes, kpisRes] = await Promise.all([
        fetch(`/admin/metrics/list?${params}`),
        fetch(`/admin/metrics/kpis?${params}`)
      ]);

      if (!metricsRes.ok) {
        throw new Error(`Error ${metricsRes.status}: No se pudieron cargar las métricas.`);
      }
      if (!kpisRes.ok) {
        throw new Error(`Error ${kpisRes.status}: No se pudieron cargar los KPIs.`);
      }

      const metricsData = await metricsRes.json();
      const kpisData = await kpisRes.json();

      if (perPageSelect && metricsData && typeof metricsData.per_page !== 'undefined') {
        perPageSelect.value = String(metricsData.per_page);
      }

      renderTable(metricsData.data || []);
      renderPagination(metricsData);
      renderKpis(kpisData);
    } catch (error) {
      handleFetchError(error);
    } finally {
      showLoading(false);
    }
  }

  function renderTable(metrics) {
    tableBody.innerHTML = '';
    if (!metrics || metrics.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="14" class="text-center p-4">
            No se encontraron resultados para los filtros seleccionados.
          </td>
        </tr>`;
      return;
    }

    const vehicleIcons = { BICYCLE: '🚲', MOTORBIKE: '🛵', CAR: '🚗', SCOOTER: '🛴' };
    const costPerOrder = parseFloat(costOrderFilter.value) || 0;
    const costPerHour = parseFloat(costHourFilter.value) || 0;

    metrics.forEach(metric => {
      const transportIcon = vehicleIcons[metric.transport.toUpperCase()] || '❓';
      const gananciaDiaria = (Number(metric.pedidos_entregados) || 0) * costPerOrder;
      const costoRider = (Number(metric.horas) || 0) * costPerHour;
      const gananciaNeta = gananciaDiaria - costoRider;

      const fechaFmt = metric.fecha
        ? new Date(metric.fecha).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
        : '';

      const ratio = Number(metric.ratio_entrega || 0);
      const ratioBadge = ratio >= 2.5 ? 'bg-label-success' : 'bg-label-warning';

      const row = `
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded-circle bg-label-secondary">
                  ${(metric.rider_name || 'S').charAt(0)}
                </span>
              </div>
              <div>
                <h6 class="mb-0">${metric.rider_name || 'Sin Asignar'}</h6>
                <small class="text-muted">${metric.courier_id || ''}</small>
              </div>
            </div>
          </td>
          <td>${fechaFmt}</td>
          <td><span class="badge bg-label-info">${metric.ciudad || ''}</span></td>
          <td>${transportIcon} ${metric.transport || 'N/A'}</td>
          <td class="text-center fw-bold">${Number(metric.pedidos_entregados || 0)}</td>
          <td class="text-center">${Number(metric.horas || 0).toFixed(2)}h</td>
          <td><span class="badge ${ratioBadge}">${ratio.toFixed(2)}</span></td>
          <td class="text-center">${Number(metric.cancelados || 0).toFixed(1)}%</td>
          <td class="text-center">${Number(metric.reasignaciones || 0).toFixed(1)}%</td>
          <td class="text-center">${Number(metric.no_show || 0).toFixed(1)}%</td>
          <td class="text-center">${Number(metric.tiempo_promedio || 0).toFixed(1)}</td>
          <td class="text-success">
            ${gananciaDiaria.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' })}
          </td>
          <td class="text-danger">
            (${costoRider.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' })})
          </td>
          <td class="fw-bold ${gananciaNeta >= 0 ? 'text-success' : 'text-danger'}">
            ${gananciaNeta.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' })}
          </td>
        </tr>
      `;
      tableBody.insertAdjacentHTML('beforeend', row);
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
  }

  function renderKpis(kpis) {
    const formatCurrency = value =>
      (Number(value) || 0).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });

    kpiOrders.textContent = Number(kpis.total_orders || 0);
    kpiRatio.textContent = `${Number(kpis.avg_ratio || 0).toFixed(2)} órd/h`;
    kpiHours.textContent = `${Number(kpis.total_hours || 0).toFixed(2)}h`;
    kpiCdt.textContent = `${Number(kpis.avg_cdt || 0).toFixed(1)} min`;
    kpiGanancia.textContent = formatCurrency(kpis.ganancia_total);
    kpiCosto.textContent = formatCurrency(kpis.costo_total);
    kpiUtilidad.textContent = formatCurrency(kpis.utilidad);
  }

  function showLoading(isLoading) {
    const spinner = document.querySelector('.card-datatable table + .table-responsive .spinner-border');
    if (spinner) spinner.style.display = isLoading ? 'block' : 'none';
  }

  function handleFetchError(error) {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: `Hubo un error al cargar los datos: ${error.message || error}`,
      customClass: { confirmButton: 'btn btn-primary' },
      buttonsStyling: false
    });
    renderTable([]);
    renderKpis({});
  }

  applyFiltersBtn.addEventListener('click', () => fetchData(1));

  clearFiltersBtn.addEventListener('click', () => {
    fp.clear();
    const form = document.getElementById('filter-form');
    if (form) form.reset();

    if (costOrderFilter) costOrderFilter.value = '5.50';
    if (costHourFilter) costHourFilter.value = '12.00';

    if (perPageSelect) perPageSelect.value = '50';

    fetchData(1);
  });

  if (perPageSelect) {
    perPageSelect.addEventListener('change', () => fetchData(1));
  }

  syncButton.addEventListener('click', async () => {
    Swal.fire({
      title: 'Sincronizando...',
      text: 'Esto puede tardar unos minutos.',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    try {
      const response = await fetch('/admin/metrics/sync', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      });
      const data = await response.json();

      if (response.ok) {
        Swal.fire({
          icon: 'success',
          title: '¡Sincronizado!',
          text: data.success,
          customClass: { confirmButton: 'btn btn-primary' },
          buttonsStyling: false
        }).then(() => {
          fetchData(1);
        });
      } else {
        throw new Error(data.error || 'Error desconocido.');
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error de Sincronización',
        text: error.message || 'No se pudo conectar con el servidor.',
        customClass: { confirmButton: 'btn btn-primary' },
        buttonsStyling: false
      });
    }
  });

  paginationLinks.addEventListener('click', e => {
    e.preventDefault();
    const pageLink = e.target.closest('a.page-link');
    if (pageLink && pageLink.dataset.page) {
      fetchData(pageLink.dataset.page);
    }
  });

  fetchData(1);
});
