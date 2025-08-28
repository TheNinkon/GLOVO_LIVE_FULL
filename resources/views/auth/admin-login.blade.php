@extends('layouts/layoutMaster')
@section('title', 'Admin Login')
@section('page-style')
  @vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection
@section('page-script')
  @vite('resources/assets/js/pages-auth.js')
@endsection

@section('content')
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <div class="card">
        <div class="card-body">
          <div class="app-brand justify-content-center mb-4 mt-2">
            <a href="{{ url('/') }}" class="app-brand-link gap-2">
              <span class="app-brand-text demo text-body fw-bold ms-1">RMS Admin</span>
            </a>
          </div>
          <h4 class="mb-1 pt-2">Panel de Administración</h4>
          <p class="mb-4">Por favor, inicia sesión para continuar</p>
          <form id="formAuthentication" class="mb-3" action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="text" class="form-control" id="email" name="email" placeholder="Introduce tu email"
                autofocus>
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Contraseña</label>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" />
                {{-- CORRECCIÓN AQUÍ: de "ti-eye-off" a "tabler-eye-off" --}}
                <span class="input-group-text cursor-pointer"><i class="ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">Iniciar Sesión</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
