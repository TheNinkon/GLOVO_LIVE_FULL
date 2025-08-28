@extends('layouts/layoutMaster')

@section('title', 'Gestión de Prefacturas')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  {{-- CORRECCIÓN: Se agrega el archivo de localización de flatpickr --}}
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/flatpickr/l10n/es.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/prefacturas-index.js'])
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Prefacturas
  </h4>

  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card p-3">
        <form id="prefactura-form" enctype="multipart/form-data">
          <h5 class="mb-3">Nueva Prefactura</h5>
          <div class="mb-3">
            <label class="form-label" for="period">Período</label>
            <input type="text" id="period" name="period" class="form-control" placeholder="YYYY-MM-DD a YYYY-MM-DD"
              required />
          </div>
          <div class="mb-3">
            <label class="form-label" for="csv-file">Archivo CSV</label>
            <input type="file" id="csv-file" name="csv_file" class="form-control" accept=".csv, .txt" required />
          </div>
          <button type="submit" class="btn btn-primary d-grid w-100">Cargar Prefactura</button>
        </form>
      </div>
    </div>
  </div>

  <div class="card">
    <h5 class="card-header">Lista de Períodos de Prefacturación</h5>
    <div class="card-datatable table-responsive">
      <table class="table" id="prefacturas-table">
        <thead>
          <tr>
            <th>Período</th>
            <th>Total Ítems</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($prefacturas as $prefactura)
            <tr>
              <td>{{ $prefactura->start_at->format('d M Y') }} a {{ $prefactura->end_at->format('d M Y') }}</td>
              <td>{{ $prefactura->items_count }}</td>
              <td><span class="badge bg-label-info">{{ ucfirst($prefactura->status) }}</span></td>
              <td>
                <a href="{{ route('admin.prefacturas.show', $prefactura->id) }}" class="btn btn-sm btn-primary">Ver
                  Detalles</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
