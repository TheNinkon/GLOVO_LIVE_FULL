/**
 * Page User Profile (Rider)
 */

'use strict';

$(function () {
  const assignmentsApiUrl = "{{ route('admin.riders.assignments', $rider->id) }}";
  const metricsApiUrl = "{{ route('admin.riders.metrics', $rider->id) }}";

  const riderAssignmentsTable = $('.datatable-assignments');
  const riderMetricsTable = $('.datatable-metrics');

  // Inicializa la tabla de asignaciones
  if (riderAssignmentsTable.length) {
    const dt_assignments = riderAssignmentsTable.DataTable({
      ajax: {
        url: assignmentsApiUrl,
        type: 'GET',
        data: function (d) {
          d.status = $('#filter-assignment-status').val();
          d.type = $('#filter-assignment-type').val();
          d.length = d.length || 10;
        },
        dataSrc: 'data'
      },
      columns: [
        { data: 'id' },
        {
          data: 'amount',
          render: function (data) {
            return `${data}€`;
          }
        },
        {
          data: 'type',
          render: function (data) {
            return data === 'cash_out' ? 'Cash Out' : 'Propina';
          }
        },
        { data: 'status' },
        {
          data: 'created_at',
          render: function (data) {
            return moment(data).format('DD/MM/YYYY');
          }
        }
      ],
      dom:
        '<"row mx-2"' +
        '<"col-md-4"l><"col-md-8 text-end"Bf>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Buscar...'
      },
      buttons: [
        {
          extend: 'collection',
          text: '<i class="ti tabler-download me-sm-1"></i> <span class="d-none d-sm-inline-block">Exportar</span>',
          className: 'btn btn-label-secondary dropdown-toggle',
          buttons: [
            { extend: 'copy', className: 'dropdown-item' },
            { extend: 'excel', className: 'dropdown-item' },
            { extend: 'csv', className: 'dropdown-item' },
            { extend: 'pdf', className: 'dropdown-item' },
            { extend: 'print', className: 'dropdown-item' }
          ]
        }
      ]
    });

    // Recarga la tabla al cambiar los filtros
    $('#filter-assignment-type, #filter-assignment-status').on('change', function () {
      dt_assignments.ajax.reload();
    });
  }

  // Inicializa la tabla de métricas
  if (riderMetricsTable.length) {
    riderMetricsTable.DataTable({
      ajax: {
        url: metricsApiUrl,
        type: 'GET',
        dataSrc: 'data'
      },
      columns: [
        {
          data: 'fecha',
          render: function (data) {
            return moment(data).format('DD/MM/YYYY');
          }
        },
        { data: 'pedidos_entregados' },
        { data: 'horas' },
        { data: 'ratio_entrega' },
        { data: 'cancelados' },
        { data: 'reasignaciones' }
      ],
      dom: '<"row mx-2"<"col-md-6 text-start"B><"col-md-6 text-end"f>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      buttons: [
        {
          extend: 'collection',
          text: '<i class="ti tabler-download me-sm-1"></i> <span class="d-none d-sm-inline-block">Exportar</span>',
          className: 'btn btn-label-secondary dropdown-toggle',
          buttons: [
            { extend: 'copy', className: 'dropdown-item' },
            { extend: 'excel', className: 'dropdown-item' },
            { extend: 'csv', className: 'dropdown-item' },
            { extend: 'pdf', className: 'dropdown-item' },
            { extend: 'print', className: 'dropdown-item' }
          ]
        }
      ]
    });
  }
});
