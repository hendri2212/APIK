@extends('../welcome')

@section('title', 'Data Holiday Calendar')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Data Holiday Calendar</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container bg-white min-vh-100 py-3">
        <div class="rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #f66d6d 0%, #ef476f 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 small text-white-50">Kalender</p>
                    <h2 class="h5 mb-0">Tandai Tanggal Merah</h2>
                    <p class="mb-0 small text-white-50">Klik tanggal untuk menambah/menghapus hari libur.</p>
                </div>
                <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-calendar2-week fs-4"></i>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <button class="btn btn-outline-secondary btn-sm" id="prevMonth">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="text-center">
                        <div class="fw-bold" id="monthLabel"></div>
                        <div class="text-muted small">Sen - Min</div>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm" id="nextMonth">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div class="calendar-grid text-center small text-muted mb-2">
                    <div class="fw-semibold">Sen</div>
                    <div class="fw-semibold">Sel</div>
                    <div class="fw-semibold">Rab</div>
                    <div class="fw-semibold">Kam</div>
                    <div class="fw-semibold">Jum</div>
                    <div class="fw-semibold">Sab</div>
                    <div class="fw-semibold">Min</div>
                </div>

                <div id="calendarGrid" class="calendar-grid"></div>

                <div class="alert alert-info mt-3 d-none" id="calendarAlert"></div>
            </div>
        </div>
    </div>

    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
        }
        .calendar-day {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 6px;
            background-color: #f8fafc;
            min-height: 70px;
            transition: all 0.15s ease-in-out;
        }
        .calendar-day:hover {
            background-color: #eef2ff;
            border-color: #cbd5e1;
            cursor: pointer;
        }
        .calendar-day.outside-month {
            background-color: #f1f5f9;
            color: #9ca3af;
        }
        .calendar-day.holiday {
            background: linear-gradient(135deg, #ff9f43 0%, #ef476f 100%);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 6px 16px rgba(239, 71, 111, 0.25);
        }
        .calendar-day .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
    </style>

    <script>
        (() => {
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const holidayDates = new Set(@json(($holidays ?? collect())->pluck('holiday_date')));
            const csrfToken = '{{ csrf_token() }}';

            const monthLabel = document.getElementById('monthLabel');
            const calendarGrid = document.getElementById('calendarGrid');
            const alertBox = document.getElementById('calendarAlert');

            let current = new Date();

            const formatDate = (date) => {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            };

            const showAlert = (type, message) => {
                alertBox.className = `alert alert-${type} mt-3`;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
                setTimeout(() => alertBox.classList.add('d-none'), 2500);
            };

            const renderCalendar = () => {
                const year = current.getFullYear();
                const month = current.getMonth();
                monthLabel.textContent = `${monthNames[month]} ${year}`;

                const firstDay = new Date(year, month, 1);
                const startOffset = (firstDay.getDay() + 6) % 7; // convert Sunday=0 to Monday=6
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();
                const totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;

                let cells = '';

                for (let i = 0; i < totalCells; i++) {
                    let displayDate;
                    let inCurrentMonth = true;

                    if (i < startOffset) {
                        const day = daysInPrevMonth - startOffset + i + 1;
                        displayDate = new Date(year, month - 1, day);
                        inCurrentMonth = false;
                    } else if (i >= startOffset + daysInMonth) {
                        const day = i - (startOffset + daysInMonth) + 1;
                        displayDate = new Date(year, month + 1, day);
                        inCurrentMonth = false;
                    } else {
                        const day = i - startOffset + 1;
                        displayDate = new Date(year, month, day);
                    }

                    const isoDate = formatDate(displayDate);
                    const isHoliday = holidayDates.has(isoDate);

                    cells += `
                        <div class="calendar-day ${inCurrentMonth ? '' : 'outside-month'} ${isHoliday ? 'holiday' : ''}" data-date="${isoDate}">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="fw-semibold">${displayDate.getDate()}</span>
                                ${isHoliday ? '<span class="badge bg-light text-danger border border-light-subtle">Libur</span>' : ''}
                            </div>
                            <div class="small ${inCurrentMonth ? 'text-muted' : 'text-secondary'}">${isHoliday ? 'Tanggal merah' : ''}</div>
                        </div>
                    `;
                }

                calendarGrid.innerHTML = cells;
            };

            const toggleHoliday = (isoDate) => {
                fetch(`{{ route('holiday.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ holiday_date: isoDate }),
                })
                .then(async (res) => {
                    if (!res.ok) {
                        const errText = await res.text();
                        throw new Error(errText || 'Gagal menyimpan tanggal merah');
                    }
                    return res.json();
                })
                .then((data) => {
                    if (data.status === 'added') {
                        holidayDates.add(isoDate);
                        showAlert('success', `Ditandai libur: ${isoDate}`);
                    } else if (data.status === 'removed') {
                        holidayDates.delete(isoDate);
                        showAlert('secondary', `Tanggal libur dibatalkan: ${isoDate}`);
                    }
                    renderCalendar();
                })
                .catch((err) => {
                    console.error(err);
                    showAlert('danger', 'Gagal menyimpan, coba lagi.');
                });
            };

            calendarGrid.addEventListener('click', (event) => {
                const dayEl = event.target.closest('.calendar-day');
                if (!dayEl) return;
                const isoDate = dayEl.getAttribute('data-date');
                if (!isoDate) return;
                toggleHoliday(isoDate);
            });

            document.getElementById('prevMonth').addEventListener('click', () => {
                current = new Date(current.getFullYear(), current.getMonth() - 1, 1);
                renderCalendar();
            });

            document.getElementById('nextMonth').addEventListener('click', () => {
                current = new Date(current.getFullYear(), current.getMonth() + 1, 1);
                renderCalendar();
            });

            renderCalendar();
        })();
    </script>
@endsection
