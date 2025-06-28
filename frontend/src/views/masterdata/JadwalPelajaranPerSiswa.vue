<template>
  <div class="p-5">
    <h2 class="text-xl font-bold mb-4">Jadwal Pelajaran Saya</h2>

    <DataTable
      :value="sortedJadwal"
      :loading="loading"
      :filters="filters"
      filterDisplay="row"
      class="p-datatable-sm"
      responsiveLayout="scroll"
    >
      <Column field="hari" header="Hari" :sortable="true" />
      <Column field="jam_mulai" header="Jam Mulai" />
      <Column field="jam_selesai" header="Jam Selesai" />
      <Column field="mata_pelajaran.nama_mapel" header="Mata Pelajaran" />
      <Column field="guru.nama" header="Guru Pengajar" />
      <Column field="kelas.nama_kelas" header="Kelas" />
    </DataTable>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

const token = localStorage.getItem('access_token')
const jadwal = ref([])
const loading = ref(false)

// PrimeVue filter model
const filters = ref({
  ruangan: { value: null, matchMode: 'contains' }
})

// Untuk mengurutkan hari dari Senin sampai Jumat
const hariUrut = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']

const sortedJadwal = computed(() => {
  return [...jadwal.value].sort((a, b) => {
    const indexA = hariUrut.indexOf(a.hari)
    const indexB = hariUrut.indexOf(b.hari)
    return indexA - indexB
  })
})

const fetchJadwal = async () => {
  loading.value = true
  try {
    const response = await axios.get(
      'http://127.0.0.1:8000/api/jadwal-pelajaran/siswa/by-user',
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    )
    jadwal.value = response.data.data || response.data
  } catch (error) {
    console.error('Gagal mengambil jadwal:', error.response?.data || error.message)
    if (error.response?.status === 401) {
      alert('Token tidak valid atau sesi habis. Silakan login ulang.')
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchJadwal()
})
</script>
