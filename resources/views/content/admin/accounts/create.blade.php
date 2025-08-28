@extends('layouts/layoutMaster')

@section('title', 'Crear Nueva Cuenta')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Cuentas /</span> Crear
  </h4>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.accounts.store') }}" method="POST">
        @csrf
        @include('content.admin.accounts._form')
      </form>
    </div>
  </div>
@endsection
