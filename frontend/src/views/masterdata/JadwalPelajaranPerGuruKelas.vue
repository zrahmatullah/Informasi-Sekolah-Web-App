<template>
  <div class="card">
    <h2>Jadwal Pelajaran Saya</h2>

    <div class="p-field p-mb-3" style="max-width: 300px;">
      <label for="hari">Filter Hari:</label>
      <Dropdown
        id="hari"
        v-model="selectedHari"
        :options="hariOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Pilih Hari"
        class="w-full"
      />
    </div>

    <DataTable
      :value="filteredJadwal"
      :paginator="true"
      :rows="10"
      :loading="loading"
      responsiveLayout="scroll"
    >
      <Column field="mata_pelajaran.nama_mapel" header="Mata Pelajaran" />
      <Column field="kelas.nama_kelas" header="Kelas" />
      <Column field="hari" header="Hari" />
      <Column field="jam_mulai" header="Jam Mulai" />
      <Column field="jam_selesai" header="Jam Selesai" />
    </DataTable>

    <Message v-if="filteredJadwal.length === 0 && !loading" severity="info">
      Tidak ada data jadwal pelajaran ditemukan.
    </Message>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'
import Dropdown from 'primevue/dropdown'

const token = localStorage.getItem('access_token')
const jadwal = ref([])
const selectedHari = ref(null)
const loading = ref(false)
const toast = useToast()

const hariOptions = [
  { label: 'Senin', value: 'Senin' },
  { label: 'Selasa', value: 'Selasa' },
  { label: 'Rabu', value: 'Rabu' },
  { label: 'Kamis', value: 'Kamis' },
  { label: 'Jumat', value: 'Jumat' },
  { label: 'Sabtu', value: 'Sabtu' }
]

const filteredJadwal = computed(() => {
  if (!selectedHari.value) return jadwal.value
  return jadwal.value.filter(item => item.hari === selectedHari.value)
})

const fetchJadwal = async () => {
  loading.value = true
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/guru/jadwal', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    jadwal.value = res.data.data || []
  } catch (err) {
    console.error(err)
    toast.add({
      severity: 'error',
      summary: 'Gagal',
      detail: 'Gagal mengambil jadwal pelajaran',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

onMounted(fetchJadwal)
</script>

<style scoped>
.card {
  padding: 2rem;
}
</style>
