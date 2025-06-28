<template>
  <div class="admin-dashboard">
    <div class="sidebar">
      <div class="sidebar-header">
        <i class="pi pi-spin pi-cog" style="font-size: 3em; color: white;"></i>
      </div>
        <ul>
          <li @click="showForm('dashboard-guru')"><i class="fa fa-tachometer-alt"></i> Dashboard</li>
          <li @click="showForm('data-siswa-guru')"><i class="fa fa-users"></i> Data Siswa</li>
          <li @click="showForm('jadwal-pelajaran-kelas')"><i class="fa fa-calendar-alt"></i> Jadwal Pelajaran</li>
          <li @click="showForm('nilai-form')"><i class="fa fa-pen-square"></i> Input Nilai Siswa</li>
          <li @click="showForm('nilai-all-siswa')"><i class="fa fa-chart-bar"></i> Nilai Keseluruhan Siswa</li>
          <li @click="showForm('nilai-all')"><i class="fa fa-chart-bar"></i> Nilai Keseluruhan Siswa</li>
          <li @click="showForm('tanggapan')"><i class="fa fa-comment-dots"></i> Kritik Dan Saran</li>
      </ul>
    </div>

    <div class="content">
      <div class="navbar">
        <div class="navbar-left">
          <h1>Guru Dashboard</h1>
        </div>
        <div class="navbar-right">
          <div class="profile-dropdown" @click.stop="toggleDropdown">
            <i class="fa fa-user-circle"></i>
            <div v-if="dropdownVisible" class="dropdown-menu">
              <p>{{ user.username }}</p>
              <button @click="editPassword">Ganti Password</button>
              <button @click="logout" class="logout-button">Logout</button>
            </div>
          </div>
        </div>
      </div>

      <Dialog v-model:visible="showPasswordModal" modal header="Edit Password" :style="{ width: '600px' }">
        <form @submit.prevent="submitEditPassword">
          <div class="field">
            <label for="newPassword">Password Baru</label>
            <Password v-model="passwordForm.password" toggleMask :feedback="false" id="newPassword" class="w-full" required />
          </div>
          <div class="field">
            <label for="confirmPassword">Konfirmasi Password Baru</label>
            <Password v-model="passwordForm.password_confirmation" toggleMask :feedback="false" id="confirmPassword" class="w-full" required />
          </div>
          <Button label="Update Password" type="submit" class="mt-3 w-full" :loading="loading" />
        </form>
      </Dialog>

      <div v-if="selectedForm === 'dashboard-guru'" class="form-container">
        <DashboardDefaultGuru />
      </div>
      <div v-if="selectedForm === 'data-siswa-guru'" class="form-container">
        <DataSiswaGuru />
      </div>
      <div v-if="selectedForm === 'jadwal-pelajaran-kelas'" class="form-container">
        <JadwalPelajaranPerGuruKelas />
      </div>
      <div v-if="selectedForm === 'nilai-form'" class="form-container">
        <NilaiForm />
      </div>
      <div v-if="selectedForm === 'nilai-all-siswa'" class="form-container">
        <NilaiSiswaPerGuru />
      </div>
      <div v-if="selectedForm === 'nilai-all'" class="form-container">
        <NilaiAll />
      </div>
      <div v-if="selectedForm === 'tanggapan'" class="form-container">
        <Tanggapan />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import Dialog from 'primevue/dialog'
import Password from 'primevue/password'
import Button from 'primevue/button'

import DashboardDefaultGuru from './DashboardDefaultGuru.vue'
import DataSiswaGuru from '../masterdata/DataSiswaPerGuru.vue'
import JadwalPelajaranPerGuruKelas from '../masterdata/JadwalPelajaranPerGuruKelas.vue'
import NilaiForm from '../akademis/NilaiForm.vue'
import NilaiSiswaPerGuru from '../masterdata/NilaiSiswaPerGuru.vue'
import NilaiAll from '../masterdata/NilaiSiswaAll.vue'
import Tanggapan from '../forms/Tanggapan.vue'

const selectedForm = ref('dashboard-guru')
const dropdownVisible = ref(false)
const showPasswordModal = ref(false)
const loading = ref(false)

