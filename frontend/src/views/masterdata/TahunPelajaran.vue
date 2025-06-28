<template>
  <div class="p-4">
    <h2 class="text-xl font-semibold mb-4">Tahun Pelajaran</h2>

    <div class="mb-4 flex gap-2">
      <Button label="Tambah" icon="pi pi-plus" @click="openAddDialog" />
    </div>

    <DataTable :value="listTahun" :paginator="true" :rows="5" responsiveLayout="scroll">
      <Column field="id" header="ID"></Column>
      <Column field="tahun_pelajaran" header="Tahun Pelajaran"></Column>
      <Column header="Aksi">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" class="p-button-text p-button-sm" @click="openEditDialog(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-text p-button-sm p-button-danger" @click="confirmDelete(slotProps.data.id)" />
        </template>
      </Column>
    </DataTable>

    <Dialog v-model:visible="dialogVisible" :header="isEdit ? 'Edit' : 'Tambah'" :modal="true" :closable="false">
      <div class="p-fluid">
        <div class="field">
          <label for="tahun">Tahun Pelajaran</label>
          <InputText id="tahun" v-model="form.tahun_pelajaran" />
        </div>
      </div>
      <template #footer>
        <Button label="Batal" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" />
        <Button label="Simpan" icon="pi pi-check" @click="submitForm" />
      </template>
    </Dialog>

    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import axios from 'axios'

const listTahun = ref([])
const dialogVisible = ref(false)
const isEdit = ref(false)
const form = ref({ id: null, tahun_pelajaran: '' })

const confirm = useConfirm()
const toast = useToast()

const fetchData = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/tahun-pelajaran')
    listTahun.value = response.data
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal memuat data', life : 3000 })
  }
}

const openAddDialog = () => {
  form.value = { id: null, tahun_pelajaran: '' }
  isEdit.value = false
  dialogVisible.value = true
}

const openEditDialog = (item) => {
  form.value = { ...item }
  isEdit.value = true
  dialogVisible.value = true
}

const submitForm = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`http://127.0.0.1:8000/api/tahun-pelajaran/${form.value.id}`, {
        tahun_pelajaran: form.value.tahun_pelajaran
      })
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data berhasil diupdate' ,life : 3000})
    } else {
      await axios.post('http://127.0.0.1:8000/api/tahun-pelajaran', {
        tahun_pelajaran: form.value.tahun_pelajaran
      })
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data berhasil ditambahkan',life : 3000 })
    }
    dialogVisible.value = false
    fetchData()
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menyimpan data',life : 3000 })
  }
}

const confirmDelete = (id) => {
  confirm.require({
    message: 'Apakah yakin ingin menghapus?',
    header: 'Konfirmasi',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      try {
        await axios.delete(`http://127.0.0.1:8000/api/tahun-pelajaran/${id}`)
        toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data dihapus' })
        fetchData()
      } catch (error) {
        toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menghapus data' })
      }
    }
  })
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.field {
  margin-bottom: 1rem;
}
</style>
