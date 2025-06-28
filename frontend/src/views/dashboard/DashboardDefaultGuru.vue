<template>
  <div class="p-4 grid gap-4">
    <div class="flex gap-4">
      <Card class="flex-1">
        <template #title>JUMLAH SISWA ANDA</template>
        <template #content>
          <h1 class="text-3xl font-bold text-center">{{ jumlahSiswa }}</h1>
        </template>
      </Card>
      <Card class="flex-1">
        <template #title>TOTAL KRITIK SARAN</template>
        <template #content>
          <h1 class="text-3xl font-bold text-center">{{ totalKritik }}</h1>
        </template>
      </Card>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <Card>
        <template #title>Statistik Jumlah Kritik Saran Per Bulan</template>
        <template #content>
          <Chart type="bar" :data="chartData" :options="chartOptions" style="height: 300px;" />
        </template>
      </Card>

      <!-- <Card>
        <template #title>Jadwal Hari Ini</template>
        <template #content>
          <DataTable
            :value="jadwalHariIni"
            responsiveLayout="scroll"
            :showEmptyMessage="true"
            emptyMessage="Tidak ada jadwal hari ini"
          >
            <Column field="mata_pelajaran" header="Mata Pelajaran" />
            <Column field="guru" header="Guru" />
            <Column field="hari" header="Hari" />
            <Column field="jam_mulai" header="Jam Mulai" />
            <Column field="jam_selesai" header="Jam Selesai" />
          </DataTable>
        </template>
      </Card> -->
    </div>
    <Card>
      <template #title>Pengumuman</template>
      <template #content>
        <p>{{ pengumuman }}</p>
      </template>
    </Card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Card from 'primevue/card'
import Chart from 'primevue/chart'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import axios from 'axios'

const token = localStorage.getItem('access_token')
const headers = { Authorization: `Bearer ${token}` }

const jumlahSiswa = ref(0)
const totalKritik = ref(0)
const pengumuman = ref('-')
const jadwalHariIni = ref([])

const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'Jumlah Kritik',
      backgroundColor: '#42A5F5',
      data: []
    }
  ]
})

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      labels: {
        color: '#333'
      }
    }
  },
  scales: {
    x: {
      ticks: { color: '#333' },
      grid: { color: '#eee' }
    },
    y: {
      ticks: { color: '#333' },
      grid: { color: '#eee' }
    }
  }
})

const fetchData = async () => {
  try {
    const baseUrl = 'http://127.0.0.1:8000/api/dashboard-guru'
    const res = await axios.get(`${baseUrl}/stats`, { headers })

    jumlahSiswa.value = res.data.jumlah_siswa ?? 0
    totalKritik.value = res.data.total_kritik ?? 0

    chartData.value.labels = res.data.kritik_perbulan?.labels ?? []
    chartData.value.datasets[0].data = res.data.kritik_perbulan?.values ?? []

    jadwalHariIni.value = res.data.jadwal_hari_ini ?? []
    pengumuman.value = res.data.pengumuman ?? '-'
  } catch (err) {
    console.error('Gagal memuat data dashboard guru:', err)
  }
}

onMounted(fetchData)
</script>

<style scoped>
.card {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  padding: 1rem;
}
</style>
