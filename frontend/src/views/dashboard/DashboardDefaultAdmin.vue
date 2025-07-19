<template>
  <div class="dashboard">
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else>
      <div v-if="error" class="error">{{ error }}</div>
      <div v-else>
        <section class="cards">
          <div class="card" v-for="card in infoCards" :key="card.label">
            <p>{{ card.label }}</p>
            <h2>{{ card.value }}</h2>
          </div>
        </section>
        <section class="charts">
          <div class="chart-container">
            <h3 class="chart-title">Statistik Kritik & Saran per Kelas</h3>
            <p class="chart-description">
              Grafik batang ini menampilkan jumlah kritik dan saran yang masuk berdasarkan kelas.
            </p>
            <Chart
              v-if="kritikSaranChartData"
              type="bar"
              :data="kritikSaranChartData"
              :options="kritikSaranChartOptions"
            />
          </div>

          <div class="chart-container">
            <h3 class="chart-title">Distribusi Siswa per Kelas</h3>
            <p class="chart-description">
              Grafik pie ini menggambarkan jumlah siswa yang terdistribusi di setiap kelas.
            </p>
            <Chart
              v-if="siswaPerKelasChartData"
              type="pie"
              :data="siswaPerKelasChartData"
              :options="siswaPerKelasChartOptions"
            />
          </div>
        </section>
        <section class="announcements">
          <label>PENGUMUMAN</label>
          <div class="marquee">
            <marquee behavior="scroll" direction="left" scrollamount="6">
              <span v-for="(item, index) in pengumuman" :key="index">
                📢 <strong>{{ item.judul }}</strong>: {{ item.keterangan }}&nbsp;&nbsp;&nbsp;
              </span>
            </marquee>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Chart from "primevue/chart";

export default {
  name: "Dashboard",
  components: { Chart },
  data() {
    return {
      loading: false,
      error: null,
      pengumuman: [],
      kritikSaranPerKelas: {},
      siswaPerKelas: {},
      kritikSaranChartData: null,
      kritikSaranChartOptions: null,
      siswaPerKelasChartData: null,
      siswaPerKelasChartOptions: null,
      infoCards: [],
    };
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      this.loading = true;
      try {
        const token = localStorage.getItem("access_token");
        const { data } = await axios.get("http://127.0.0.1:8000/api/dashboard/stats", {
          headers: { Authorization: `Bearer ${token}` },
        });

        this.infoCards = [
          { label: "JUMLAH USER", value: data.totalUser },
          { label: "JUMLAH SISWA", value: data.totalSiswa },
          { label: "JUMLAH WALIKELAS", value: data.totalWaliKelas },
          { label: "TOTAL KRITIK SARAN", value: data.totalKritikSaran },
        ];

        // this.kritikSaranPerKelas = data.kritikSaranPerKelas || {};
        this.kritikSaranPerKelas = {};
        data.kritikSaranPerKelas.forEach(item => {
          this.kritikSaranPerKelas[item.nama_kelas] = item.total;
        });
        this.siswaPerKelas = data.siswaPerKelas || {};
        this.pengumuman = data.pengumuman || [];

        this.prepareCharts();
      } catch (e) {
        this.error = "Gagal mengambil data dashboard.";
      } finally {
        this.loading = false;
      }
    },

    prepareCharts() {
      const labelsKelas = Object.keys(this.siswaPerKelas);
      const kritikData = labelsKelas.map(kelas => this.kritikSaranPerKelas[kelas] ?? 0);

      this.kritikSaranChartData = {
        labels: labelsKelas,
        datasets: [
          {
            label: "Jumlah Kritik Saran",
            data: kritikData,
            backgroundColor: "#003080",
          },
        ],
      };

      this.kritikSaranChartOptions = {
        responsive: true,
        plugins: {
          legend: {
            display: true
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              suggestedMax: 5
            }
          }
        }
      };

      const siswaData = labelsKelas.map(kelas => this.siswaPerKelas[kelas] ?? 0);
      const colors = [
        "#003080", "#00509e", "#0077b6", "#0096c7", "#00b4d8",
        "#48cae4", "#90e0ef", "#ade8f4", "#caf0f8", "#d0f0ff"
      ];

      this.siswaPerKelasChartData = {
        labels: labelsKelas,
        datasets: [
          {
            data: siswaData,
            backgroundColor: colors.slice(0, labelsKelas.length),
            hoverBackgroundColor: colors.slice(0, labelsKelas.length),
          },
        ],
      };

      this.siswaPerKelasChartOptions = {
        responsive: true,
        plugins: {
          legend: { position: "right" },
        },
      };
    },
  },
};
</script>

<style scoped>
.dashboard {
  padding: 2rem 3rem;
  background-color: #ffffff;
  font-family: "Segoe UI", sans-serif;
  min-height: 100vh;
  color: #333;
}

.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.card {
  background: #fff;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  text-align: center;
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-4px);
}

.card p {
  font-size: 1.1rem;
  color: #777;
  margin: 0;
}

.card h2 {
  font-size: 2.8rem;
  color: #003080;
  margin: 0.5rem 0 0;
}

.charts {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  justify-content: center;
  margin-bottom: 2rem;
}

.chart-container {
  flex: 1 1 48%;
  background: #fff;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  min-height: 700px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.chart-title {
  font-size: 1.4rem;
  font-weight: 600;
  color: #003080;
  margin-bottom: 0.5rem;
}

.chart-description {
  font-size: 0.95rem;
  color: #666;
  margin-bottom: 1.5rem;
}

.announcements label {
  font-size: 1.2rem;
  font-weight: bold;
  color: #003080;
  margin-bottom: 0.5rem;
  display: block;
}

.marquee {
  background: #eaf0f9;
  border-radius: 12px;
  padding: 1rem;
  overflow: hidden;
}

.marquee marquee {
  font-size: 1rem;
  font-weight: 500;
  color: #444;
}

.loading,
.error {
  text-align: center;
  padding: 1.5rem;
  font-size: 1.2rem;
  color: #555;
}

@media (max-width: 1024px) {
  .charts {
    flex-direction: column;
  }

  .chart-container {
    flex: 1 1 100%;
    min-height: 500px;
  }
}
</style>
