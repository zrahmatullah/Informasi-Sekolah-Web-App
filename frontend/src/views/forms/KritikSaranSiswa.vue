<template>
  <div class="p-6">
    <Toast />

    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">Kritik dan Saran</h2>
      <Button label="Input Kritik dan Saran" icon="pi pi-plus" @click="showDialog = true" class="mb-4" />

      <!-- Dialog Input -->
      <Dialog v-model:visible="showDialog" header="Form Input Kritik dan Saran" :modal="true" class="w-full md:w-3/4 lg:w-2/3 xl:w-1/2 rounded-md">
        <div class="p-fluid space-y-4">
          <div>
            <label for="tanggal">Tanggal</label>
            <Calendar id="tanggal" v-model="form.tanggal" placeholder="dd/mm/yyyy" dateFormat="dd/mm/yy" showIcon class="w-full" />
          </div>

          <div>
            <label for="judul">Judul</label>
            <InputText id="judul" v-model="form.judul" placeholder="Judul" class="w-full" />
          </div>

          <div>
            <label for="isi">Isi</label>
            <Textarea id="isi" v-model="form.isi" placeholder="Isi" rows="5" class="w-full" />
          </div>

          <div>
            <label>Guru Tujuan (Wali Kelas)</label>
            <InputText :value="guruList[0]?.nama || '-'" readonly class="w-full" />
          </div>

          <div>
            <label>Siswa</label>
            <InputText :value="siswaList[0]?.nama_lengkap_siswa || '-'" readonly class="w-full" />
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <Button label="Batal" severity="secondary" @click="resetForm" class="p-button-outlined" />
            <Button label="Simpan" @click="submitForm" />
          </div>
        </div>
      </Dialog>

      <!-- Dialog Detail -->
      <Dialog v-model:visible="viewDialog" header="Detail Kritik dan Saran" :modal="true" class="w-full md:w-3/4 lg:w-2/3 xl:w-1/2 rounded-md">
        <div class="space-y-2 p-2">
          <p><strong>Judul:</strong> {{ selectedItem?.judul }}</p>
          <p><strong>Isi:</strong> {{ selectedItem?.isi }}</p>
          <p><strong>Tanggal:</strong> {{ selectedItem?.tanggal }}</p>
          <p><strong>Siswa:</strong> {{ selectedItem?.siswa?.nama_lengkap_siswa || '-' }}</p>
          <p><strong>Guru Tujuan:</strong> {{ selectedItem?.gurus?.nama || '-' }}</p>
          <p><strong>Tanggapan:</strong> {{ selectedItem?.tanggapan }}</p>
        </div>
        <template #footer>
          <Button label="Tutup" icon="pi pi-times" @click="viewDialog = false" class="p-button-secondary" />
        </template>
      </Dialog>

      <!-- Tabel Data -->
      <DataTable :value="kritikSaranList" class="mt-6" responsiveLayout="scroll" stripedRows>
        <Column field="no" header="No" style="width: 50px" />
        <Column field="tanggal" header="Tanggal" />
        <Column field="judul" header="Judul" />
        <Column field="isi" header="Kritik" />
        <Column header="Siswa">
          <template #body="slotProps">
            {{ slotProps.data.siswa?.nama_lengkap_siswa || '-' }}
          </template>
        </Column>
        <Column field="tanggapan" header="Tanggapan" />
        <Column header="Guru">
          <template #body="slotProps">
            {{ slotProps.data.guru?.nama || '-' }}
          </template>
        </Column>
        <Column header="Aksi" style="width: 100px">
          <template #body="slotProps">
            <Button label="View" icon="pi pi-eye" size="small" @click="viewDetail(slotProps.data)" class="p-button-text p-button-sm" />
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Calendar from 'primevue/calendar'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

const toast = useToast()
const token = localStorage.getItem('access_token')

const showDialog = ref(false)
const viewDialog = ref(false)
const selectedItem = ref(null)

const form = ref({
  tanggal: null,
  judul: '',
  isi: '',
  guru_id: null,
  siswa_id: null
})

const kritikSaranList = ref([])
const guruList = ref([])
const siswaList = ref([])

const fetchWaliKelas = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/guru/wali-kelas', {
      headers: { Authorization: `Bearer ${token}` }
    });

    console.log('Response wali kelas:', response.data);

    if (!response.data || !response.data.data || !response.data.data.id) {
      throw new Error('Data wali kelas tidak valid');
    }

    guruList.value = [response.data.data];
    form.value.guru_id = response.data.data.id;
  } catch (error) {
    console.error('Error fetchWaliKelas:', error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal ambil wali kelas', life: 3000 });
  }
};

const fetchSiswaLogin = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/siswa/me', {
      headers: { Authorization: `Bearer ${token}` }
    })
    const siswa = response.data.data
    siswaList.value = [{ id: siswa.id, nama_lengkap_siswa: siswa.nama_lengkap_siswa }]
    form.value.siswa_id = siswa.id
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal mengambil data siswa', life: 3000 })
  }
}

const fetchData = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/kritik-saran', {
      headers: { Authorization: `Bearer ${token}` }
    })
    kritikSaranList.value = response.data.data.map((item, index) => ({
      ...item,
      no: index + 1
    }))
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal mengambil data kritik dan saran', life: 3000 })
  }
}

const formatDate = (date) => {
  const d = new Date(date)
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  const year = d.getFullYear()
  return `${year}-${month}-${day}`
}

const submitForm = async () => {
  if (!form.value.tanggal || !form.value.judul || !form.value.isi || !form.value.guru_id) {
    toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Semua field harus diisi.', life: 3000 })
    return
  }

  try {
    const payload = {
      tanggal: formatDate(form.value.tanggal),
      judul: form.value.judul,
      isi: form.value.isi,
      siswa_id: form.value.siswa_id,
      guru_id: form.value.guru_id
    }

    await axios.post('http://127.0.0.1:8000/api/kritik-saran/kirim', payload, {
      headers: { Authorization: `Bearer ${token}` }
    })

    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data berhasil disimpan.', life: 3000 })
    resetForm()
    fetchData()
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal menyimpan data', life: 3000 })
  }
}

const resetForm = () => {
  const siswa_id = form.value.siswa_id
  form.value = {
    tanggal: '',
    judul: '',
    isi: '',
    guru_id: null,
    siswa_id
  }
  showDialog.value = false
}

const viewDetail = (item) => {
  selectedItem.value = item
  viewDialog.value = true
}

onMounted(() => {
  fetchWaliKelas()
  fetchSiswaLogin()
  fetchData()
})
</script>

<style scoped>
.p-dialog .p-dialog-content {
  padding: 1.5rem !important;
}

.p-dialog .p-dialog-header {
  padding: 1rem 1.5rem !important;
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.p-dialog .p-dialog-footer {
  padding: 1rem 1.5rem !important;
  background-color: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.p-fluid > * {
  margin-bottom: 1rem;
}

label {
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 0.25rem;
  display: inline-block;
  color: #374151;
}

input, textarea, .p-dropdown {
  border-radius: 0.5rem;
}
</style>
