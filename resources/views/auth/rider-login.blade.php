@extends('layouts/layoutMaster')
@section('title', 'Acceso de Rider')
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
              <span class="app-brand-text demo text-body fw-bold ms-1">Portal del Rider</span>
            </a>
          </div>
          <h4 class="mb-1 pt-2">¡Bienvenido! 🚀</h4>
          <p class="mb-4">Inicia sesión con tu DNI</p>
          <form id="formAuthentication" class="mb-3" action="{{ route('rider.login') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="dni" class="form-label">DNI</label>
              <input type="text" class="form-control" id="dni" name="dni" placeholder="Introduce tu DNI"
                autofocus>
            </div>
            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">Contraseña</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                {{-- CORRECCIÓN AQUÍ: de "ti-eye-off" a "tabler-eye-off" --}}
                <span class="input-group-text cursor-pointer"><i class="ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">Entrar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
