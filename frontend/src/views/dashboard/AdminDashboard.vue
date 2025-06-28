<template>
  <div class="admin-dashboard">
    <div class="sidebar">
      <div class="sidebar-header">
        <i class="pi pi-spin pi-cog" style="font-size: 3em; color: white;"></i>
      </div>
      <ul>
        <li @click="showForm('dashboard')"><i class="fa fa-tachometer-alt"></i> Dashboard</li>
        <li @click="showForm('user-regist')"><i class="fa fa-user-plus"></i> Registrasi User</li>
        <li @click="showForm('input-data-siswa')"><i class="fa fa-user-edit"></i> Input Data Siswa</li>
        <li @click="showForm('master-siswa')"><i class="fa fa-users"></i> Master Data Siswa</li>
        <li @click="showForm('master-guru')"><i class="fa fa-chalkboard-teacher"></i> Master Data Guru</li>
        <li @click="showForm('master-jadwal-pelajaran')"><i class="fa fa-calendar-alt"></i> Master Jadwal Pelajaran</li>
        <li @click="showForm('master-tahun-pelajaran')"><i class="fa fa-calendar-alt"></i> Master Tahun Pelajaran</li>
        <li @click="showForm('mata-pelajaran')"><i class="fa fa-book"></i> Mata Pelajaran</li>
        <li @click="showForm('data-kelas')"><i class="fa fa-door-open"></i> Data Kelas</li>
        <!-- <li @click="showForm('profile-sekolah')"><i class="fa fa-school"></i> Profile Sekolah</li> -->
        <li @click="showForm('tanggal-libur')"><i class="fa fa-bullhorn"></i> Pengumuman</li>
        <li @click="showForm('kirim-surat')"><i class="fa fa-envelope"></i> Kirim Surat</li>
        <li @click="showForm('map-siswa')"><i class="fa fa-envelope"></i> Mapping Siswa</li>
      </ul>
    </div>

    <div class="content">
      <div class="navbar">
        <div class="navbar-left">
          <h1>Admin Dashboard</h1>
        </div>
        <div class="navbar-right">
          <div class="profile-dropdown" @click.stop="toggleDropdown">
            <i class="fa fa-user-circle"></i>
            <div v-if="dropdownVisible" class="dropdown-menu">
              <p>{{ user.username }}</p>
              <button @click="editPassword" class="logout-button">Ganti Password</button>
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

      <div v-if="selectedForm === 'dashboard'" class="form-container"><DashboardDefaultAdmin /></div>
      <div v-if="selectedForm === 'input-data-siswa'" class="form-container"><InputDataSiswa /></div>
      <div v-if="selectedForm === 'master-siswa'" class="form-container"><MasterDataSiswa /></div>
      <div v-if="selectedForm === 'user-regist'" class="form-container"><RegistrasiUser /></div>
      <div v-if="selectedForm === 'master-guru'" class="form-container"><MasterDataGuru /></div>
      <div v-if="selectedForm === 'master-jadwal-pelajaran'" class="form-container"><MasterJadwalPelajaran /></div>
      <div v-if="selectedForm === 'master-tahun-pelajaran'" class="form-container"><TahunPelajaran /></div>
      <div v-if="selectedForm === 'data-kelas'" class="form-container"><DataKelas /></div>
      <div v-if="selectedForm === 'mata-pelajaran'" class="form-container"><MasterDataMataPelajaran /></div>
      <div v-if="selectedForm === 'profile-sekolah'" class="form-container"><ProfileSekolah /></div>
      <div v-if="selectedForm === 'tanggal-libur'" class="form-container"><TanggalLibur /></div>
      <div v-if="selectedForm === 'kirim-surat'" class="form-container"><KirimSurat /></div>
      <div v-if="selectedForm === 'map-siswa'" class="form-container"><MapSiswaToKelas /></div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ref } from 'vue'
import { useToast } from 'primevue/usetoast'

