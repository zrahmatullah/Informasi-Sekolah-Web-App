<template>
  <div class="p-4">
    <h2 class="text-xl font-semibold mb-4">Master Jadwal Pelajaran</h2>

    <div class="flex justify-end mb-3">
      <Button label="Tambah Jadwal" icon="pi pi-plus" @click="openDialog" />
    </div>

    <DataTable :value="jadwalList" :loading="loading" paginator :rows="10">
      <Column field="id" header="Id" />
      <Column field="kelas.nama_kelas" header="Kelas" />
      <Column field="mata_pelajaran.nama_mapel" header="Mata Pelajaran" />
      <Column field="guru.nama" header="Guru" />
      <Column field="hari" header="Hari" />
      <Column field="jam_mulai" header="Jam Mulai" />
      <Column field="jam_selesai" header="Jam Selesai" />
      <Column header="Aksi">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-text p-button-sm" @click="editJadwal(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-text p-button-sm text-red-500" @click="deleteJadwal(slotProps.data.id)" />
        </template>
      </Column>
    </DataTable>

    <Dialog v-model:visible="showDialog" modal header="Form Jadwal" :style="{ width: '600px' }">
      <form @submit.prevent="submitForm" class="p-fluid">
        <div class="grid gap-3">
          <div class="col-12">
            <label for="kelas" class="block text-sm font-medium mb-1">Kelas</label>
            <Dropdown
              v-model="form.kelas_id"
              :options="kelasList"
              optionLabel="nama_kelas"
              optionValue="id"
              placeholder="Pilih Kelas"
              class="w-full"
            />
          </div>

          <div class="col-12">
            <label for="mapel" class="block text-sm font-medium mb-1">Mata Pelajaran</label>
            <Dropdown
              v-model="form.mata_pelajaran_id"
              :options="mapelList"
              optionLabel="nama_mapel"
              optionValue="id"
              placeholder="Pilih Mapel"
              class="w-full"
            />
          </div>

          <div class="col-12">
            <label for="guru" class="block text-sm font-medium mb-1">Guru</label>
            <Dropdown
              v-model="form.guru_id"
              :options="guruList"
              optionLabel="nama"
              optionValue="id"
              placeholder="Pilih Guru"
              class="w-full"
            />
          </div>

          <div class="col-12">
            <label for="hari" class="block text-sm font-medium mb-1">Hari</label>
            <Dropdown
              v-model="form.hari"
              :options="hariOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Pilih Hari"
              class="w-full"
            />
          </div>

          <div class="col-12 md:col-6">
            <label for="jam_mulai" class="block text-sm font-medium mb-1">Jam Mulai</label>
            <InputText v-model="form.jam_mulai" type="time" class="w-full" />
          </div>

          <div class="col-12 md:col-6">
            <label for="jam_selesai" class="block text-sm font-medium mb-1">Jam Selesai</label>
            <InputText v-model="form.jam_selesai" type="time" class="w-full" />
          </div>
        </div>

        <div class="flex justify-end gap-2 mt-5">
          <Button label="Simpan" icon="pi pi-check" type="submit" />
          <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="showDialog = false" />
        </div>
      </form>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

const toast = useToast()

const jadwalList = ref([])
const loading = ref(false)
const showDialog = ref(false)
const isEdit = ref(false)

const form = ref({
  id: null,
  kelas_id: null,
  mata_pelajaran_id: null,
  guru_id: null,
  hari: '',
  jam_mulai: '',
  jam_selesai: ''
})

const kelasList = ref([])
const mapelList = ref([])
const guruList = ref([])

const hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'].map(h => ({ label: h, value: h }))

const fetchData = async () => {
  loading.value = true
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/jadwal-pelajaran')
    jadwalList.value = res.data.data
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat data jadwal' })
  } finally {
    loading.value = false
  }
}

const fetchDropdowns = async () => {
  try {
    const [kelasRes, mapelRes, guruRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/kelas'),
      axios.get('http://127.0.0.1:8000/api/matapelajaran'),
      axios.get('http://127.0.0.1:8000/api/guru')
    ])

    console.log('Kelas:', kelasRes.data)
    console.log('Mapel:', mapelRes.data)
    console.log('Guru:', guruRes.data)

    console.log('Kelas:', kelasRes.data.data?.length, kelasRes.data.data)
    console.log('Mapel:', mapelRes.data.data?.length, mapelRes.data.data)
    console.log('Guru:', guruRes.data.data?.length, guruRes.data.data)

    kelasList.value = kelasRes.data
    mapelList.value = mapelRes.data
    guruList.value = guruRes.data
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat dropdown' })
  }
}

const openDialog = () => {
  isEdit.value = false
  resetForm()
  showDialog.value = true
}

const editJadwal = (data) => {
  isEdit.value = true
  form.value = {
    id: data.id,
    kelas_id: data.kelas_id,
    mata_pelajaran_id: data.mata_pelajaran_id,
    guru_id: data.guru_id,
    hari: data.hari,
    jam_mulai: data.jam_mulai,
    jam_selesai: data.jam_selesai
  }
  showDialog.value = true
}

const resetForm = () => {
  form.value = {
    id: null,
    kelas_id: null,
    mata_pelajaran_id: null,
    guru_id: null,
    hari: '',
    jam_mulai: '',
    jam_selesai: ''
  }
}

const submitForm = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`http://127.0.0.1:8000/api/jadwal-pelajaran/${form.value.id}`, form.value)
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Jadwal diperbarui' })
    } else {
      await axios.post('http://127.0.0.1:8000/api/jadwal-pelajaran', form.value)
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Jadwal ditambahkan' })
    }
    fetchData()
    showDialog.value = false
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menyimpan jadwal' })
  }
}

const deleteJadwal = async (id) => {
  if (confirm('Yakin ingin menghapus jadwal ini?')) {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/jadwal-pelajaran/${id}`)
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Jadwal dihapus' })
      fetchData()
    } catch (error) {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menghapus jadwal' })
    }
  }
}

onMounted(() => {
  fetchDropdowns()
  fetchData()
})
</script>

<style scoped>
.mb-3 label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}
</style>
