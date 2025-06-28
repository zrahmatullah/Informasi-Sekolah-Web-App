import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../views/HomePage.vue';
import LoginPage from '../views/LoginPage.vue';
import AdminDashboard from '../views/dashboard/AdminDashboard.vue';
import DashboardDefault from '../views/dashboard/DashboardDefaultAdmin.vue';
import DashboardDefaultGuru from '../views/dashboard/DashboardDefaultGuru.vue';
import DashboardDefaultSiswa from '../views/dashboard/DashboardDefaultSiswa.vue';
import GuruDashboard from '../views/dashboard/GuruDashboard.vue';
import SiswaDashboard from '../views/dashboard/SiswaDashboard.vue';
import InputDataSiswa from '../views/forms/InputDataSiswa.vue';
import MasterDataSiswa from '../views/masterdata/MasterDataSiswa.vue';
import RegistrasiUser from '../views/forms/RegistrasiUser.vue';
import NilaiForm from '../views/akademis/NilaiForm.vue';
import MasterDataGuru from '../views/masterdata/MasterDataGuru.vue';
import MasterJadwalPelajaran from '../views/masterdata/MasterJadwalPelajaran.vue';
import DataKelas from '../views/masterdata/DataKelas.vue';
import MataPelajaran from '../views/masterdata/MasterDataMataPelajaran.vue';
import ProfileSekolah from '../views/forms/InputProfileSekolah.vue';
import TanggalLibur from '../views/forms/TanggalLibur.vue';
import KirimSurat from '../views/forms/KirimSurat.vue';
import DataSiswaGuru from '../views/masterdata/DataSiswaPerGuru.vue';
import JadwalPelajaranPerGuruKelas from '../views/masterdata/JadwalPelajaranPerGuruKelas.vue';
import JadwalPelajaranPerSiswa from '../views/masterdata/JadwalPelajaranPerSiswa.vue';
import NilaiSiswaPerGuru from '../views/masterdata/NilaiSiswaPerGuru.vue';
import NilaiSiswaByLogin from '../views/masterdata/NilaiSiswaByLogin.vue';
import KritikSaranSiswa from '../views/forms/KritikSaranSiswa.vue';
import Tanggapan from '../views/forms/Tanggapan.vue';
import TahunPelaharan from '../views/masterdata/TahunPelajaran.vue';
import NilaiAll from '../views/masterdata/NilaiSiswaAll.vue';
import MapSiswaToKelas from '../views/forms/MapSiswaToKelas.vue';

const routes = [
  { path: '/', component: HomePage },
  { path: '/login', component: LoginPage },
  { path: '/dashboard/admin', component: AdminDashboard },
  { path: '/dashboard/default', component: DashboardDefault },
  { path: '/dashboard/guru', component: GuruDashboard },
  { path: '/dashboard/guru', component: DashboardDefaultGuru },
  { path: '/dashboard/siswa', component: SiswaDashboard },
  { path: '/dashboard/siswa', component: DashboardDefaultSiswa },
  { path: '/forms/inputdatasiswa', component: InputDataSiswa },
  { path: '/masterdata/masterdatasiswa', component: MasterDataSiswa },
  { path: '/forms/registrasiuser', component: RegistrasiUser },
  { path: '/akademis/nilaiform', component: NilaiForm },
  { path: '/masterdata/masterdataguru', component: MasterDataGuru },
  { path: '/masterdata/masterjadwalpelajaran', component: MasterJadwalPelajaran },
  { path: '/masterdata/datakelas', component: DataKelas },
  { path: '/masterdata/matapelajaran', component: MataPelajaran },
  { path: '/forms/profilesekolah', component: ProfileSekolah },
  { path: '/forms/tanggallibur', component: TanggalLibur },
  { path: '/forms/kirimsurat', component: KirimSurat },
  { path: '/masterdata/datasiswaguru', component: DataSiswaGuru },
  { path: '/masterdata/jadwalpelajaranpergurukelas', component: JadwalPelajaranPerGuruKelas },
  { path: '/masterdata/jadwalpelajaranpersiswa', component: JadwalPelajaranPerSiswa },
  { path: '/masterdata/nilaisiswaperguru', component: NilaiSiswaPerGuru },
  { path: '/masterdata/nilaisiswabylogin', component: NilaiSiswaByLogin },
  { path: '/forms/kritiksaransiswa', component: KritikSaranSiswa },
  { path: '/forms/tanggapan', component: Tanggapan },
  { path: '/masterdata/tahunpelajaran', component: TahunPelaharan },
  { path: '/masterdata/nilaiall', component: NilaiAll },
  { path: '/forms/mapsiswatokelas', component: MapSiswaToKelas },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('access_token');
  const role = localStorage.getItem('role');

  if (to.path.startsWith('/dashboard')) {
    if (!token) {
      next('/login');
    } else {
      console.log("Checking role:", role);

      if (to.path.startsWith('/dashboard/admin') && role === 'Admin Sekolah') {
        next();
      } else if (to.path.startsWith('/dashboard/guru') && role === 'Guru') {
        next();
      } else if (to.path.startsWith('/dashboard/siswa') && role === 'Siswa') {
        next();
      } else {
        next('/login');
      }
    }
  } else {
    next();
  }
});



export default router;