import DashboardDefaultAdmin from './DashboardDefaultAdmin.vue'
import InputDataSiswa from '../forms/InputDataSiswa.vue'
import MasterDataSiswa from '../masterdata/MasterDataSiswa.vue'
import RegistrasiUser from '../forms/RegistrasiUser.vue'
import MasterDataGuru from '../masterdata/MasterDataGuru.vue'
import MasterJadwalPelajaran from '../masterdata/MasterJadwalPelajaran.vue'
import DataKelas from '../masterdata/DataKelas.vue'
import MasterDataMataPelajaran from '../masterdata/MasterDataMataPelajaran.vue'
import ProfileSekolah from '../forms/InputProfileSekolah.vue'
import TanggalLibur from '../forms/TanggalLibur.vue'
import KirimSurat from '../forms/KirimSurat.vue'
import TahunPelajaran from '../masterdata/TahunPelajaran.vue'
import MapSiswaToKelas from '../forms/MapSiswaToKelas.vue'

export default {
  name: 'AdminDashboard',
  components: {
    DashboardDefaultAdmin,
    InputDataSiswa,
    MasterDataSiswa,
    RegistrasiUser,
    MasterDataGuru,
    MasterJadwalPelajaran,
    TahunPelajaran,
    DataKelas,
    MasterDataMataPelajaran,
    ProfileSekolah,
    TanggalLibur,
    KirimSurat,
    MapSiswaToKelas
  },
  setup() {
    const selectedForm = ref('dashboard')
    const dropdownVisible = ref(false)
    const showPasswordModal = ref(false)
    const loading = ref(false)
    const toast = useToast()

    console.log(localStorage.getItem('user_id'));

    const passwordForm = ref({
      user_id: localStorage.getItem('user_id'),
      password: '',
      password_confirmation: ''
    })

    const user = ref({
      username: localStorage.getItem('username') || 'Tidak Dikenal'
    })

    const showForm = (form) => {
      selectedForm.value = form
    }

    const toggleDropdown = () => {
      dropdownVisible.value = !dropdownVisible.value
    }

    const closeDropdown = () => {
      dropdownVisible.value = false
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

    const logout = () => {
      localStorage.removeItem('access_token')
      localStorage.removeItem('role')
      localStorage.removeItem('username')
      localStorage.removeItem('user_id')
      window.location.href = '/login'
    }

    document.addEventListener('click', closeDropdown)
    return {
      selectedForm,
      dropdownVisible,
      showForm,
      toggleDropdown,
      closeDropdown,
      logout,
      editPassword,
      submitEditPassword,
      showPasswordModal,
      passwordForm,
      loading,
      user
    }
  },
  unmounted() {
    document.removeEventListener('click', this.closeDropdown)
  }
}
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  height: 100vh;
  flex-direction: row;
  flex-wrap: wrap;
}
.sidebar {
  width: 280px;
  background: linear-gradient(145deg, #003080, #56a7dd);
  color: white;
  padding: 30px;
  display: flex;
  flex-direction: column;
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
}
.profile-dropdown {
  position: relative;
}
.profile-dropdown i {
  font-size: 3rem;
  cursor: pointer;
  color: white;
}
.dropdown-menu {
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
.dropdown-menu p {
  margin: 10px 0;
}
.dropdown-menu button {
  background-color: #003080;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
  margin-bottom: 10px;
}
.dropdown-menu button:hover {
  background-color: #004aad;
}
.logout-button {
  background-color: #d9534f;
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
@media screen and (max-width: 768px) {
  .admin-dashboard {
    flex-direction: column;
  }
  .sidebar {
    width: 100%;
    flex-direction: row;
    overflow-x: auto;
    padding: 10px;
  }
  .sidebar ul {
    display: flex;
    flex-wrap: nowrap;
  }
  .sidebar ul li {
    margin: 0 10px;
    font-size: 1rem;
  }
  .content {
    padding: 10px;
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
