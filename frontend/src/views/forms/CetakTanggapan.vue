<template>
  <div class="p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto">
    <Toast />

    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">
      Cetak Kritik & Saran
    </h2>

    <div class="space-y-4">
      <div>
        <label for="rangeTanggal" class="block text-sm font-medium text-gray-700 mb-1">
          Pilih Rentang Tanggal
        </label>
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

      <div class="flex justify-end pt-2">
        <Button
          label="Cetak PDF"
          icon="pi pi-print"
          class="p-button p-button-primary px-5 py-2"
          @click="submitCetak"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Calendar from 'primevue/calendar'
import Button from 'primevue/button'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const toast = useToast()
const rangeTanggal = ref(null)

const submitCetak = () => {
  if (!rangeTanggal.value || rangeTanggal.value.length !== 2) {
    toast.add({ severity: 'warn', summary: 'Peringatan', detail: 'Pilih tanggal awal dan akhir', life: 3000 })
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

  const tanggal_awal = format(startDate)
  const tanggal_akhir = format(endDate)
  const username = localStorage.getItem('username') || 'Tidak diketahui'
  const token = localStorage.getItem('access_token')

  const url = `http://127.0.0.1:8000/api/cetak/kritik-saran?tanggal_awal=${tanggal_awal}&tanggal_akhir=${tanggal_akhir}&username=${encodeURIComponent(username)}&token=${token}`

  window.open(url, '_blank')
}
</script>
