<template>
  <div class="p-6 space-y-6">
    <Toast />

    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-800">Manajemen Mata Pelajaran</h2>
      <Button icon="pi pi-plus" label="Tambah Mata Pelajaran" class="p-button-success" @click="openDialog" />
    </div>

    <div class="card">
      <DataTable
        :value="mapelList"
        dataKey="id"
        responsiveLayout="scroll"
        stripedRows
        class="p-datatable-sm shadow-md rounded-lg"
      >
        <Column field="nama_mapel" header="Nama Mata Pelajaran" sortable />
        <Column field="gurus.nama" header="Guru Pengajar" sortable />
        <Column header="Aksi" style="width: 160px">
          <template #body="slotProps">
            <div class="flex gap-3 justify-center">
              <Button icon="pi pi-pencil" severity="info" outlined rounded class="p-button-sm"
                @click="editData(slotProps.data)" />
              <Button icon="pi pi-trash" severity="danger" outlined rounded class="p-button-sm"
                @click="deleteData(slotProps.data.id)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog
      v-model:visible="showDialog"
      header="Form Mata Pelajaran"
      :modal="true"
      :style="{ width: '500px' }"
      class="p-fluid"
    >
      <form @submit.prevent="saveData" class="space-y-4">
        <div>
          <label for="nama_mapel" class="block text-sm font-medium text-gray-700 mb-1">Nama Mapel</label>
          <InputText id="nama_mapel" v-model="form.nama_mapel" placeholder="Contoh: Matematika" class="w-full" />
        </div>

        <div>
          <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">Guru Pengajar</label>
          <Dropdown
            id="guru_id"
            v-model="form.guru_id"
            :options="guruList"
            optionLabel="nama"
            optionValue="id"
            placeholder="Pilih Guru"
            class="w-full"
          />
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <Button label="Batal" icon="pi pi-times" severity="secondary" @click="resetForm" />
          <Button label="Simpan" icon="pi pi-check" type="submit" />
        </div>
      </form>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const mapelList = ref([]);
const guruList = ref([]);
const showDialog = ref(false);
const toast = useToast();

const form = ref({
  id: null,
  nama_mapel: '',
  guru_id: null,
});

const fetchData = async () => {
  try {
    const [mapelRes, guruRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/matapelajaran'),
      axios.get('http://127.0.0.1:8000/api/guru'),
    ]);
    mapelList.value = mapelRes.data;
    guruList.value = guruRes.data;
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: 'Gagal memuat data', life: 3000 });
  }
};

const saveData = async () => {
  try {
    if (form.value.id) {
      await axios.put(`http://127.0.0.1:8000/api/matapelajaran/${form.value.id}`, form.value);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data diperbarui', life: 3000 });
    } else {
      await axios.post('http://127.0.0.1:8000/api/matapelajaran', form.value);
      toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Data ditambahkan', life: 3000 });
    }
    resetForm();
    fetchData();
  } catch (error) {
    const message = error.response?.data?.message || 'Terjadi kesalahan';
    toast.add({ severity: 'error', summary: 'Gagal', detail: message, life: 3000 });
  }
};

const editData = (data) => {
  form.value = {
    id: data.id,
    nama_mapel: data.nama_mapel,
    guru_id: data.gurus?.id || null,
  };
  showDialog.value = true;
};

const deleteData = async (id) => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/matapelajaran/${id}`);
      toast.add({ severity: 'warn', summary: 'Dihapus', detail: 'Data berhasil dihapus', life: 3000 });
      fetchData();
    } catch (error) {
      toast.add({ severity: 'error', summary: 'Gagal', detail: 'Tidak dapat menghapus data', life: 3000 });
    }
  }
};

const openDialog = () => {
  resetForm();
  showDialog.value = true;
};

const resetForm = () => {
  form.value = { id: null, nama_mapel: '', guru_id: null };
  showDialog.value = false;
};

onMounted(fetchData);
</script>

<style scoped>
.p-button-sm {
  padding: 0.4rem 0.75rem !important;
  font-size: 0.8rem !important;
  border-radius: 0.375rem !important;
}

.p-datatable .p-column-header,
.p-datatable .p-cell-editing {
  white-space: nowrap;
}

.p-dialog .p-fluid .p-inputtext,
.p-dialog .p-fluid .p-dropdown {
  width: 100%;
}
</style>