const user = {
  username: localStorage.getItem('username') || 'Tidak Dikenal'
}

const passwordForm = ref({
  user_id: localStorage.getItem('user_id'),
  password: '',
  password_confirmation: ''
})

const showForm = (formType) => {
  selectedForm.value = formType
}

const toggleDropdown = () => {
  dropdownVisible.value = !dropdownVisible.value
}

const closeDropdown = () => {
  dropdownVisible.value = false
}

const logout = () => {
  localStorage.removeItem('access_token')
  localStorage.removeItem('role')
  localStorage.removeItem('username')
  localStorage.removeItem('user_id')
  window.location.href = '/login'
}

const editPassword = () => {
  showPasswordModal.value = true
}

const submitEditPassword = async () => {
  loading.value = true
  try {
    await axios.put(`http://127.0.0.1:8000/api/users/${passwordForm.value.user_id}/password`, passwordForm.value)
    toast.add({ severity: 'success', summary: 'Berhasil', detail: 'Password berhasil diubah', life: 3000 })
    showPasswordModal.value = false
  } catch (err) {
    toast.add({
      severity: 'error',
      summary: 'Gagal',
      detail: err.response?.data?.message || 'Gagal update password',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', closeDropdown)
})
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  flex-direction: row;
  height: 100vh;
  flex-wrap: wrap;
}

.sidebar {
  width: 280px;
  background: linear-gradient(145deg, #003080, #56a7dd);
  color: white;
  padding: 30px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: width 0.3s ease;
  border-radius: 10px;
}

.sidebar-header {
  margin-bottom: 40px;
  text-align: center;
}

.sidebar ul {
  list-style-type: none;
  padding: 0;
}

.sidebar ul li {
  margin: 20px 0;
  cursor: pointer;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
}

.sidebar ul li:hover {
  background-color: #2ea1cf;
  padding-left: 15px;
  border-radius: 5px;
}

.sidebar ul li i {
  margin-right: 20px;
  font-size: 1.4rem;
}

.sidebar ul li:hover i {
  color: #ffffff;
}

.content {
  flex-grow: 1;
  padding: 30px;
  background-color: #f9f9f9;
  border-radius: 10px;
  min-width: 0;
  overflow-x: auto;
}

.navbar {
  background-color: #003080;
  color: white;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 10px 10px 0 0;
  flex-wrap: wrap;
}

.navbar-left h1 {
  font-size: 2.2rem;
  margin: 0;
}

.navbar-right {
  display: flex;
  align-items: center;
}

.profile-dropdown {
  position: relative;
}

.profile-dropdown i {
  font-size: 3rem;
  cursor: pointer;
  color: white;
  margin-left: 20px;
}

.profile-dropdown i:hover {
  color: #2ea1cf;
}

.profile-dropdown .dropdown-menu {
  position: absolute;
  top: 35px;
  right: 0;
  background-color: #fff;
  color: #003080;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  width: 180px;
  font-size: 1.2rem;
  z-index: 999;
}

.profile-dropdown .dropdown-menu p {
  margin: 10px 0;
}

.profile-dropdown .dropdown-menu button {
  background-color: #003080;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
}

.profile-dropdown .dropdown-menu button:hover {
  background-color: #004aad;
}

.logout-button {
  background-color: #d9534f;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  width: 100%;
  margin-top: 10px;
}

.logout-button:hover {
  background-color: #c9302c;
}

.form-container {
  background-color: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-top: 20px;
}

/* Responsive Style */
@media screen and (max-width: 768px) {
  .admin-dashboard {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    border-radius: 0 0 10px 10px;
  }

  .navbar {
    flex-direction: column;
    align-items: flex-start;
  }

  .navbar-left h1 {
    font-size: 1.5rem;
  }

  .sidebar ul li {
    font-size: 1rem;
  }

  .profile-dropdown i {
    font-size: 2.2rem;
  }

  .form-container {
    padding: 20px;
}
}
.field {
  margin-bottom: 1rem;
}


.w-full {
  width: 100%;
}

.mt-3 {
  margin-top: 1rem;
}
</style>
