<template>
  <div class="p-4">
    <Card>
      <template #title>Profil Sekolah</template>
      <template #content>
        <form @submit.prevent="submitForm" class="grid gap-4">
          <div class="field">
            <label class="block mb-2 font-semibold">Nama Sekolah</label>
            <InputText v-model="form.nama_sekolah" class="w-full" />
          </div>

          <div class="field">
            <label class="block mb-2 font-semibold">Alamat</label>
            <InputText v-model="form.alamat" class="w-full" />
          </div>

          <div class="field">
            <label class="block mb-2 font-semibold">No Telepon</label>
            <InputText v-model="form.no_telp" class="w-full" />
          </div>

          <div class="field">
            <label class="block mb-2 font-semibold">Visi</label>
            <Textarea v-model="form.visi" class="w-full" rows="3" autoResize />
          </div>

          <div class="field">
            <label class="block mb-2 font-semibold">Misi</label>
            <Textarea v-model="form.misi" class="w-full" rows="3" autoResize />
          </div>

          <div class="field">
            <label class="block mb-2 font-semibold">Kepala Sekolah</label>
            <Dropdown
              v-model="form.kepala_sekolah_id"
              :options="guruOptions"
              optionLabel="nama"
              optionValue="id"
              placeholder="Pilih Kepala Sekolah"
              class="w-full"
            />
          </div>

          <Divider />
        <div class="field">
            <label class="block mb-2 font-semibold">Foto Sekolah</label>
            <FileUpload
                name="foto"
                mode="basic"
                accept="image/*"
                customUpload
                :auto="true"
                @uploader="handleFileUpload"
                chooseLabel="Pilih Foto"
                class="w-full"
            />
            <div v-if="formPreview" class="mt-4">
                <img
                :src="formPreview"
                alt="Preview Foto Sekolah"
                class="rounded-lg shadow-md border border-gray-200 max-w-full h-auto"
                />
            </div>
        </div>

          <div class="mt-4">
            <Button label="Simpan" icon="pi pi-save" type="submit" class="w-full" />
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Divider from 'primevue/divider'
import FileUpload from 'primevue/fileupload'

const form = ref({
  nama_sekolah: '',
  alamat: '',
  no_telp: '',
  visi: '',
  misi: '',
  kepala_sekolah_id: '',
  foto: null,
})

const formPreview = ref(null)
const guruOptions = ref([])
const existingId = ref(null)

const fetchProfil = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/profil-sekolah')
    if (res.data) {
      form.value = {
        nama_sekolah: res.data.nama_sekolah,
        alamat: res.data.alamat,
        no_telp: res.data.no_telp,
        visi: res.data.visi,
        misi: res.data.misi,
        kepala_sekolah_id: res.data.kepala_sekolah_id,
        foto: null,
      }
      existingId.value = res.data.id
      if (res.data.foto) {
        formPreview.value = `/storage/${res.data.foto}`
      }
    }
  } catch (err) {
    console.log('Belum ada data profil sekolah')
  }
}

const fetchGurus = async () => {
  const res = await axios.get('http://127.0.0.1:8000/api/guru')
  guruOptions.value = res.data
}

const handleFileUpload = (event) => {
  const file = event.files[0]
  if (file) {
    form.value.foto = file
    formPreview.value = URL.createObjectURL(file)
  }
}

const submitForm = async () => {
  const formData = new FormData()
  for (const key in form.value) {
    if (form.value[key] !== null) {
      formData.append(key, form.value[key])
    }
  }

  try {
    if (existingId.value) {
      await axios.post(`http://127.0.0.1:8000/api/profil-sekolah/${existingId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      alert('Profil sekolah diperbarui!')
    } else {
      await axios.post('http://127.0.0.1:8000/api/profil-sekolah', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      alert('Profil sekolah disimpan!')
    }
  } catch (err) {
    console.error(err)
    alert('Gagal menyimpan data')
  }
}

onMounted(() => {
  fetchProfil()
  fetchGurus()
})
</script>

<style scoped>
.field {
  display: flex;
  flex-direction: column;
}
</style>
