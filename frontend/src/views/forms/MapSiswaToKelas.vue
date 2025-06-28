<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-4">Mapping Siswa ke Kelas</h2>

    <Toast />

    <div class="flex flex-wrap gap-x-6 gap-y-2 mb-4 items-center">
      <div class="flex items-center gap-2">
        <RadioButton name="filter" value="all" v-model="filterOption" inputId="rb1" />
        <label for="rb1" class="cursor-pointer">Semua</label>
      </div>
      <div class="flex items-center gap-2">
        <RadioButton name="filter" value="sudah" v-model="filterOption" inputId="rb2" />
        <label for="rb2" class="cursor-pointer">Sudah Punya Kelas</label>
      </div>
      <div class="flex items-center gap-2">
        <RadioButton name="filter" value="belum" v-model="filterOption" inputId="rb3" />
        <label for="rb3" class="cursor-pointer">Belum Punya Kelas</label>
      </div>
    </div>

    <DataTable
      :value="filteredSiswa"
      :paginator="true"
      :rows="10"
      stripedRows
      responsiveLayout="scroll"
    >
      <Column field="nama_lengkap_siswa" header="Nama Siswa"></Column>
      <Column header="Kelas">
        <template #body="{ data }">
          <Dropdown
            v-model="data.kelas_id"
            :options="kelasList"
            optionLabel="nama_kelas"
            optionValue="id"
            placeholder="Pilih Kelas"
            @change="() => assignKelas(data)"
            class="w-full"
          />
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dropdown from 'primevue/dropdown'
import RadioButton from 'primevue/radiobutton'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const siswaList = ref([])
const kelasList = ref([])
const toast = useToast()
const filterOption = ref('all')

const fetchSiswa = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/siswa')
    siswaList.value = res.data
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal mengambil data siswa' })
  }
}

const fetchKelas = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/kelas')
    kelasList.value = res.data
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal mengambil data kelas' })
  }
}

const assignKelas = async (siswa) => {
  try {
    await axios.post(`http://127.0.0.1:8000/api/siswa/${siswa.id}/assign-kelas`, {
      kelas_id: siswa.kelas_id
    })
    toast.add({ severity: 'success', summary: 'Sukses', detail: `Kelas siswa ${siswa.nama_lengkap_siswa} diperbarui` })
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal mengupdate kelas siswa' })
  }
}

const filteredSiswa = computed(() => {
  if (filterOption.value === 'sudah') {
    return siswaList.value.filter(s => s.kelas_id !== null)
  } else if (filterOption.value === 'belum') {
    return siswaList.value.filter(s => s.kelas_id === null)
  }
  return siswaList.value
})

onMounted(() => {
  fetchSiswa()
  fetchKelas()
})
</script>
