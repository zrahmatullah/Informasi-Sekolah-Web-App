<template>
  <div class="card">
    <h3>Input Nilai Siswa Per Kelas</h3>

    <div class="p-fluid p-formgrid p-grid">
      <div class="p-field p-col-12 p-md-4">
        <label>Kelas</label>
        <Dropdown
          v-model="selectedKelasId"
          :options="kelasList"
          optionLabel="kode_kelas"
          optionValue="id"
          placeholder="Pilih Kelas"
          @change="checkAndFetchNilai"
          :disabled="true"
        />
      </div>

      <div class="p-field p-col-12 p-md-4">
        <label>Mata Pelajaran</label>
        <Dropdown
          v-model="selectedMapelId"
          :options="mapelList"
          optionLabel="nama_mapel"
          optionValue="id"
          placeholder="Pilih Mata Pelajaran"
          @change="checkAndFetchNilai"
        />
      </div>

      <div class="p-field p-col-12 p-md-4">
        <label>Tahun Pelajaran</label>
        <Dropdown
          v-model="selectedTahunPelajaranId"
          :options="tahunPelajaranList"
          optionLabel="tahun_pelajaran"
          optionValue="id"
          placeholder="Pilih Tahun Pelajaran"
          @change="checkAndFetchNilai"
        />
      </div>
    </div>

    <div v-if="selectedKelasId && selectedMapelId && selectedTahunPelajaranId" class="mt-4">
      <DataTable :value="nilaiList" dataKey="siswa_id" responsiveLayout="scroll">
        <Column header="Tahun Pelajaran">
          <template #body="{ data }">
            {{ getTahunPelajaranLabel(data.tahun_pelajaran_id) }}
          </template>
        </Column>
        <Column field="nama_lengkap_siswa" header="Siswa" />
        <Column field="nilai_tugas" header="Tugas" />
        <Column field="nilai_uts" header="UTS" />
        <Column field="nilai_ujian" header="Ujian" />
        <Column field="rata_rata" header="Rata-rata" />
        <Column field="grade" header="Grade" />
        <Column header="Status Nilai" style="width: 150px">
          <template #body="{ data }">
            <span
              class="p-tag"
              :class="{
                'p-tag-success': data.nilai_tugas !== null && data.nilai_uts !== null && data.nilai_ujian !== null,
                'p-tag-warning': data.nilai_tugas === null || data.nilai_uts === null || data.nilai_ujian === null,
              }"
            >
              {{
                data.nilai_tugas !== null && data.nilai_uts !== null && data.nilai_ujian !== null
                  ? 'Sudah Dinilai'
                  : 'Belum Dinilai'
              }}
            </span>
          </template>
        </Column>
        <Column header="Aksi" style="width: 150px">
          <template #body="{ data }">
            <Button
              :label="data.nilai_tugas !== null ? 'Edit Nilai' : 'Input Nilai'"
              icon="pi pi-pencil"
              class="p-button-sm"
              :class="data.nilai_tugas !== null ? 'p-button-warning' : 'p-button-info'"
              @click="showInputDialog(data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <div v-else class="mt-4">
      <p class="text-danger">
        Silakan pilih <strong>Kelas</strong>, <strong>Mata Pelajaran</strong>, dan <strong>Tahun Pelajaran</strong>.
      </p>
    </div>

    <Dialog v-model:visible="dialogVisible" header="Input Nilai Siswa" modal :closable="true">
      <div class="p-fluid">
        <div class="p-field">
          <label>Nama Siswa</label>
          <InputText v-model="selectedSiswa.nama_lengkap_siswa" disabled />
        </div>
        <div class="p-field">
          <label>Nilai Tugas</label>
          <InputNumber v-model="selectedSiswa.nilai_tugas" :min="0" :max="100" />
        </div>
        <div class="p-field">
          <label>Nilai UTS</label>
          <InputNumber v-model="selectedSiswa.nilai_uts" :min="0" :max="100" />
        </div>
        <div class="p-field">
          <label>Nilai Ujian</label>
          <InputNumber v-model="selectedSiswa.nilai_ujian" :min="0" :max="100" />
        </div>
      </div>
      <template #footer>
        <Button label="Batal" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" />
        <Button label="Simpan" icon="pi pi-check" @click="simpanNilaiDialog" autofocus />
      </template>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Dropdown from 'primevue/dropdown'
import InputNumber from 'primevue/inputnumber'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import { useToast } from 'primevue/usetoast'

const toast = useToast()

const selectedKelasId = ref(null)
const selectedMapelId = ref(null)
const selectedTahunPelajaranId = ref(null)

const kelasList = ref([])
const mapelList = ref([])
const tahunPelajaranList = ref([])
const nilaiList = ref([])

