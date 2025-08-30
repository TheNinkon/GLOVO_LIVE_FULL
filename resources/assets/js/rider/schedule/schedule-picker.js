/**
 * Schedule Picker JS for Rider Dashboard
 */
document.addEventListener('DOMContentLoaded', function () {
  const scheduleCard = document.querySelector('.schedule-card');
  if (!scheduleCard) return;

  const csrfToken = scheduleCard.dataset.csrfToken;
  const selectUrl = scheduleCard.dataset.selectUrl;
  const deselectUrl = scheduleCard.dataset.deselectUrl;
  const defaultDay = scheduleCard.dataset.defaultDay;
  const apiUrl = scheduleCard.dataset.apiUrl;

  let scheduleIsLocked = scheduleCard.dataset.isLocked === 'true';
  let contractedHours = 0;
  let reservedHours = 0;
  let forecastId = scheduleCard.dataset.forecastId === 'null' ? null : scheduleCard.dataset.forecastId;

  const hoursReservedEl = document.getElementById('summary-reservadas');
  const hoursContractedEl = document.getElementById('summary-contratadas');
  const editsRemainingEl = document.getElementById('summary-comodines');
  const dateSelectorItems = document.querySelectorAll('.date-selector-item');
  const availableContent = document.getElementById('disponibles-content');
  const reservedContent = document.getElementById('reservadas-content');
  const scheduleTabs = document.querySelectorAll('.schedule-tab');

  // Lógica para el contador de la cuenta atrás
  function startCountdown(deadlineDate) {
    const deadline = new Date(deadlineDate);
    const countdownEl = document.getElementById('countdown-text');
    const deadlineDisplay = document.getElementById('deadline-display');

    const updateCountdown = () => {
      const now = new Date().getTime();
      const distance = deadline - now;

      if (distance < 0) {
        countdownEl.innerHTML = '¡Plazo expirado!';
        deadlineDisplay.classList.add('expired');
        scheduleIsLocked = true;
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      countdownEl.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

      if (distance < 86400000) {
        deadlineDisplay.classList.add('expiring');
      }

      requestAnimationFrame(updateCountdown);
    };
    updateCountdown();
  }

  // Función principal para cargar el horario de una semana
  async function loadSchedule(dateString) {
    availableContent.innerHTML = `<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div></div>`;
    reservedContent.innerHTML = `<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div></div>`;

    // Se corrige la URL para usar el parámetro `week` como se definió en las rutas
    const url = new URL(apiUrl);
    url.searchParams.append('week', dateString);

    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      const data = await response.json();

      if (!data.success) {
        availableContent.innerHTML = `<div class="alert alert-warning text-center m-4">${data.message}</div>`;
        reservedContent.innerHTML = '';
        return;
      }

      forecastId = data.forecast_id;
      scheduleIsLocked = data.isLocked;
      contractedHours = data.contractedHours;
      reservedHours = data.reservedHours;

      // Actualiza el resumen de horas y comodines
      hoursContractedEl.innerText = `${contractedHours}h`;
      hoursReservedEl.innerText = `${reservedHours.toFixed(1)}h`;
      editsRemainingEl.innerText = `${data.wildcards}`;

      if (data.scheduleData) {
        renderSchedule(data.scheduleData);
      } else {
        availableContent.innerHTML = `<div class="alert alert-warning text-center m-4">No hay un horario disponible para esta semana.</div>`;
        reservedContent.innerHTML = '';
      }

      // Inicia el contador si hay una fecha límite
      const deadlineDisplay = document.getElementById('deadline-display');
      if (data.deadline) {
        deadlineDisplay.dataset.deadline = data.deadline;
        startCountdown(data.deadline);
      } else {
        deadlineDisplay.innerHTML = `<span class="deadline-label">No hay plazo.</span>`;
      }

      // Deshabilita los botones si está bloqueado
      const submitBtn = document.getElementById('submit-schedule');
      if (submitBtn) {
        submitBtn.disabled = scheduleIsLocked;
      }
    } catch (error) {
      console.error('Error fetching schedule data:', error);
      availableContent.innerHTML = `<div class="alert alert-danger text-center m-4">Error al cargar el horario.</div>`;
      reservedContent.innerHTML = '';
    }
  }

  // Función para renderizar los slots de horario
  function renderSchedule(scheduleData) {
    availableContent.innerHTML = '';
    reservedContent.innerHTML = '';

    if (!scheduleData || !Array.isArray(scheduleData)) {
      availableContent.innerHTML = `<div class="alert alert-warning text-center m-4">No hay datos de horario para mostrar.</div>`;
      return;
    }

    scheduleData.forEach(dayData => {
      const dayContainer = document.createElement('div');
      dayContainer.innerHTML = `<h6 class="text-center text-muted text-uppercase mt-4">${dayData.dayName}</h6>`;

      if (!dayData.slots || !Array.isArray(dayData.slots)) {
        return;
      }

      dayData.slots.forEach(slot => {
        const isMine = slot.status === 'mine';
        const isAvailable = slot.status === 'available';
        const isLockedOrOverLimit = scheduleIsLocked || (reservedHours >= contractedHours && !isMine);
        const classList = ['slot-bar'];
        let iconHtml = '';
        let cursor = '';

        if (isMine) {
          classList.push('mine');
          iconHtml = `<i class="ti tabler-minus"></i>`;
          cursor = 'pointer';
        } else if (isAvailable) {
          classList.push('available');
          iconHtml = `<i class="ti tabler-plus"></i>`;
          cursor = 'pointer';
        } else {
          classList.push('locked');
          iconHtml = `<i class="ti tabler-lock"></i>`;
          cursor = 'not-allowed';
        }

        if (isLockedOrOverLimit && !isMine) {
          cursor = 'not-allowed';
          if (!classList.includes('locked')) {
            classList.push('locked');
          }
          if (!iconHtml) {
            iconHtml = `<i class="ti tabler-lock"></i>`;
          }
        }

        const slotEl = document.createElement('div');
        slotEl.className = 'daily-schedule-slot';
        slotEl.innerHTML = `
          <div class="slot-time">${slot.time}</div>
          <div class="${classList.join(' ')}" style="cursor: ${cursor};" data-slot-identifier="${slot.identifier}">
              <span>${slot.time}</span>
              ${iconHtml}
          </div>
        `;

        if (isMine) {
          reservedContent.appendChild(dayContainer.cloneNode(true));
          reservedContent.lastElementChild.appendChild(slotEl);
        } else {
          availableContent.appendChild(dayContainer.cloneNode(true));
          availableContent.lastElementChild.appendChild(slotEl);
        }
      });
    });

    attachEventListeners();
  }

  // Manejar eventos de clic en los slots
  function attachEventListeners() {
    document.querySelectorAll('.slot-bar').forEach(slot => {
      if (slot.classList.contains('available') && !scheduleIsLocked) {
        slot.addEventListener('click', handleSelectSlot);
      }
      if (slot.classList.contains('mine') && !scheduleIsLocked) {
        slot.addEventListener('click', handleDeselectSlot);
      }
    });

    const submitBtn = document.getElementById('submit-schedule');
    if (submitBtn) {
      submitBtn.addEventListener('click', function () {
        Swal.fire({
          icon: 'success',
          title: '¡Horario guardado!',
          text: 'Tus horas han sido reservadas y ya no podrás modificarlas.',
          confirmButtonText: 'Ok'
        });
      });
    }
  }

  // Funciones de manejo de peticiones AJAX
  async function handleSelectSlot(event) {
    const slotEl = event.currentTarget;
    const slotIdentifier = slotEl.dataset.slotIdentifier;

    const data = {
      _token: csrfToken,
      slot: slotIdentifier,
      forecast_id: forecastId
    };

    try {
      const response = await fetch(selectUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
      });

      const responseData = await response.json();
      if (!response.ok) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: responseData.message || 'Ha ocurrido un error inesperado.',
          confirmButtonText: 'Ok'
        });
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: responseData.message,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          loadSchedule(defaultDay);
        });
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error de Conexión',
        text: 'No se pudo conectar con el servidor.',
        confirmButtonText: 'Ok'
      });
    }
  }

  async function handleDeselectSlot(event) {
    const slotEl = event.currentTarget;
    const slotIdentifier = slotEl.dataset.slotIdentifier;
    const data = {
      _token: csrfToken,
      slot: slotIdentifier
    };

    try {
      const response = await fetch(deselectUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
      });

      const responseData = await response.json();
      if (!response.ok) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: responseData.message || 'Ha ocurrido un error inesperado.',
          confirmButtonText: 'Ok'
        });
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: responseData.message,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          loadSchedule(defaultDay);
        });
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error de Conexión',
        text: 'No se pudo conectar con el servidor.',
        confirmButtonText: 'Ok'
      });
    }
  }

  // Manejar la navegación de la semana y las pestañas
  dateSelectorItems.forEach(item => {
    item.addEventListener('click', function () {
      dateSelectorItems.forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      loadSchedule(this.dataset.date);
    });
  });

  scheduleTabs.forEach(tab => {
    tab.addEventListener('click', function () {
      scheduleTabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      document.querySelectorAll('.schedule-content').forEach(content => content.classList.remove('active'));
      document.getElementById(this.dataset.tab + '-content').classList.add('active');
    });
  });

  // Carga inicial del horario
  if (initialScheduleData && Object.keys(initialScheduleData).length > 0) {
    renderSchedule(initialScheduleData);
    hoursContractedEl.innerText = `${initialContractedHours}h`;
    hoursReservedEl.innerText = `${initialReservedHours.toFixed(1)}h`;
    editsRemainingEl.innerText = `${initialEditsRemaining}`;
  } else {
    loadSchedule(defaultDay);
  }
});
