@extends('layouts/layoutMaster')

@section('title', 'Editar Rider')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Riders /</span> Editar
  </h4>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.riders.update', $rider) }}" method="POST">
        @csrf
        @method('PUT')
        @include('content.admin.riders._form')
      </form>
    </div>
  </div>
@endsection
