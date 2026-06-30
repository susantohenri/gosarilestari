<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
  <!-- Stat 1 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-users"></i>
      </div>
      <span class="flex items-center gap-1 text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> +<?= $card_warga['mendaftar_minggu_ini'] ?> minggu ini
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Total Warga Aktif</div>
    <div class="text-2xl font-bold text-slate-800"><?= $card_warga['warga_aktif'] ?></div>
  </div>

  <!-- Stat 2 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-wallet"></i>
      </div>
      <?php $progressColor = 0 <= $card_saldo['progress'] ? 'green' : 'red'  ?>
      <span class="flex items-center gap-1 text-xs font-semibold text-<?= $progressColor ?>-600 bg-<?= $progressColor ?>-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> <?= number_format($card_saldo['progress'], 1) ?>%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Saldo Beredar</div>
    <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($card_saldo['beredar'], 0, ',', '.') ?></div>
  </div>

  <!-- Stat 3 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-scale-balanced"></i>
      </div>
      <?php $progressColor = 0 <= $card_setoran['progress'] ? 'green' : 'red'  ?>
      <span class="flex items-center gap-1 text-xs font-semibold text-<?= $progressColor ?>-600 bg-<?= $progressColor ?>-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-up"></i> <?= number_format($card_setoran['progress'], 1) ?>%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Setoran Bulan Ini</div>
    <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($card_setoran['bulan_ini'], 0, ',', '.') ?></div>
  </div>

  <!-- Stat 4 -->
  <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-4">
      <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-basket-shopping"></i>
      </div>
      <?php $progressColor = 0 <= $card_tukar_produk['progress'] ? 'green' : 'red'  ?>
      <span class="flex items-center gap-1 text-xs font-semibold text-<?= $progressColor ?>-500 bg-<?= $progressColor ?>-50 px-2 py-1 rounded-full">
        <i class="fa-solid fa-arrow-trend-down"></i> <?= number_format($card_tukar_produk['progress'], 1) ?>%
      </span>
    </div>
    <div class="text-slate-500 text-sm font-medium mb-1">Tukar Produk</div>
    <div class="text-2xl font-bold text-slate-800">Rp <?= number_format($card_tukar_produk['bulan_ini'], 0, ',', '.') ?></div>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 pb-6">
  <div class="flex flex-col gap-6 bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <div id="map" class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm h-[50vh] w-full"></div>
    <script>
      const mapData = <?= json_encode($card_map['data']) ?>;

      // Default center (ambil dari data pertama atau fallback)
      let defaultCenter = [-7.8800, 110.3050];
      let defaultZoom = 12;

      if (mapData.length > 0 && mapData[0].latitude && mapData[0].longitude) {
        defaultCenter = [mapData[0].latitude, mapData[0].longitude];
        defaultZoom = 13;
      }

      // Inisialisasi peta
      const map = L.map('map').setView(defaultCenter, defaultZoom);

      // Tile layer
      L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        subdomains: 'abcd',
        maxZoom: 19
      }).addTo(map);

      // Fungsi membuat marker dengan warna sesuai status
      function createMarker(lat, lng, color, popupHtml) {
        const markerHtml = `
        <div style="
            background-color: ${color};
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        "></div>
    `;

        const icon = L.divIcon({
          html: markerHtml,
          iconSize: [24, 24],
          className: 'custom-marker',
          popupAnchor: [0, -12]
        });

        return L.marker([lat, lng], {
          icon: icon
        }).bindPopup(popupHtml);
      }

      // Array untuk menyimpan bounds
      const bounds = [];

      // Loop data dan tambahkan marker
      mapData.forEach(item => {
        if (!item.latitude || !item.longitude) return;

        const popupContent = `
        <div style="min-width: 180px; font-size: 13px;">
            <strong>📍 ${item.nama_rtrw}</strong><br>
            🟢 Status: ${item.status}<br>
            🗑️ Total setoran: ${item.total_setoran} kali<br>
            ⚖️ Total berat: ${item.total_berat} kg<br>
            👥 Warga: ${item.total_warga} orang<br>
            📅 Update: ${item.last_update}<br><br>
            <a href='<?= site_url('SetorSampah?mapFilter=') ?>Sampah ${item.status} di ${item.nama_rtrw}|${item.uuid_rtrw}|${item.kategori}'><b>Lihat Detail</b></a>
        </div>
    `;

        const marker = createMarker(item.latitude, item.longitude, item.warna, popupContent);
        marker.addTo(map);

        bounds.push([item.latitude, item.longitude]);
      });

      // Fit peta ke semua marker
      if (bounds.length > 0) {
        map.fitBounds(bounds);
      }
    </script>
  </div>
  <div class="flex flex-col gap-6 bg-white p-6 rounded-xl border border-slate-100 shadow-sm text-right">
    <table id="warga_minus">
      <thead>
        <tr>
          <th>warga</th>
          <th style="text-align: right;">tagihan belum terbayar</th>
          <th style="text-align: right;">sampah belum terpilah</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 pb-6">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <div class="flex flex-col gap-6 bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-lg font-bold text-slate-800">Volume Sampah Tersetor</h2>
        <p class="text-sm text-slate-500">7 hari terakhir, dipecah per kategori</p>
      </div>
      <a href="<?= site_url('SetorSampah') ?>" class="text-sm text-brand-600 font-medium hover:text-brand-700">Detail &gt;</a>
    </div>

    <div style="height: 300px; width: 100%; position: relative;">
      <canvas id="volumeChart" style="height: 100%; width: 100%;"></canvas>
    </div>
    <script>
      const rawData = <?= json_encode($card_grafik['items']) ?>;
      const ctx = document.getElementById('volumeChart').getContext('2d');

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: rawData.labels,
          datasets: rawData.datasets
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.dataset.label + ': ' + context.raw + ' kg';
                }
              }
            }
          },
          scales: {
            x: {
              stacked: true,
            },
            y: {
              stacked: true,
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return value + ' kg';
                }
              }
            }
          }
        }
      });
    </script>
  </div>

  <div class="flex flex-col gap-6">

    <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm flex-1">
      <h2 class="text-lg font-bold text-slate-800 mb-1">Hasil Pemilahan Sampah</h2>
      <p class="text-sm text-slate-500 mb-4">Berdasarkan volume bulan ini</p>

      <div style="height: 300px; width: 100%; position: relative;">
        <canvas id="pieChart" style="height: 100%; width: 100%;"></canvas>
      </div>
      <script type="text/javascript">
        const topKategoriData = {
          "labels": ["Tidak Terpilah", "Terpilah Sebagian", "Terpilah dg Baik"],
          "datasets": [{
            "data": <?= json_encode($card_pie['items']) ?>,
            "backgroundColor": ["rgb(220, 38, 38)", "rgb(234, 179, 8)", "rgb(22, 163, 74)"],
            "borderColor": ["rgb(220, 38, 38)", "rgb(234, 179, 8)", "rgb(22, 163, 74)"],
            "borderWidth": 1
          }]
        };
        const pie = document.getElementById('pieChart').getContext('2d');

        new Chart(pie, {
          type: 'pie',
          data: {
            labels: topKategoriData.labels,
            datasets: [{
              data: topKategoriData.datasets[0].data,
              backgroundColor: topKategoriData.datasets[0].backgroundColor,
              borderColor: topKategoriData.datasets[0].borderColor,
              borderWidth: 1,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'top',
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return ` ${context.formattedValue} Kg`;
                  }
                }
              }
            }
          }
        });
      </script>
    </div>

  </div>
</div>