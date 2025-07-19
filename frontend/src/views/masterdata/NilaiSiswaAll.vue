<template>
  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md px-8 py-10 space-y-8">
    <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
      <i class="pi pi-print text-purple-600 text-2xl"></i> Cetak Raport Semua Siswa
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div class="flex flex-col">
        <label class="text-sm font-semibold text-gray-700 mb-2">Kelas (otomatis dari guru login)</label>
        <InputText
          v-model="kelasLogin.nama_kelas"
          class="w-full"
          disabled
        />
      </div>

      <div class="flex flex-col">
        <label class="text-sm font-semibold text-gray-700 mb-2">Pilih Tahun Pelajaran</label>
        <Dropdown
          v-model="selectedTahun"
          :options="tahunPelajaranList"
          optionLabel="tahun_pelajaran"
          optionValue="id"
          placeholder="Pilih Tahun Pelajaran"
          class="w-full"
        />
      </div>
    </div>

    <div class="pt-6 flex justify-end">
      <Button
        label="Cetak Raport Semua"
        icon="pi pi-print"
        @click="cetakRaport"
        :disabled="!kelasLogin.id || !selectedTahun"
        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md border-none shadow-md"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

const kelasLogin = ref({})
const selectedTahun = ref(null)
const tahunPelajaranList = ref([])

const fetchKelasLogin = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/guru/kelas-guru', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
    })
    kelasLogin.value = Array.isArray(res.data) ? res.data[0] : res.data
  } catch (err) {
    console.error('Gagal mengambil kelas login:', err)
    alert('Gagal mengambil data kelas.')
  }
}

const fetchTahunPelajaran = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/tahun-pelajaran')
    tahunPelajaranList.value = res.data
  } catch (err) {
    console.error('Gagal mengambil tahun pelajaran:', err)
    alert('Gagal mengambil tahun pelajaran.')
  }
}

const cetakRaport = async () => {
  try {
    const res = await axios.post(
      'http://127.0.0.1:8000/api/raport/cetak-semua',
      {
        kelas_id: kelasLogin.value.id,
        tahun_pelajaran_id: selectedTahun.value
      },
      {
        responseType: 'blob',
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          Accept: 'application/pdf'
        },
        withCredentials: true 
      }
    )

    const blob = new Blob([res.data], { type: 'application/pdf' })
    const url = window.URL.createObjectURL(blob)
    window.open(url, '_blank')
  } catch (error) {
    console.error('Gagal mencetak raport:', error)
    alert('Terjadi kesalahan saat mencetak raport.')
  }
}


onMounted(() => {
  fetchKelasLogin()
  fetchTahunPelajaran()
})
</script>
<style scoped>
label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.5rem;
}
.p-inputtext, .p-dropdown {
  padding: 0.75rem;
  border-radius: 0.5rem;
  width: 100%;
}
</style>
