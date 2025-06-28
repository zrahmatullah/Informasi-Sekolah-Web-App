<template>
  <div class="card">
    <h3>Detail Nilai Siswa</h3>

    <!-- Dropdown Tahun Pelajaran -->
    <div class="p-field p-col-12 p-md-6">
      <label for="tahunPelajaran">Tahun Pelajaran</label>
      <Dropdown
        v-model="selectedTahunPelajaran"
        :options="tahunPelajaranList"
        optionLabel="tahun_pelajaran"
        optionValue="id"
        placeholder="Pilih Tahun Pelajaran"
        class="w-full"
        @change="fetchNilaiSiswaLogin"
      />
    </div>

    <!-- Spinner saat loading -->
    <div v-if="loading" class="mt-4">
      <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="4" />
    </div>

    <!-- Tabel Nilai -->
    <div v-else-if="filteredNilaiList.length > 0" class="mt-4">
      <Button
        label="Cetak Raport"
        icon="pi pi-print"
        class="p-button-success mb-3"
        @click="cetakRaport"
      />
      <DataTable :value="filteredNilaiList" responsiveLayout="scroll">
        <Column field="nama_mapel" header="Mata Pelajaran" />
        <Column field="nilai_tugas" header="Tugas" />
        <Column field="nilai_uts" header="UTS" />
        <Column field="nilai_ujian" header="Ujian" />
        <Column field="rata_rata" header="Rata-rata" />
        <Column field="grade" header="Grade" />
        <Column header="Status Akademis">
          <template #body="slotProps">
            <span :class="getStatusClass(slotProps.data.grade)">
              {{ getStatus(slotProps.data.grade) }}
            </span>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- Pesan jika tidak ada nilai -->
    <div v-else-if="selectedTahunPelajaran" class="mt-4">
      <p class="text-muted">Nilai belum tersedia untuk tahun ini</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Dropdown from 'primevue/dropdown'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import ProgressSpinner from 'primevue/progressspinner'
import { useToast } from 'primevue/usetoast'

const toast = useToast()
const token = localStorage.getItem('access_token')

const tahunPelajaranList = ref([])
const selectedTahunPelajaran = ref(null)
const filteredNilaiList = ref([])
const loading = ref(false)

onMounted(() => {
  fetchTahunPelajaran()
})

const fetchTahunPelajaran = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/tahun-pelajaran')
    tahunPelajaranList.value = res.data || []
  } catch (err) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Gagal memuat tahun pelajaran.',
      life: 3000
    })
  }
}

const fetchNilaiSiswaLogin = async () => {
  if (!selectedTahunPelajaran.value) {
    filteredNilaiList.value = []
    return
  }

  loading.value = true
  try {
    const res = await axios.get(
      `http://127.0.0.1:8000/api/siswa/nilai/${selectedTahunPelajaran.value}`,
      { headers: { Authorization: `Bearer ${token}` } }
    )
    filteredNilaiList.value = res.data || []
  } catch (err) {
    toast.add({
      severity: 'error',
      summary: 'Gagal',
      detail: 'Gagal mengambil data nilai siswa.',
      life: 3000
    })
    filteredNilaiList.value = []
  } finally {
    loading.value = false
  }
}

const cetakRaport = () => {
  if (!selectedTahunPelajaran.value) {
    toast.add({
      severity: 'warn',
      summary: 'Peringatan',
      detail: 'Silakan pilih tahun pelajaran terlebih dahulu.',
      life: 3000
    })
    return
  }

  const tahunId = selectedTahunPelajaran.value
  const url = `http://127.0.0.1:8000/api/siswa/raport/cetak/${tahunId}?token=${token}`
  window.open(url, '_blank')
}

const getStatus = (grade) => {
  if (['A', 'B'].includes(grade)) return 'Lulus'
  if (['C', 'D'].includes(grade)) return 'Remedial'
  return 'Tidak Lulus'
}

const getStatusClass = (grade) => {
  if (['A', 'B'].includes(grade)) return 'text-success'
  if (['C', 'D'].includes(grade)) return 'text-warning'
  return 'text-danger'
}
</script>
<style scoped>
.mt-4 {
  margin-top: 1.5rem;
}
.text-muted {
  color: #6c757d;
}
.text-success {
  color: green;
  font-weight: bold;
}
.text-warning {
  color: orange;
  font-weight: bold;
}
.text-danger {
  color: red;
  font-weight: bold;
}
</style>
