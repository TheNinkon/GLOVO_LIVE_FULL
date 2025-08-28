@extends('layouts/layoutMaster')

@section('title', 'Gestión de Cuentas')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/accounts-list.js')
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Cuentas
  </h4>

  <div class="card" @if (session('success')) data-success-message="{{ session('success') }}" @endif>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Listado de Cuentas</h5>
      <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary">
        <i class="ti tabler-plus me-1"></i> Crear Cuenta
      </a>
    </div>
    <div class="card-datatable table-responsive">
      <table class="table" id="accounts-table">
        <thead class="border-top">
          {{-- ASEGÚRATE DE QUE TIENES ESTAS 7 CABECERAS --}}
          <tr>
            <th>ID</th>
            <th>Courier ID</th>
            <th>Email</th>
            <th>Ciudad</th>
            <th>Estado</th>
            <th>Asignado a</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
@endsection
