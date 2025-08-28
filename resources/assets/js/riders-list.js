/**
 * Page User List (Riders)
 */

'use strict';

// Datatable (jQuery)
$(function () {
  const ridersTable = $('.datatables-riders');

  if (ridersTable.length) {
    const dt_riders = ridersTable.DataTable({
      ajax: {
        url: riderListApi,
        type: 'GET',
        data: function (d) {
          d.status = $('#filter-by-status').val();
          d.search = $('#filter-by-name').val();
          d.length = d.length || 10;
        },
        dataSrc: function (json) {
          if (json && json.kpis) {
            // Actualiza las tarjetas con los datos de los KPIs
            $('#total-riders-count').text(json.kpis.total);
            $('#active-riders-count').text(json.kpis.active);
            $('#inactive-riders-count').text(json.kpis.inactive);
            $('#pending-riders-count').text(json.kpis.pending);
            $('#blocked-riders-count').text(json.kpis.blocked);
          }
          return json.data;
        },
        error: function (xhr, error, thrown) {
          console.log('Error de DataTables:', thrown);
          alert('Error al cargar los datos. Revisa la consola del navegador.');
        }
      },
      columns: [
        { data: 'id' },
        { data: 'id' },
        { data: 'full_name' },
        { data: 'email' },
        { data: 'phone' },
        { data: 'city' },
        { data: 'status' },
        { data: 'actions' }
      ],
      columnDefs: [
        {
          targets: -1,
          title: 'Acciones',
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            const riderId = full.id;
            return `
              <a href="/admin/riders/${riderId}" class="btn btn-sm btn-icon item-view"><i class="ti tabler-eye"></i></a>
              `;
          }
        },
        {
          targets: [0],
          visible: false,
          searchable: false
        },
        {
          targets: 1,
          render: function (data, type, full, meta) {
            const riderId = full.id;
            const initials = full.full_name
              ? full.full_name
                  .match(/\b(\w)/g)
                  .join('')
                  .substring(0, 2)
                  .toUpperCase()
              : 'N/A';
            return `
              <div class="d-flex justify-content-start align-items-center user-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-3">
                    <span class="avatar-initial rounded-circle bg-label-info">${initials}</span>
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <a href="/admin/riders/${riderId}" class="text-body text-truncate fw-medium">${full.full_name}</a>
                  <small class="text-muted">${full.email}</small>
                </div>
              </div>
            `;
          }
        },
        {
          targets: 6,
          render: function (data, type, full, meta) {
            const status = data;
            let badgeClass = '';
            let statusText = '';
            switch (status) {
              case 'active':
                badgeClass = 'bg-label-success';
                statusText = 'Activo';
                break;
              case 'inactive':
                badgeClass = 'bg-label-danger';
                statusText = 'Inactivo';
                break;
              case 'blocked':
                badgeClass = 'bg-label-secondary';
                statusText = 'Bloqueado';
                break;
              case 'pending':
                badgeClass = 'bg-label-warning';
                statusText = 'Pendiente';
                break;
              default:
                badgeClass = 'bg-label-info';
                statusText = 'Desconocido';
                break;
            }
            return `<span class="badge ${badgeClass}">${statusText}</span>`;
          }
        }
      ],
      dom:
        '<"row mx-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0 mt-3 mt-md-0"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Buscar...'
      },
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-secondary dropdown-toggle',
          text: '<i class="ti tabler-download me-sm-1"></i> <span class="d-none d-sm-inline-block">Exportar</span>',
          buttons: [
            {
              extend: 'copy',
              text: '<i class="ti tabler-copy me-2"></i>Copiar',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'excel',
              text: '<i class="ti tabler-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'csv',
              text: '<i class="ti tabler-file-text me-2"></i>CSV',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'pdf',
              text: '<i class="ti tabler-file-text me-2"></i>PDF',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
              extend: 'print',
              text: '<i class="ti tabler-printer me-2"></i>Imprimir',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            }
          ]
        }
      ]
    });
  }

  // Lógica para que los filtros personalizados actualicen la tabla
  $('#filter-by-name').on('keyup', function () {
    dt_riders.ajax.reload();
  });

  $('#filter-by-status').on('change', function () {
    dt_riders.ajax.reload();
  });
});