const dialogVisible = ref(false)
const selectedSiswa = ref({})

onMounted(async () => {
  await fetchDropdownData()
})

const fetchDropdownData = async () => {
  try {
    const [kelasRes, mapelRes, tahunPelajaranRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/guru/kelas-guru'),
      axios.get('http://127.0.0.1:8000/api/matapelajaran'),
      axios.get('http://127.0.0.1:8000/api/tahun-pelajaran'),
    ])
    kelasList.value = kelasRes.data
    mapelList.value = mapelRes.data
    tahunPelajaranList.value = tahunPelajaranRes.data

    if (kelasList.value.length === 1) {
      selectedKelasId.value = kelasList.value[0].id
    }
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal memuat data dropdown.', life:3000 })
  }
}

const checkAndFetchNilai = async () => {
  if (!selectedKelasId.value || !selectedMapelId.value || !selectedTahunPelajaranId.value) return

  try {
    const res = await axios.get(
      `http://127.0.0.1:8000/api/nilai/kelas/${selectedKelasId.value}/matapelajaran/${selectedMapelId.value}/tahun/${selectedTahunPelajaranId.value}`
    )

    if (res.data && res.data.length > 0) {
      nilaiList.value = res.data.map(item => ({
        ...item,
        id: item.id ?? null
      }))
    } else {
      const siswaRes = await axios.get(`http://127.0.0.1:8000/api/siswa/kelas/${selectedKelasId.value}`)
      nilaiList.value = siswaRes.data.map(siswa => ({
        id: null,
        siswa_id: siswa.id,
        nama_lengkap_siswa: siswa.nama_lengkap_siswa,
        mata_pelajaran_id: selectedMapelId.value,
        kelas_id: selectedKelasId.value,
        tahun_pelajaran_id: selectedTahunPelajaranId.value,
        nilai_tugas: null,
        nilai_uts: null,
        nilai_ujian: null,
        rata_rata: null,
        grade: null,
      }))
    }
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal mengambil data nilai/siswa.' , life:3000})
  }
}

const getTahunPelajaranLabel = (id) => {
  const tahun = tahunPelajaranList.value.find(t => t.id == id)
  return tahun ? tahun.tahun_pelajaran : '-'
}

const showInputDialog = (siswa) => {
  selectedSiswa.value = { ...siswa }
  dialogVisible.value = true
}

const hitungRataRata = (t, u, uj) => {
  return ((Number(t) + Number(u) + Number(uj)) / 3).toFixed(2)
}

const tentukanGrade = (rata) => {
  if (rata >= 90) return 'A'
  if (rata >= 80) return 'B'
  if (rata >= 70) return 'C'
  if (rata >= 60) return 'D'
  return 'E'
}

const simpanNilaiDialog = async () => {
  const siswa = { ...selectedSiswa.value }

  if (siswa.nilai_tugas == null || siswa.nilai_uts == null || siswa.nilai_ujian == null) {
    toast.add({ severity: 'warn', summary: 'Lengkapi Nilai', detail: 'Nilai belum lengkap!' , life:3000})
    return
  }

  siswa.mata_pelajaran_id = selectedMapelId.value
  siswa.kelas_id = selectedKelasId.value
  siswa.tahun_pelajaran_id = selectedTahunPelajaranId.value
  siswa.rata_rata = parseFloat(hitungRataRata(siswa.nilai_tugas, siswa.nilai_uts, siswa.nilai_ujian))
  siswa.grade = tentukanGrade(siswa.rata_rata)

  const isEdit = !!siswa.id && typeof siswa.id === 'number'

  try {
    if (isEdit) {
      await axios.put(`http://127.0.0.1:8000/api/nilai`, siswa)
    } else {
      delete siswa.id
      await axios.post(`http://127.0.0.1:8000/api/nilai`, siswa)
    }

    const index = nilaiList.value.findIndex(n => n.siswa_id === siswa.siswa_id)
    if (index !== -1) {
      nilaiList.value[index] = { ...siswa }
    }

    toast.add({ severity: 'success', summary: 'Berhasil', detail: isEdit ? 'Nilai diperbarui.' : 'Nilai disimpan.' , life:1500})
    dialogVisible.value = false
  } catch (err) {
    const errorMessage = err.response?.data?.message || 'Gagal menyimpan nilai.'
    toast.add({ severity: 'error', summary: 'Error', detail: errorMessage })
  }
}
</script>

<style scoped>
.mt-4 {
  margin-top: 1.5rem;
}
.text-danger {
  color: red;
}
.p-tag-success {
  background-color: #28a745;
  color: white;
}
.p-tag-warning {
  background-color: #ffc107;
  color: black;
}
</style>
