<div>

  <div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
      <div class="col">
        <div class="page-pretitle">Page</div>
        <h2 class="page-title">{{ $title }}</h2>
      </div>

    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">

      <div class="row row-deck row-cards">

        <div class="col-12">
          <div class="row row-cards">
            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="bg-primary text-white avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-group">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                          <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                          <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                          <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                          <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                          <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                        </svg>
                      </span>
                    </div>
                    <div class="col">
                      <div class="font-weight-medium">
                        All Karyawan
                      </div>
                      <div class="text-secondary">
                        {{ $totalKaryawan }} Orang
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="bg-green text-white avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                          <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                          <path d="M15 19l2 2l4 -4" />
                        </svg>
                      </span>
                    </div>
                    <div class="col">
                      <div class="font-weight-medium">
                        Karyawan Aktif
                      </div>
                      <div class="text-secondary">
                        {{ $aktif }} Orang
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="bg-danger text-white avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-cancel">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                          <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                          <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                          <path d="M17 21l4 -4" />
                        </svg>
                      </span>
                    </div>
                    <div class="col">
                      <div class="font-weight-medium">
                        Karyawan NonAktif
                      </div>
                      <div class="text-secondary">
                        {{ $nonaktif }} Orang
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card card-sm">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="bg-warning text-white avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-hourglass-empty">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" />
                          <path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" />
                        </svg>
                      </span>
                    </div>
                    <div class="col">
                      <div class="font-weight-medium">
                        Segera Berakhir
                      </div>
                      <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="text-secondary">
                          {{ $kontrakHabis }} Orang
                        </div>

                        @if ($kontrakHabis > 0)
                          <a href="{{ route('karyawan.renewal') }}"
                            class="text-warning text-decoration-none">
                            <small>Renewal</small>
                            <svg 
                            xmlns="http://www.w3.org/2000/svg" 
                            width="24" 
                            height="24" 
                            viewBox="0 0 24 24" fill="none" 
                            stroke="currentColor" 
                            stroke-width="2" 
                            stroke-linecap="round" stroke-linejoin="round" 
                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-right m-0">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                              <path d="M5 12l14 0" />
                              <path d="M15 16l4 -4" />
                              <path d="M15 8l4 4" />
                            </svg>
                          </a>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-8 col-md-8 col-sm-12">
          <div class="card">
            <h3 class="ms-3 mt-2">Karyawan (12 Bulan Terakhir)</h3>
            <div class="card-body">
              <canvas id="trenKaryawanChart" height="120"></canvas>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12">
          <div class="card">
            <h3 class="ms-3 mt-2">Distribusi Kategori</h3>
            <div class="card-body">
              <canvas id="kategoriChart"></canvas>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>

@push('scripts')

<script>
  document.addEventListener('livewire:navigated', () => {
    // Definisi data chart dari controller
    const tren = @json($trenKaryawan);
    const kategori = @json($kategoriDistribusi);

    // Fungsi inisialisasi chart
    const initCharts = () => {
        if (typeof Chart === 'undefined') return;

        // --- Tren Karyawan ---
        const trenCanvas = document.getElementById('trenKaryawanChart');
        if (trenCanvas && tren.labels.length > 0) {
            // Hancurkan chart lama jika ada (untuk mencegah double render/memory leak)
            const existingTrenChart = Chart.getChart(trenCanvas);
            if (existingTrenChart) existingTrenChart.destroy();

            const ctx = trenCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: tren.labels,
                    datasets: [{
                        label: 'Karyawan Aktif',
                        data: tren.values,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    devicePixelRatio: window.devicePixelRatio || 1,
                    plugins: {
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0,0,0,0.85)',
                            titleFont: { size: 12, weight: '600' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 6
                        },
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // --- Distribusi Kategori ---
        const kategoriCanvas = document.getElementById('kategoriChart');
        if (kategoriCanvas && kategori.labels.length > 0) {
            // Hancurkan chart lama jika ada
            const existingKategoriChart = Chart.getChart(kategoriCanvas);
            if (existingKategoriChart) existingKategoriChart.destroy();

            const ctx2 = kategoriCanvas.getContext('2d');
            kategori.labels = kategori.labels.map(l => l.toUpperCase());

            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: kategori.labels,
                    datasets: [{
                        data: kategori.data,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    devicePixelRatio: window.devicePixelRatio || 1,
                    plugins: {
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0,0,0,0.85)',
                            titleFont: { size: 12, weight: '600' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 6
                        },
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    };

    // --- Dynamic Script Loading ---
    if (typeof Chart === 'undefined') {
        const scriptId = 'chart-js-script';
        if (!document.getElementById(scriptId)) {
            const script = document.createElement('script');
            script.id = scriptId;
            script.src = "{{ asset('js/vendor/chart.js') }}";
            script.onload = initCharts;
            document.head.appendChild(script);
        } else {
            // Script sudah ada tapi mungkin belum loaded, atau sedang loading
            // Kita bisa cek event load atau coba initialize (jika cached)
            const script = document.getElementById(scriptId);
            script.addEventListener('load', initCharts);
        }
    } else {
        // Chart sudah terdefinisi, langsung init
        initCharts();
    }
  });
</script>

@endpush

