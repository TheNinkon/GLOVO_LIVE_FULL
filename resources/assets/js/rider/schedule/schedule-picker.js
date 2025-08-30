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

  let scheduleIsLocked = false;
  let contractedHours = 0;
  let reservedHours = 0;
  let forecastId = null;

  const hoursReservedEl = document.getElementById('summary-reservadas');
  const hoursContractedEl = document.getElementById('summary-contratadas');
  const dateSelectorItems = document.querySelectorAll('.date-selector-item');
  const availableContent = document.getElementById('disponibles-content');
  const reservedContent = document.getElementById('reservadas-content');
  const scheduleTabs = document.querySelectorAll('.schedule-tab');

  // Función para manejar la lógica de la semana actual
  function getWeekStart(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const day = date.getDay();
    const diff = date.getDate() - day + (day === 0 ? -6 : 1); // Ajusta a lunes
    return new Date(date.setDate(diff));
  }

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
        scheduleIsLocked = true; // Bloquea la selección
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      countdownEl.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

      if (distance < 86400000) {
        // Menos de 24 horas
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

    try {
      const response = await fetch(`/rider/schedule/data?start_date=${dateString}`);
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      const data = await response.json();

      forecastId = data.forecastId;
      scheduleIsLocked = data.is_locked;
      contractedHours = data.contractedHours;
      reservedHours = data.reservedHours;

      // Actualiza el resumen de horas
      hoursContractedEl.innerText = `${contractedHours}h`;
      hoursReservedEl.innerText = `${reservedHours.toFixed(1)}h`;

      if (data.schedule) {
        renderSchedule(data.schedule, data.riderSchedules);
      } else {
        availableContent.innerHTML = `<div class="alert alert-warning text-center m-4">No hay un horario disponible para esta semana.</div>`;
        reservedContent.innerHTML = '';
      }

      // Inicia el contador si hay una fecha límite
      if (data.deadline) {
        startCountdown(data.deadline);
      }

      // Deshabilita los botones si está bloqueado
      if (scheduleIsLocked) {
        document.getElementById('submit-schedule').disabled = true;
      }
    } catch (error) {
      console.error('Error fetching schedule data:', error);
      availableContent.innerHTML = `<div class="alert alert-danger text-center m-4">Error al cargar el horario.</div>`;
      reservedContent.innerHTML = '';
    }
  }

  // Función para renderizar los slots de horario
  function renderSchedule(schedule, riderSchedules) {
    availableContent.innerHTML = '';
    reservedContent.innerHTML = '';

    Object.keys(schedule).forEach(day => {
      const dayContainer = document.createElement('div');
      const dayName = new Date(day).toLocaleDateString('es-ES', { weekday: 'long' });
      dayContainer.innerHTML = `<h6 class="text-center text-muted text-uppercase mt-4">${dayName}</h6>`;

      schedule[day].forEach(slot => {
        const isMine = riderSchedules.some(s => s.day === day && s.start_time === slot.start_time);
        const isLockedOrOverLimit = scheduleIsLocked || (reservedHours >= contractedHours && !isMine);
        const classList = ['slot-bar'];
        let iconHtml = '';
        let cursor = '';

        if (isMine) {
          classList.push('mine');
          iconHtml = `<i class="ti ti-minus"></i>`;
          cursor = 'pointer';
        } else if (slot.available) {
          classList.push('available');
          iconHtml = `<i class="ti ti-plus"></i>`;
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
                    <div class="slot-time">${moment(slot.start_time).format('H:mm')}</div>
                    <div class="${classList.join(' ')}" style="cursor: ${cursor};" data-slot-id="${slot.id}" data-start-time="${slot.start_time}" data-end-time="${slot.end_time}" data-day="${day}">
                        <span>${moment(slot.start_time).format('H:mm')} - ${moment(slot.end_time).format('H:mm')}</span>
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

    document.getElementById('submit-schedule').addEventListener('click', function () {
      Swal.fire({
        icon: 'success',
        title: '¡Horario guardado!',
        text: 'Tus horas han sido reservadas y ya no podrás modificarlas.',
        confirmButtonText: 'Ok'
      });
      // Aquí se enviaría el formulario o la petición AJAX final si existiera
    });
  }

  // Funciones de manejo de peticiones AJAX
  async function handleSelectSlot(event) {
    const slotEl = event.currentTarget;
    const data = {
      forecast_id: forecastId,
      start_time: slotEl.dataset.startTime,
      end_time: slotEl.dataset.endTime,
      day: slotEl.dataset.day
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
          loadSchedule(defaultDay); // Recarga el horario
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
    const scheduleId = slotEl.dataset.slotId;
    const data = {
      schedule_id: scheduleId
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
  loadSchedule(defaultDay);
});
