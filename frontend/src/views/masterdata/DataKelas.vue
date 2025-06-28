<template>
  <div class="p-4">
    <h2 class="text-xl font-semibold mb-4">Data Kelas</h2>

    <Button label="Tambah Kelas" icon="pi pi-plus" class="mb-3" @click="addKelas" />

    <DataTable
      :value="kelasList"
      :key="kelasList.length"
      :paginator="true"
      :rows="10"
      responsiveLayout="scroll"
      stripedRows
      class="mb-4"
    >
      <Column field="nama_kelas" header="Nama Kelas" style="min-width: 150px" />
      <Column header="Wali Kelas">
        <template #body="slotProps">
          {{ slotProps.data.guru?.nama || '-' }}
        </template>
      </Column>
      <Column header="Aksi" style="text-align: center; width: 200px;">
        <template #body="slotProps">
          <Button icon="pi pi-eye" class="p-button-sm p-button-text" @click="openSiswaDialog(slotProps.data)" />
          <Button icon="pi pi-pencil" class="p-button-sm p-button-text" @click="editKelas(slotProps.data)" />
          <Button icon="pi pi-trash" class="p-button-sm p-button-text p-button-danger" @click="deleteKelas(slotProps.data)" />
        </template>
      </Column>
    </DataTable>

    <Dialog v-model:visible="showDialog" :header="form.id ? 'Edit Kelas' : 'Tambah Kelas'" modal :closable="true" :style="{ width: '30vw' }">
      <div class="p-fluid">
        <label class="mb-2 font-semibold">Nama Kelas</label>
        <InputText v-model="form.nama_kelas" class="mb-3" :disabled="!!form.id" />

        <label class="mb-2 font-semibold">Kode Kelas</label>
        <InputText v-model="form.kode_kelas" class="mb-3" :disabled="!!form.id" />

        <label class="mb-2 font-semibold">Wali Kelas</label>
        <Dropdown
          v-model="form.guru_id"
          :options="guruList"
          optionLabel="nama"
          optionValue="id"
          placeholder="Pilih Guru"
          class="mb-3"
        />

        <Button label="Simpan" icon="pi pi-check" class="mt-2" @click="saveKelas" />
      </div>
    </Dialog>

    <Dialog v-model:visible="showSiswaDialog" header="Data Siswa" modal :closable="true" :style="{ width: '1300px' }">
      <DataTable :value="siswaList" :paginator="false" responsiveLayout="scroll" stripedRows>
        <Column field="nama_lengkap_siswa" header="Nama" />
        <Column field="nis" header="NIS" />
        <Column field="nisn" header="NISN" />
        <Column field="jenis_kelamin.jenis_kelamin" header="Jenis Kelamin" />
        <Column field="tempat_lahir" header="Tempat" />
        <Column field="tanggal_lahir" header="Tanggal Lahir" />
        <Column field="nomor_telepon" header="No Telepon" />
      </DataTable>
    </Dialog>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog
      v-model:visible="showDeleteDialog"
      header="Konfirmasi Hapus"
      modal
      :closable="false"
      :style="{ width: '350px' }"
    >
      <div class="p-3">
        <p>Apakah Anda yakin ingin menghapus kelas <strong>{{ kelasToDelete?.nama_kelas }}</strong>?</p>
      </div>
      <template #footer>
        <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="showDeleteDialog = false" />
        <Button label="Hapus" icon="pi pi-trash" class="p-button-danger" @click="confirmDeleteKelas" />
      </template>
    </Dialog>

    <!-- Toast Notification -->
    <Toast />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const kelasList = ref([]);
const guruList = ref([]);
const siswaList = ref([]);

const showDialog = ref(false);
const showSiswaDialog = ref(false);
const showDeleteDialog = ref(false);
const kelasToDelete = ref(null);

const form = ref({
  id: null,
  nama_kelas: '',
  kode_kelas: '',
  guru_id: null,
});

const resetForm = () => {
  form.value = {
    id: null,
    nama_kelas: '',
    kode_kelas: '',
    guru_id: null,
  };
};

const fetchData = async () => {
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/kelas');
    kelasList.value = data;
  } catch (error) {
    console.error('Gagal mengambil data kelas:', error);
  }
};

const fetchGuru = async () => {
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/guru');
    guruList.value = data;
  } catch (error) {
    console.error('Gagal mengambil data guru:', error);
  }
};

const getSiswaByKelas = async (kelasId) => {
  try {
    const { data } = await axios.get(`http://127.0.0.1:8000/api/siswa/kelas/${kelasId}`);
    siswaList.value = data;
  } catch (error) {
    console.error('Gagal mengambil data siswa:', error);
  }
};

const openSiswaDialog = async (row) => {
  await getSiswaByKelas(row.id);
  showSiswaDialog.value = true;
};

const editKelas = (row) => {
  form.value = {
    id: row.id,
    nama_kelas: row.nama_kelas,
    kode_kelas: row.kode_kelas,
    guru_id: row.guru_id || row.guru?.id || null,
  };
  showDialog.value = true;
};

const addKelas = () => {
  resetForm();
  showDialog.value = true;
};

const saveKelas = async () => {
  try {
    let response;
    if (form.value.id) {
      response = await axios.put(`http://127.0.0.1:8000/api/kelas/${form.value.id}`, form.value);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data kelas diperbarui', life: 3000 });
    } else {
      response = await axios.post('http://127.0.0.1:8000/api/kelas', form.value);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data kelas ditambahkan', life: 3000 });
    }

    const kelas = response.data;
    const index = kelasList.value.findIndex(k => k.id === kelas.id);
    if (index !== -1) {
      kelasList.value[index] = kelas;
    } else {
      kelasList.value.push(kelas);
    }

    showDialog.value = false;
    resetForm();
  } catch (error) {
    console.error('Gagal menyimpan data kelas:', error.response?.data || error);
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menyimpan data kelas', life: 3000 });
  }
};

const deleteKelas = (row) => {
  kelasToDelete.value = row;
  showDeleteDialog.value = true;
};

const confirmDeleteKelas = async () => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/kelas/${kelasToDelete.value.id}`);
    toast.add({ severity: 'warn', summary: 'Dihapus', detail: 'Data kelas dihapus', life: 3000 });
    await fetchData();
  } catch (error) {
    console.error('Gagal menghapus kelas:', error);
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal menghapus data kelas', life: 3000 });
  } finally {
    showDeleteDialog.value = false;
    kelasToDelete.value = null;
  }
};

onMounted(() => {
  fetchData();
  fetchGuru();
});

watch(showDialog, (val) => {
  if (!val) {
    resetForm();
    fetchData();
  }
});
</script>

<style scoped>
.p-fluid > * {
  margin-bottom: 1rem;
}
</style>
