@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="row">
  <div class="mb-3 col-md-6">
    <label for="courier_id" class="form-label">Courier ID</label>
    <input class="form-control" type="text" id="courier_id" name="courier_id"
      value="{{ old('courier_id', $account->courier_id ?? '') }}" required autofocus />
  </div>
  <div class="mb-3 col-md-6">
    <label for="email" class="form-label">Email de la cuenta</label>
    <input class="form-control" type="email" name="email" id="email"
      value="{{ old('email', $account->email ?? '') }}" required />
  </div>
  <div class="mb-3 col-md-6">
    <label for="password" class="form-label">Contraseña</label>
    <input class="form-control" type="password" id="password" name="password" />
    @isset($account)
      <small class="text-muted">Dejar en blanco para no cambiar la contraseña.</small>
    @endisset
  </div>
  <div class="mb-3 col-md-6">
    <label for="city" class="form-label">Ciudad</label>
    <select id="city" name="city" class="form-select" required>
      <option value="">Seleccionar ciudad</option>
      @foreach (['GRO', 'MAT', 'FIG', 'BCN', 'CAL'] as $city)
        <option value="{{ $city }}" @selected(old('city', $account->city ?? '') == $city)>{{ $city }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-3 col-md-6">
    <label for="start_date" class="form-label">Fecha de Inicio</label>
    <input class="form-control" type="date" id="start_date" name="start_date"
      value="{{ old('start_date', isset($account) ? $account->start_date->format('Y-m-d') : '') }}" required />
  </div>
  <div class="mb-3 col-md-6">
    <label for="end_date" class="form-label">Fecha de Fin (Opcional)</label>
    <input class="form-control" type="date" id="end_date" name="end_date"
      value="{{ old('end_date', isset($account) && $account->end_date ? $account->end_date->format('Y-m-d') : '') }}" />
  </div>
  <div class="mb-3 col-md-6">
    <label for="status" class="form-label">Estado</label>
    <select id="status" name="status" class="form-select" required>
      <option value="active" @selected(old('status', $account->status ?? 'active') == 'active')>Activa</option>
      <option value="inactive" @selected(old('status', $account->status ?? '') == 'inactive')>Inactiva</option>
      <option value="blocked" @selected(old('status', $account->status ?? '') == 'blocked')>Bloqueada</option>
    </select>
  </div>
  <div class="mb-3 col-md-6">
    <label for="notes" class="form-label">Notas (Opcional)</label>
    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $account->notes ?? '') }}</textarea>
  </div>
</div>
<div class="mt-2">
  <button type="submit" class="btn btn-primary me-2">Guardar Cuenta</button>
  <a href="{{ route('admin.accounts.index') }}" class="btn btn-label-secondary">Cancelar</a>
</div>
