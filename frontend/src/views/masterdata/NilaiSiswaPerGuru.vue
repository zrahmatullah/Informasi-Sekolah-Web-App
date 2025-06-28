<template>
  <div class="card">
    <h3>Detail Nilai Siswa</h3>

    <div class="p-fluid p-formgrid p-grid">
      <div class="p-field p-col-12 p-md-6">
        <label for="siswa">Pilih Siswa</label>
        <Dropdown
          v-model="selectedSiswaId"
          :options="siswaList"
          optionLabel="nama_lengkap_siswa"
          optionValue="id"
          placeholder="Pilih Siswa"
          @change="fetchNilaiSiswa"
        />
      </div>

      <div class="p-field p-col-12 p-md-6">
        <label for="tahunPelajaran">Tahun Pelajaran</label>
        <Dropdown
          v-model="selectedTahunPelajaranId"
          :options="tahunPelajaranList"
          optionLabel="tahun_pelajaran"
          optionValue="id"
          placeholder="Pilih Tahun Pelajaran"
          @change="fetchNilaiSiswa"
        />
      </div>
    </div>

    <div v-if="nilaiList.length" class="mt-4">
      <Button
        label="Cetak Raport"
        icon="pi pi-print"
        class="p-button-success mb-3"
        @click="cetakRaport"
      />
      <DataTable :value="nilaiList" responsiveLayout="scroll">
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

    <div v-else-if="selectedSiswaId && selectedTahunPelajaranId" class="mt-4">
      <p class="text-muted">Nilai Siswa belum lengkap keseluruhan.</p>
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
import { useToast } from 'primevue/usetoast'

const toast = useToast()
const token = localStorage.getItem('access_token')

const siswaList = ref([])
const tahunPelajaranList = ref([])
const selectedSiswaId = ref(null)
const selectedTahunPelajaranId = ref(null)
const nilaiList = ref([])

onMounted(async () => {
  await fetchDropdownData()
})

const fetchDropdownData = async () => {
  try {
    const [siswaRes, tahunRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/guru/data-siswa', {
        headers: { Authorization: `Bearer ${token}` }
      }),
      axios.get('http://127.0.0.1:8000/api/tahun-pelajaran')
    ])
    siswaList.value = siswaRes.data
    tahunPelajaranList.value = tahunRes.data
  } catch (err) {
    console.error(err)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Gagal memuat data dropdown.',
      life: 3000
    })
  }
}

const fetchNilaiSiswa = async () => {
  if (!selectedSiswaId.value || !selectedTahunPelajaranId.value) return

  try {
    const res = await axios.get(
      `http://127.0.0.1:8000/api/nilai/siswa/${selectedSiswaId.value}/tahun/${selectedTahunPelajaranId.value}`
    )

    const data = res.data || []

    if (data.length === 0) {
      toast.add({
        severity: 'warn',
        summary: 'Informasi',
        detail: 'Nilai siswa belum lengkap.',
        life: 3000
      })
    }

    nilaiList.value = data.map((item) => ({
      nama_mapel: item.nama_mapel || '-',
      nilai_tugas: item.nilai_tugas ?? 0,
      nilai_uts: item.nilai_uts ?? 0,
      nilai_ujian: item.nilai_ujian ?? 0,
      rata_rata: item.rata_rata ?? 0,
      grade: item.grade ?? '-'
    }))
  } catch (err) {
    nilaiList.value = []
    console.error(err)
    toast.add({
      severity: 'error',
      summary: 'Gagal',
      detail: 'Terjadi kesalahan saat mengambil data nilai siswa.',
      life: 3000
    })
  }
}

const cetakRaport = () => {
  if (!selectedSiswaId.value || !selectedTahunPelajaranId.value) {
    toast.add({
      severity: 'warn',
      summary: 'Peringatan',
      detail: 'Silakan pilih siswa dan tahun pelajaran terlebih dahulu.',
      life: 3000
    })
    return
  }

  const siswaId = selectedSiswaId.value
  const tahunId = selectedTahunPelajaranId.value

  const raportUrl = `http://127.0.0.1:8000/api/cetak-raport/${siswaId}/${tahunId}`
  window.open(raportUrl, '_blank')
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
