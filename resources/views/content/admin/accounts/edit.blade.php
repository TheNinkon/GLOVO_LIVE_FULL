@extends('layouts/layoutMaster')

@section('title', 'Editar Cuenta')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Cuentas /</span> Editar
  </h4>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.accounts.update', $account) }}" method="POST">
        @csrf
        @method('PUT')
        @include('content.admin.accounts._form')
      </form>
    </div>
  </div>
@endsection
