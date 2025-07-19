<template>
  <div class="container">
    <div class="header">
      <h2>Kirim Surat</h2>
      <Button label="Upload Surat" icon="pi pi-upload" @click="showUploadDialog = true" />
    </div>

    <div class="filters">
      <!-- <label for="kelas">Kelas :</label> -->
      <Dropdown
        id="kelas"
        v-model="selectedKelas"
        :options="kelasList"
        optionLabel="nama_kelas"
        optionValue="id"
        placeholder="Pilih Kelas"
        @change="onKelasChange"
        class="w-full"
      />
    </div>

    <div v-if="uploadedFileName" class="mb-2 text-sm text-green-600">
      File siap dikirim: <strong>{{ uploadedFileName }}</strong>
    </div>

    <DataTable
      v-if="siswaList.length"
      :value="siswaList"
      paginator
      :rows="10"
      :rowsPerPageOptions="[10, 20, 50]"
      v-model:selection="selectedSiswa"
      selectionMode="multiple"
      dataKey="id"
      responsiveLayout="scroll"
      stripedRows
    >
      <Column selectionMode="multiple" headerStyle="width: 3rem" />
      <Column field="nis" header="NIS" />
      <Column field="nama_lengkap_siswa" header="Nama Lengkap" />
      <!-- <Column field="kelas.nama_kelas" header="Kelas" /> -->
      <Column header="Aksi">
        <template #body="slotProps">
          <Button
            label="Kirim Surat"
            icon="pi pi-send"
            class="p-button-sm"
            @click="kirimEmail(slotProps.data)"
            :disabled="loading"
          />
        </template>
      </Column>
    </DataTable>

    <div class="actions mt-3" v-if="selectedSiswa.length">
      <Button
        label="Kirim ke yang dipilih"
        icon="pi pi-envelope"
        class="p-button-success"
        @click="kirimBatch"
        :loading="loading"
      />
    </div>

    <Dialog v-model:visible="showUploadDialog" header="Upload Surat" modal :style="{ width: '700px' }">
    <div class="p-fluid p-4">
      <div class="field mb-4">
        <label for="judul" class="font-semibold mb-2 block">Judul Surat:</label>
        <input
          id="judul"
          type="text"
          v-model="judul"
          placeholder="Masukkan judul surat"
          class="p-inputtext w-full"
        />
      </div>

      <div class="field mb-4">
        <label class="font-semibold mb-2 block">Upload File Surat:</label>
        <FileUpload
          name="file"
          accept=".pdf,.doc,.docx"
          :auto="true"
          mode="advanced"
          :customUpload="true"
          chooseLabel="Pilih Surat"
          uploadLabel="Upload"
          cancelLabel="Batal"
          @uploader="uploadSuratWithPrimevue"
          class="w-full"
        />
      </div>

      <div v-if="uploadedFileName" class="text-green-700 text-sm mt-2">
        File berhasil diupload: <strong>{{ uploadedFileName }}</strong>
      </div>
    </div>
  </Dialog>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import FileUpload from 'primevue/fileupload'

const toast = useToast()

const kelasList = ref([])
const selectedKelas = ref(null)
const siswaList = ref([])
const selectedSiswa = ref([])

const showUploadDialog = ref(false)
const uploadedFileName = ref('')
const uploadedFilePath = ref('')
const judul = ref('')

const loading = ref(false)

const getKelasList = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/kelas')
    kelasList.value = res.data
  } catch {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal memuat data kelas',life: 3000 })
  }
}

const getAllSiswaByKelas = async (kelasId) => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/siswa/kelas/${kelasId}`)
    siswaList.value = res.data
    selectedSiswa.value = []
  } catch {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal mengambil data siswa',life: 3000 })
  }
}

const onKelasChange = () => {
  if (selectedKelas.value) {
    getAllSiswaByKelas(selectedKelas.value)
  }
}

const uploadSuratWithPrimevue = async (event) => {
  const file = event.files?.[0] || event.originalEvent?.target?.files?.[0];
  if (!file) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'File tidak ditemukan', life: 3000 });
    return;
  }

  const formData = new FormData();
  formData.append('file', file);

  try {
    const res = await axios.post('http://127.0.0.1:8000/api/surat-edaran/upload', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    uploadedFilePath.value = res.data.path;
    uploadedFileName.value = file.name;
    toast.add({ severity: 'success', summary: 'Sukses', detail: 'File berhasil diupload', life: 3000 });
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Gagal Upload',
      detail: error.response?.data?.errors?.file?.[0] || 'Terjadi kesalahan saat upload',
    });
  }
};


const validasiSurat = () => {
  if (!judul.value || !selectedKelas.value) {
    toast.add({ severity: 'warn', summary: 'Lengkapi Data', detail: 'Judul dan kelas harus diisi',life: 3000 })
    return false
  }

  if (!uploadedFilePath.value) {
    toast.add({ severity: 'warn', summary: 'Belum Upload Surat', detail: 'Silakan upload surat terlebih dahulu',life: 3000 })
    return false
  }

  return true
}

const kirimEmail = async (siswa) => {
  if (!validasiSurat()) return

  loading.value = true
  try {
    await axios.post('http://127.0.0.1:8000/api/surat-edaran/kirim', {
      siswa_id: siswa.id,
      file: uploadedFilePath.value,
      judul: judul.value,
      kelas_id: selectedKelas.value
    })
    toast.add({ severity: 'success', summary: 'Terkirim', detail: `Surat dikirim ke ${siswa.nama_lengkap_siswa}`,life: 3000 })
  } catch {
    toast.add({ severity: 'error', summary: 'Gagal', detail: `Gagal kirim ke ${siswa.nama_lengkap_siswa}`,life: 3000 })
  } finally {
    loading.value = false
  }
}

const kirimBatch = async () => {
  if (!validasiSurat()) return
  if (!selectedSiswa.value.length) {
    toast.add({ severity: 'warn', summary: 'Tidak ada siswa', detail: 'Silakan pilih minimal satu siswa',life: 3000 })
    return
  }

  loading.value = true
  try {
    await Promise.all(
      selectedSiswa.value.map((siswa) =>
        axios.post('http://127.0.0.1:8000/api/surat-edaran/kirim', {
          siswa_id: siswa.id,
          file: uploadedFilePath.value,
          judul: judul.value,
          kelas_id: selectedKelas.value
        })
      )
    )
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Surat berhasil dikirim ke semua siswa',life: 3000 })
    judul.value = ''
    uploadedFileName.value = ''
    uploadedFilePath.value = ''
    selectedSiswa.value = []
    showUploadDialog.value = false
  } catch {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Sebagian pengiriman gagal',life: 3000 })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  getKelasList()
})
</script>

<style scoped>
.container {
  padding: 20px;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.filters {
  margin-bottom: 20px;
}
.actions {
  text-align: right;
}
</style>
