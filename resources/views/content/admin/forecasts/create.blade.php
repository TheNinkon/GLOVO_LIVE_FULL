@extends('layouts/layoutMaster')

@section('title', 'Importar Forecast')

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Admin / Forecasts /</span> Importar
  </h4>

  <div class="card">
    <div class="card-body">
      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('admin.forecasts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label for="city" class="form-label">Ciudad</label>
          <select class="form-control" id="city" name="city" required>
            @foreach ($availableCities as $city)
              <option value="{{ $city }}" {{ $selectedCity == $city ? 'selected' : '' }}>{{ $city }}
              </option>
            @endforeach
          </select>
          @error('city')
            <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="week_start_date" class="form-label">Fecha de Inicio de Semana (Lunes)</label>
          <input type="date" class="form-control" id="week_start_date" name="week_start_date"
            value="{{ $startOfWeek->format('Y-m-d') }}" required>
          @error('week_start_date')
            <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="booking_deadline" class="form-label">Fecha Límite para Reservar</label>
          <input type="datetime-local" class="form-control" id="booking_deadline" name="booking_deadline" required>
          @error('booking_deadline')
            <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="file" class="form-label">Archivo CSV del Forecast</label>
          <input class="form-control" type="file" id="file" name="file" required>
          @error('file')
            <div class="text-danger mt-2">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary">
          <i class="ti tabler-upload me-1"></i> Cargar Forecast
        </button>
      </form>
    </div>
  </div>
@endsection
