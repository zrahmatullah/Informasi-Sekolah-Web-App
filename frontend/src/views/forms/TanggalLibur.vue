<template>
  <div class="container">
    <h2 class="title">Pengumuman</h2>

    <div class="toolbar">
      <Button label="Tambah Pengumuman" icon="pi pi-plus" @click="showDialog = true" />
    </div>

    <DataTable :value="dataLibur" responsiveLayout="scroll">
      <Column field="tanggal" header="Tanggal" />
      <Column field="judul" header="Judul Pengumuman" />
      <Column field="keterangan" header="Keterangan" />
      <Column header="Aksi">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-sm p-button-warning btn-action" @click="editLibur(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-sm p-button-danger" @click="confirmHapus(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <!-- Dialog Form -->
    <Dialog v-model:visible="showDialog" header="Form Tanggal Libur" modal :style="{ width: '450px' }">
      <div class="form-layout">
        <div class="form-group">
          <label for="judul">Judul</label>
          <Textarea
            id="judul"
            v-model="form.judul"
            rows="3"
            class="input"
            autoResize
          />
        </div>
        <div class="form-group">
          <label for="tanggal">Tanggal Libur</label>
          <Calendar
            id="tanggal"
            v-model="form.tanggal"
            dateFormat="yy-mm-dd"
            showIcon
            class="input"
          />
        </div>
        <div class="form-group">
          <label for="keterangan">Keterangan</label>
          <Textarea
            id="keterangan"
            v-model="form.keterangan"
            rows="3"
            class="input"
            autoResize
          />
        </div>
        <div class="form-footer">
          <Button label="Simpan" icon="pi pi-check" @click="simpanLibur" />
        </div>
      </div>
    </Dialog>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog v-model:visible="showConfirmDialog" header="Konfirmasi" modal :style="{ width: '400px' }">
      <p>Apakah Anda yakin ingin menghapus data Tanggal Libur ini?</p>
      <div class="form-footer">
        <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="showConfirmDialog = false" />
        <Button label="Hapus" icon="pi pi-trash" class="p-button-danger" @click="hapusLibur" />
      </div>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Calendar from 'primevue/calendar'
import Textarea from 'primevue/textarea'
import { useToast } from 'primevue/usetoast'

const toast = useToast()
const dataLibur = ref([])
const showDialog = ref(false)
const showConfirmDialog = ref(false)
const idToDelete = ref(null)

const form = ref({
  id: null,
  tanggal: '',
  judul: '',
  keterangan: '',
})

const loadData = async () => {
  const res = await axios.get('/tanggal-libur')
  dataLibur.value = res.data
}

const simpanLibur = async () => {
  try {
    if (form.value.id) {
      await axios.put(`/tanggal-libur/${form.value.id}`, form.value)
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data diperbarui' })
    } else {
      await axios.post('/tanggal-libur', form.value)
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data ditambahkan' })
    }
    showDialog.value = false
    resetForm()
    loadData()
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Cek isian data' })
  }
}

const editLibur = (data) => {
  form.value = { ...data }
  showDialog.value = true
}

const confirmHapus = (data) => {
  idToDelete.value = data.id
  showConfirmDialog.value = true
}

const hapusLibur = async () => {
  try {
    await axios.delete(`/tanggal-libur/${idToDelete.value}`)
    toast.add({ severity: 'info', summary: 'Dihapus', detail: 'Data dihapus' })
    showConfirmDialog.value = false
    loadData()
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menghapus data' })
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    tanggal: '',
    judul: '',
    keterangan: '',
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.container {
  padding: 20px;
}

.title {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 20px;
}

.toolbar {
  margin-bottom: 16px;
}

.btn-action {
  margin-right: 8px;
}

.form-layout {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 6px;
}

.input {
  width: 100%;
}

.form-footer {
  text-align: right;
  margin-top: 12px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
</style>
