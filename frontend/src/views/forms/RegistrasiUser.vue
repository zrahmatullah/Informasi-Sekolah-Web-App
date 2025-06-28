<template>
  <section class="container">
    <div class="top-bar">
      <Button label="Tambah User" icon="pi pi-plus" @click="openAddUserModal" />
    </div>

    <Dialog v-model:visible="showModal" modal header="Tambah User Baru" :style="{ width: '1000px' }">
      <form @submit.prevent="submitUser">
        <div class="field">
          <label for="role">Role</label>
          <Dropdown
            v-model="form.role_id"
            :options="roles"
            optionLabel="name"
            optionValue="id"
            placeholder="Pilih Role"
            id="role"
            @change="onRoleChange"
            class="w-full"
            required
          />
        </div>

        <div class="field" v-if="form.role_id === 2">
          <label for="guru">Pilih Guru</label>
          <Dropdown
            v-model="form.guru_id"
            :options="gurus"
            optionLabel="nama"
            optionValue="id"
            placeholder="Pilih Guru"
            class="w-full"
            id="guru"
            required
          />
        </div>

        <div class="field" v-if="form.role_id === 3">
          <label for="siswa">Pilih Siswa</label>
          <Dropdown
            v-model="form.siswa_id"
            :options="siswas"
            optionLabel="nama_lengkap_siswa"
            optionValue="id"
            placeholder="Pilih Siswa"
            class="w-full"
            id="siswa"
            required
          />
        </div>

        <div class="field">
          <label for="username">Username</label>
          <InputText v-model="form.username" id="username" class="w-full" required />
        </div>

        <div class="field">
          <label for="password">Password</label>
          <Password v-model="form.password" toggleMask :feedback="false" id="password" class="w-full" required />
        </div>

        <div class="field">
          <label for="password_confirmation">Konfirmasi Password</label>
          <Password
            v-model="form.password_confirmation"
            toggleMask
            :feedback="false"
            id="password_confirmation"
            class="w-full"
            required
          />
        </div>

        <Button label="Simpan" type="submit" class="mt-3 w-full" :loading="loading" />
      </form>
    </Dialog>

    <Dialog v-model:visible="showPasswordModal" modal header="Edit Password" :style="{ width: '600px' }">
      <form @submit.prevent="submitEditPassword">
        <div class="field">
          <label for="newPassword">Password Baru</label>
          <Password v-model="passwordForm.password" toggleMask :feedback="false" id="newPassword" class="w-full" required />
        </div>
        <div class="field">
          <label for="confirmPassword">Konfirmasi Password Baru</label>
          <Password
            v-model="passwordForm.password_confirmation"
            toggleMask
            :feedback="false"
            id="confirmPassword"
            class="w-full"
            required
          />
        </div>
        <Button label="Update Password" type="submit" class="mt-3 w-full" :loading="loading" />
      </form>
    </Dialog>

    <div class="table-section">
      <h3>Data Pengguna</h3>
      <DataTable :value="users" class="user-table" responsiveLayout="scroll">
        <Column field="username" header="Username" />
        <Column field="role.name" header="Role" />
        <Column header="Aksi">
          <template #body="slotProps">
            <Button
              icon="pi pi-key"
              class="p-button-rounded p-button-warning p-mr-2"
              title="Edit Password"
              @click="editPassword(slotProps.data)"
            />
            <Button
              icon="pi pi-trash"
              class="p-button-rounded p-button-danger"
              title="Hapus Akun"
              @click="deleteUser(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

// PrimeVue components
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'

// State
const showModal = ref(false)
const showPasswordModal = ref(false)
const loading = ref(false)

const form = ref({
  username: '',
  password: '',
  password_confirmation: '',
  role_id: null,
  siswa_id: null,
  guru_id: null,
})

const passwordForm = ref({
  user_id: null,
  password: '',
  password_confirmation: '',
})

const roles = ref([])
const gurus = ref([])
const siswas = ref([])
const users = ref([])

const toast = useToast()

const openAddUserModal = () => {
  showModal.value = true
  form.value = {
    username: '',
    password: '',
    password_confirmation: '',
    role_id: null,
    siswa_id: null,
    guru_id: null,
  }
}

// API Calls
const fetchRoles = async () => {
  const res = await axios.get('http://127.0.0.1:8000/api/roles')
  roles.value = res.data
}

const fetchGurus = async () => {
  const res = await axios.get('http://127.0.0.1:8000/api/guru')
  gurus.value = res.data
}

const fetchSiswas = async () => {
  const res = await axios.get('http://127.0.0.1:8000/api/siswa')
  siswas.value = res.data
}

const fetchUsers = async () => {
  const res = await axios.get('http://127.0.0.1:8000/api/users')
  users.value = res.data
}

const onRoleChange = () => {
  form.value.guru_id = null
  form.value.siswa_id = null
  if (form.value.role_id === 2) {
    fetchGurus()
  } else if (form.value.role_id === 3) {
    fetchSiswas()
  }
}

const submitUser = async () => {
  loading.value = true

  if (form.value.role_id === 2 && !form.value.guru_id) {
    toast.add({ severity: 'warn', summary: 'Validasi', detail: 'Pilih guru', life: 3000 })
    loading.value = false
    return
  }

  if (form.value.role_id === 3 && !form.value.siswa_id) {
    toast.add({ severity: 'warn', summary: 'Validasi', detail: 'Pilih siswa', life: 3000 })
    loading.value = false
    return
  }

  if (form.value.password !== form.value.password_confirmation) {
    toast.add({ severity: 'warn', summary: 'Validasi', detail: 'Password tidak sama', life: 3000 })
    loading.value = false
    return
  }

  try {
    await axios.post('http://127.0.0.1:8000/api/users', form.value)
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'User ditambahkan', life: 3000 })
    showModal.value = false
    fetchUsers()
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: err.response?.data?.message || 'Terjadi kesalahan', life: 3000 })
  } finally {
    loading.value = false
  }
}

const editPassword = (user) => {
  passwordForm.value = {
    user_id: user.id,
    password: '',
    password_confirmation: '',
  }
  showPasswordModal.value = true
}

const submitEditPassword = async () => {
  loading.value = true

  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    toast.add({ severity: 'warn', summary: 'Validasi', detail: 'Password tidak sama', life: 3000 })
    loading.value = false
    return
  }

  try {
    await axios.put(`http://127.0.0.1:8000/api/users/${passwordForm.value.user_id}/password`, passwordForm.value)
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Password berhasil diubah', life: 3000 })
    showPasswordModal.value = false
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: err.response?.data?.message || 'Terjadi kesalahan', life: 3000 })
  } finally {
    loading.value = false
  }
}

const deleteUser = async (user) => {
  const konfirmasi = confirm(`Yakin ingin menghapus user ${user.username}?`)
  if (!konfirmasi) return

  try {
    await axios.delete(`http://127.0.0.1:8000/api/users/${user.id}`)
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'User dihapus', life: 3000 })
    fetchUsers()
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Gagal', detail: err.response?.data?.message || 'Terjadi kesalahan', life: 3000 })
  }
}

onMounted(() => {
  fetchRoles()
  fetchUsers()
})
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.top-bar {
  display: flex;
  justify-content: flex-start;
}
.table-section {
  width: 100%;
}
.field {
  margin-bottom: 1.2rem;
}
form {
  display: flex;
  flex-direction: column;
}
.w-full {
  width: 100%;
}
.user-table {
  margin-top: 1rem;
  width: 100%;
}
</style>
