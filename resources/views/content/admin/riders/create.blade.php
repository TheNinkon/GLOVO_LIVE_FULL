@extends('layouts/layoutMaster')

@section('title', 'Crear Nuevo Rider')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Riders /</span> Crear
  </h4>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.riders.store') }}" method="POST">
        @csrf
        @include('content.admin.riders._form')
      </form>
    </div>
  </div>
@endsection
