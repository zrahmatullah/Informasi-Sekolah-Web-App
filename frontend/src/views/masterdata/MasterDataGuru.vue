<template>
  <div class="p-4 manajemen-guru">
    <h2 class="text-2xl font-bold mb-4">Manajemen Guru</h2>

    <Button label="Tambah Guru" icon="pi pi-plus" class="mb-3" @click="showAddDialog" />

    <DataTable :value="gurus" responsiveLayout="scroll" stripedRows>
      <Column field="nrp_nip" header="NRP/NIP" />
      <Column field="nama" header="Nama" />
      <Column field="email" header="Email" />
      <Column field="nomor_telpon" header="No. Telp" />
      <Column field="jenis_kelamin.jenis_kelamin" header="Jenis Kelamin" />
      <Column header="Aksi">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-sm p-button-text" @click="editGuru(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-sm p-button-text p-button-danger" @click="deleteGuru(slotProps.data.id)" />
        </template>
      </Column>
    </DataTable>

    <Dialog v-model:visible="displayDialog" :header="editMode ? 'Edit Guru' : 'Tambah Guru'" modal :style="{ width: '40vw' }">
      <form @submit.prevent="submitGuru">
        <div class="p-fluid grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="field">
            <label>NRP/NIP</label>
            <InputText v-model="form.nrp_nip" />
          </div>

          <div class="field">
            <label>Nama</label>
            <InputText v-model="form.nama" />
          </div>

          <div class="field">
            <label>Jenis Kelamin</label>
            <Dropdown
              v-model="form.jenis_kelamin_id"
              :options="jenisKelaminList"
              optionLabel="jenis_kelamin"
              optionValue="id"
              placeholder="Pilih Jenis Kelamin"
            />
          </div>

          <div class="field">
            <label>Tempat Lahir</label>
            <InputText v-model="form.tempat_lahir" />
          </div>

          <div class="field">
            <label>Tanggal Lahir</label>
            <Calendar v-model="form.tanggal_lahir" dateFormat="yy-mm-dd" showIcon />
          </div>

          <div class="field">
            <label>Pendidikan</label>
            <InputText v-model="form.pendidikan" />
          </div>

          <div class="field">
            <label>Tahun Masuk</label>
            <Calendar v-model="form.tahun_masuk" dateFormat="yy-mm-dd" showIcon />
          </div>

          <div class="field">
            <label>Alamat</label>
            <InputText v-model="form.alamat" />
          </div>

          <div class="field">
            <label>RT/RW</label>
            <InputText v-model="form.rt_rw" />
          </div>

          <div class="field">
            <label>Dusun</label>
            <InputText v-model="form.dusun" />
          </div>

          <div class="field">
            <label>Kelurahan</label>
            <InputText v-model="form.kelurahan" />
          </div>

          <div class="field">
            <label>Kecamatan</label>
            <InputText v-model="form.kecamatan" />
          </div>

          <div class="field">
            <label>Kode Pos</label>
            <InputText v-model="form.kode_pos" />
          </div>

          <div class="field">
            <label>No. Telpon</label>
            <InputText v-model="form.nomor_telpon" />
          </div>

          <div class="field">
            <label>Email</label>
            <InputText v-model="form.email" />
          </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 justify-end">
          <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="displayDialog = false" />
          <Button label="Simpan" icon="pi pi-check" type="submit" />
        </div>
      </form>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Calendar from 'primevue/calendar'
import DataTable from 'primevue/datatable'
import Dropdown from 'primevue/dropdown'
import axios from 'axios'

const gurus = ref([])
const displayDialog = ref(false)
const editMode = ref(false)
const selectedId = ref(null)

const jenisKelaminList = ref([])

const form = ref({
  nrp_nip: '',
  nama: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  pendidikan: '',
  tahun_masuk: '',
  alamat: '',
  rt_rw: '',
  dusun: '',
  kelurahan: '',
  kecamatan: '',
  kode_pos: '',
  nomor_telpon: '',
  email: '',
  jenis_kelamin_id: null
})

const getData = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/guru')
    gurus.value = res.data
  } catch (error) {
    console.error('Gagal fetch data guru:', error)
  }
}

const fetchJenisKelamin = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/jenis-kelamin')
    jenisKelaminList.value = res.data
  } catch (error) {
    console.error("Gagal fetch jenis kelamin:", error)
  }
}

const showAddDialog = () => {
  resetForm()
  editMode.value = false
  displayDialog.value = true
}

const editGuru = (guru) => {
  Object.assign(form.value, guru)
  selectedId.value = guru.id
  editMode.value = true
  displayDialog.value = true
}

const submitGuru = async () => {
  try {
    if (editMode.value) {
      await axios.put(`http://127.0.0.1:8000/api/guru/${selectedId.value}`, form.value)
    } else {
      await axios.post('http://127.0.0.1:8000/api/guru', form.value)
    }
    await getData()
    displayDialog.value = false
  } catch (err) {
    console.error(err)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

const deleteGuru = async (id) => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/guru/${id}`)
      await getData()
    } catch (error) {
      console.error('Gagal hapus data guru:', error)
    }
  }
}

const resetForm = () => {
  form.value = {
    nrp_nip: '',
    nama: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    pendidikan: '',
    tahun_masuk: '',
    alamat: '',
    rt_rw: '',
    dusun: '',
    kelurahan: '',
    kecamatan: '',
    kode_pos: '',
    nomor_telpon: '',
    email: '',
    jenis_kelamin_id: null
  }
  selectedId.value = null
}

onMounted(() => {
  getData()
  fetchJenisKelamin()
})
</script>

<style scoped>
.field label {
  font-weight: 500;
  margin-bottom: 0.25rem;
  display: block;
}
.manajemen-guru .field {
  margin-bottom: 1rem;
}

.manajemen-guru label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: block;
}

.manajemen-guru input,
.manajemen-guru textarea {
  width: 100%;
}

.manajemen-guru .p-button-text {
  color: #666;
}

.manajemen-guru .p-button-danger {
  color: #fff;
  background-color: #e53935;
  border: none;
}
</style>
