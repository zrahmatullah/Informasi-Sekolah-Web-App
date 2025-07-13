<template>
  <div class="p-4">
    <Toast />
    <h2 class="text-xl font-bold mb-3">Tanggapan Kritik dan Saran</h2>

    <!-- <Button label="Cetak Semua" icon="pi pi-print" class="mb-3" @click="showCetakDialog = true" /> -->

    <!-- Dialog Cetak -->
    <!-- <Dialog v-model:visible="showCetakDialog" header="Cetak Kritik dan Saran" :modal="true" class="w-full md:w-1/3 rounded-lg">
      <div class="p-3 space-y-4">
        <div>
          <label for="rangeTanggal" class="block text-sm font-medium text-gray-700 mb-1">Pilih Rentang Tanggal</label>
          <Calendar
            id="rangeTanggal"
            v-model="rangeTanggal"
            selectionMode="range"
            dateFormat="dd/mm/yy"
            class="w-full"
            showIcon
            placeholder="Pilih tanggal awal dan akhir"
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2 px-3 pb-3">
          <Button label="Batal" severity="secondary" class="p-button-outlined" @click="showCetakDialog = false" />
          <Button label="Cetak" icon="pi pi-print" @click="submitCetak" />
        </div>
      </template>
    </Dialog> -->

    <!-- Dialog Tanggapan -->
    <Dialog v-model:visible="showDialog" header="Tanggapi Kritik dan Saran" :modal="true" class="w-full md:w-1/2 rounded-lg">
      <div class="p-3 space-y-3">
        <div>
          <label class="text-sm font-medium text-gray-700">Judul</label>
          <p class="bg-gray-100 rounded px-3 py-2 mt-1">{{ selectedItem?.judul }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-700">Isi</label>
          <p class="bg-gray-100 rounded px-3 py-2 mt-1 whitespace-pre-wrap">{{ selectedItem?.isi }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-700">Tanggapan</label>
          <Textarea
            v-model="tanggapan"
            rows="5"
            class="w-full mt-1"
            placeholder="Tuliskan tanggapan Anda..."
            autoResize
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2 px-3 pb-3">
          <Button label="Batal" severity="secondary" class="p-button-outlined" @click="closeDialog" />
          <Button label="Kirim Tanggapan" icon="pi pi-check" @click="submitTanggapan" />
        </div>
      </template>
    </Dialog>

    <!-- Tabel -->
    <DataTable :value="kritikSaranList" class="mt-4" responsiveLayout="scroll" stripedRows>
      <Column field="no" header="No" style="width: 50px" />
      <Column field="tanggal" header="Tanggal" />
      <Column field="judul" header="Judul" />
      <Column field="isi" header="Isi" />
      <Column header="Siswa">
        <template #body="slotProps">
          {{ slotProps.data.siswa?.nama_lengkap_siswa || '-' }}
        </template>
      </Column>
      <Column header="Tanggapan">
        <template #body="slotProps">
          {{ slotProps.data.tanggapan || '-' }}
        </template>
      </Column>
      <Column header="Aksi">
        <template #body="slotProps">
          <Button
            label="Tanggapi"
            icon="pi pi-reply"
            size="small"
            @click="openDialog(slotProps.data)"
            :disabled="!!slotProps.data.tanggapan"
          />
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Textarea from 'primevue/textarea'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Calendar from 'primevue/calendar'

const toast = useToast()
const token = localStorage.getItem('access_token')

const showDialog = ref(false)
const showCetakDialog = ref(false)
const selectedItem = ref(null)
const tanggapan = ref('')
const rangeTanggal = ref(null)

const kritikSaranList = ref([])

const fetchKritikSaran = async () => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/kritik-saran/guru`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    kritikSaranList.value = res.data.data.map((item, index) => ({
      ...item,
      no: index + 1
    }))
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal ambil data kritik dan saran', life: 3000 })
  }
}

const openDialog = (item) => {
  selectedItem.value = item
  tanggapan.value = item.tanggapan || ''
  showDialog.value = true
}

const closeDialog = () => {
  showDialog.value = false
  selectedItem.value = null
  tanggapan.value = ''
}

const submitTanggapan = async () => {
  if (!tanggapan.value) {
    toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Tanggapan tidak boleh kosong.', life: 3000 })
    return
  }

  try {
    await axios.post(
      `http://127.0.0.1:8000/api/kritik-saran/${selectedItem.value.id}/tanggapan`,
      { tanggapan: tanggapan.value },
      { headers: { Authorization: `Bearer ${token}` } }
    )
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Tanggapan berhasil dikirim.', life: 3000 })
    closeDialog()
    fetchKritikSaran()
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Gagal mengirim tanggapan', life: 3000 })
  }
}

const submitCetak = () => {
  if (!rangeTanggal.value || rangeTanggal.value.length !== 2) {
    toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Harap pilih rentang tanggal', life: 3000 })
    return
  }

  const [startDate, endDate] = rangeTanggal.value
  const format = (date) => {
    const d = new Date(date)
    const yyyy = d.getFullYear()
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const dd = String(d.getDate()).padStart(2, '0')
    return `${yyyy}-${mm}-${dd}`
  }

  const start = format(startDate)
  const end = format(endDate)
  const username = localStorage.getItem('username') || 'Tidak diketahui'
  const token = localStorage.getItem('access_token')

  const url = `http://127.0.0.1:8000/api/cetak/kritik-saran?tanggal_awal=${start}&tanggal_akhir=${end}&username=${encodeURIComponent(username)}&token=${token}`

  window.open(url, '_blank')
  showCetakDialog.value = false
}

onMounted(() => {
  fetchKritikSaran()
})
</script>
