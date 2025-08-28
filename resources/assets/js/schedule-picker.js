import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
  const scheduleCard = document.querySelector('.schedule-card');
  if (!scheduleCard) return;

  const selectUrl = scheduleCard.dataset.selectUrl;
  const deselectUrl = scheduleCard.dataset.deselectUrl;
  const csrfToken = scheduleCard.dataset.csrfToken;
  const initialScheduleData = JSON.parse(scheduleCard.dataset.scheduleData);
  const defaultDay = scheduleCard.dataset.defaultDay;

  const dateSelector = document.querySelector('.date-selector');
  const tabsContainer = document.querySelector('.schedule-tabs');
  const contentAvailable = document.getElementById('disponibles-content');
  const contentReserved = document.getElementById('reservadas-content');
  const reservedHoursDisplay = document.getElementById('summary-reservadas');
  const comodinesDisplay = document.getElementById('summary-comodines');
  const deadlineDisplay = document.getElementById('deadline-display');

  // Función para actualizar la cuenta regresiva del plazo de reserva
  function updateCountdown() {
    if (!deadlineDisplay) return;

    const deadlineTime = new Date(deadlineDisplay.dataset.deadline).getTime();
    const now = new Date().getTime();
    const distance = deadlineTime - now;

    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    const countdownText = document.getElementById('countdown-text');

    if (distance > 0) {
      countdownText.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
      if (hours < 24) {
        deadlineDisplay.classList.add('expiring');
      }
    } else {
      countdownText.innerHTML = 'Finalizado';
      deadlineDisplay.classList.remove('expiring');
      deadlineDisplay.classList.add('expired');
      const currentDay = document.querySelector('.date-selector-item.active').dataset.date;
      renderDaySlots(currentDay);
      clearInterval(countdownInterval);
    }
  }

  let countdownInterval;
  if (deadlineDisplay && deadlineDisplay.dataset.deadline) {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
  }

  function renderDaySlots(date) {
    if (!initialScheduleData || !initialScheduleData[date]) {
      contentAvailable.innerHTML = `<div class="alert alert-warning text-center m-4">No hay un horario disponible para esta semana.</div>`;
      contentReserved.innerHTML = '';
      return;
    }

    const dayData = initialScheduleData[date];
    let availableSlotsHtml = '';
    let reservedSlotsHtml = '';
    const now = new Date();
    const deadline = deadlineDisplay ? new Date(deadlineDisplay.dataset.deadline) : null;
    const isPastDeadline = deadline && now > deadline;

    dayData.slots.forEach(slot => {
      const isMine = slot.status === 'mine';
      const isAvailable = slot.status === 'available';
      const isUnavailable = slot.status === 'unavailable';

      const isSelectable = !isPastDeadline && isAvailable;
      const isDeselectable = !isPastDeadline && isMine;

      const slotClass = isMine ? 'mine' : isAvailable ? 'available' : 'locked';
      const slotText = isMine ? 'Reservado' : isAvailable ? 'Disponible' : '';
      const slotIcon = isMine ? 'minus' : isAvailable ? 'plus' : 'lock';

      availableSlotsHtml += `
                <div class="daily-schedule-slot" data-slot="${slot.identifier}">
                    <div class="slot-time">${slot.time}</div>
                    <div class="slot-bar ${slotClass} ${isSelectable || isDeselectable ? '' : 'not-clickable'}">
                        <span>${slotText}</span>
                        <i class="ti tabler-${slotIcon}"></i>
                    </div>
                </div>
            `;

      if (isMine) {
        reservedSlotsHtml += `
                    <div class="reserved-slot-item" data-slot="${slot.identifier}">
                        <span>${slot.time}</span>
                        <button class="btn btn-sm btn-icon btn-outline-danger btn-deselect-slot ${isDeselectable ? '' : 'disabled'}" ${isDeselectable ? '' : 'disabled'}>
                            <i class="ti tabler-x"></i>
                        </button>
                    </div>
                `;
      }
    });

    contentAvailable.innerHTML =
      availableSlotsHtml || `<div class="alert alert-info text-center m-4">No hay slots para este día.</div>`;
    contentReserved.innerHTML =
      reservedSlotsHtml || `<div class="alert alert-info text-center m-4">No has reservado horas para este día.</div>`;

    attachEventListeners();
  }

  dateSelector.querySelectorAll('.date-selector-item').forEach(item => {
    item.addEventListener('click', function () {
      dateSelector.querySelector('.active').classList.remove('active');
      this.classList.add('active');
      renderDaySlots(this.dataset.date);
    });
  });

  tabsContainer.querySelectorAll('.schedule-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      tabsContainer.querySelector('.active').classList.remove('active');
      this.classList.add('active');
      document.querySelector('.schedule-content.active').classList.remove('active');
      document.getElementById(`${this.dataset.tab}-content`).classList.add('active');
    });
  });

  function attachEventListeners() {
    contentAvailable.querySelectorAll('.slot-bar.available').forEach(bar => {
      bar.addEventListener('click', function () {
        const slotIdentifier = this.closest('.daily-schedule-slot').dataset.slot;
        updateSlotStatus(slotIdentifier, 'select');
      });
    });

    contentReserved.querySelectorAll('.btn-deselect-slot:not([disabled])').forEach(btn => {
      btn.addEventListener('click', function () {
        const slotIdentifier = this.closest('.reserved-slot-item').dataset.slot;
        updateSlotStatus(slotIdentifier, 'deselect');
      });
    });
  }

  async function updateSlotStatus(slotIdentifier, action) {
    const url = action === 'select' ? selectUrl : deselectUrl;
    const method = 'POST';

    try {
      const response = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ slot: slotIdentifier })
      });

      // Si la respuesta no es OK, leemos el mensaje de error y lo mostramos
      if (!response.ok) {
        const errorData = await response.json();
        await Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: errorData.message || 'Ha ocurrido un error inesperado. Inténtalo de nuevo.'
        });
        return;
      }

      const data = await response.json();

      const currentDay = document.querySelector('.date-selector-item.active').dataset.date;
      if (initialScheduleData[currentDay]) {
        const slot = initialScheduleData[currentDay].slots.find(s => s.identifier === slotIdentifier);
        if (slot) {
          slot.status = action === 'select' ? 'mine' : 'available';
        }
      }
      renderDaySlots(currentDay);

      reservedHoursDisplay.textContent = `${data.total_hours.toFixed(1)}h`;
      if (data.edits_remaining !== undefined) {
        comodinesDisplay.textContent = data.edits_remaining;
      }
    } catch (error) {
      await Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar con el servidor. Por favor, revisa tu conexión a Internet.'
      });
      console.error('Error:', error);
    }
  }

  renderDaySlots(defaultDay);
});
