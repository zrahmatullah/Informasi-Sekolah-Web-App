<template>
  <div class="card">
    <h2>Data Siswa</h2>

    <DataTable
      :value="siswa"
      :paginator="true"
      :rows="10"
      :loading="loading"
      responsiveLayout="scroll"
    >
      <Column field="nisn" header="NISN" />
      <Column field="nama_lengkap_siswa" header="Nama" />
      <Column field="kelas.nama_kelas" header="Kelas" />
      <!-- <Column field="jenis_kelamin.jenis_kelamin" header="Jenis Kelamin" /> -->
    </DataTable>

    <Message v-if="siswa.length === 0 && !loading" severity="info">
      Tidak ada data siswa untuk guru ini.
    </Message>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

const token = localStorage.getItem('access_token')
const siswa = ref([])
const loading = ref(false)
const toast = useToast()

const fetchSiswa = async () => {
  loading.value = true
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/guru/data-siswa', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    siswa.value = res.data
  } catch (err) {
    console.error(err.response)
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal mengambil data siswa', life: 3000 })
  } finally {
    loading.value = false
  }
}

onMounted(fetchSiswa)
</script>
