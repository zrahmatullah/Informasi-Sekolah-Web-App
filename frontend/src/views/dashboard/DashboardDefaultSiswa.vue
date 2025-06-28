<template>
  <div class="p-4">
    <h2 class="text-2xl font-semibold mb-4">Dashboard Siswa</h2>

    <div v-if="loading" class="text-center">
      <ProgressSpinner
        style="width:50px;height:50px"
        strokeWidth="4"
        animationDuration=".5s"
        aria-label="Loading"
      />
    </div>

    <div v-else>
      <div class="grid mb-5">
        <div class="col-12 md:col-4">
          <div class="p-4 shadow-2 border-round bg-green-500 text-white text-center">
            <div class="text-lg">Total Mata Pelajaran</div>
            <div class="text-4xl font-bold mt-2">{{ totalMapel }}</div>
          </div>
        </div>
      </div>

      <div class="card shadow-2 border-round p-4">
        <h5 class="mb-3">Grafik Nilai Rata-rata per Tahun Pelajaran</h5>
        <Chart
          type="bar"
          :data="chartData"
          :options="chartOptions"
          class="w-full"
          style="height: 350px"
        />
      </div>
    </div>

    <div v-if="error" class="mt-4 text-red-500 text-center">
      Gagal memuat data dashboard. Silakan coba lagi.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'primevue/chart'
import ProgressSpinner from 'primevue/progressspinner'
import axios from 'axios'

const token = localStorage.getItem('access_token')
const headers = { Authorization: `Bearer ${token}` }

const totalMapel = ref(0)
const chartData = ref({})
const chartOptions = ref({})
const loading = ref(true)
const error = ref(false)

const fetchDashboard = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/dashboard-siswa/stats', { headers })

    const labels = res.data.grafik_nilai?.labels ?? []
    const values = res.data.grafik_nilai?.values?.map(v => Number(v)) ?? []

    totalMapel.value = res.data.total_mapel ?? 0

    chartData.value = {
      labels,
      datasets: [
        {
          label: 'Rata-rata Nilai',
          backgroundColor: '#42A5F5',
          borderRadius: 4,
          data: values
        }
      ]
    }

    chartOptions.value = {
      indexAxis: 'x',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            color: '#333'
          }
        },
        tooltip: {
          callbacks: {
            label: (context) => `Rata-rata: ${context.parsed.y}`
          }
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Tahun Pelajaran'
          },
          ticks: {
            color: '#555'
          },
          grid: {
            color: '#eee'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Nilai'
          },
          ticks: {
            stepSize: 10,
            color: '#555'
          },
          grid: {
            color: '#eee'
          }
        }
      }
    }
  } catch (err) {
    console.error('Gagal memuat dashboard siswa:', err)
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboard)
</script>

<style scoped>
.card {
  min-height: 300px;
}
</style>

