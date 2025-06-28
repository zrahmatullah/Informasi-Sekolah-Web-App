<template>
  <div class="form-container">
    <h2>Input Data Siswa</h2>
    <form @submit.prevent="submitForm" class="form-grid">
      <!-- Tombol Simpan -->
      <div class="button-wrapper">
        <Button label="Simpan" icon="pi pi-sign-in" type="submit" class="submit-btn" />
      </div>

      <!-- Kelas -->
      <div class="input-group">
        <label for="kelas_id">Kelas</label>
        <Dropdown v-model="formData.kelas_id" :options="kelasList" optionLabel="nama_kelas" optionValue="id" placeholder="Pilih Kelas" />
      </div>

      <!-- Nama Siswa -->
      <div class="input-group">
        <label for="nama">Nama Siswa</label>
        <Textarea v-model="formData.nama_lengkap_siswa" autoResize rows="2" />
      </div>

      <!-- Jenis Kelamin -->
      <div class="input-group">
        <label for="jenis_kelamin">Jenis Kelamin</label>
        <Dropdown v-model="formData.jenis_kelamin_id" :options="jenisKelaminList" optionLabel="jenis_kelamin" optionValue="id" placeholder="Pilih Jenis Kelamin" />
      </div>

      <!-- NIS -->
      <div class="input-group">
        <label for="nis">NIS</label>
        <Textarea v-model="formData.nis" autoResize rows="1" />
      </div>

      <!-- NISN -->
      <div class="input-group">
        <label for="nisn">NISN</label>
        <Textarea v-model="formData.nisn" autoResize rows="1" />
      </div>

      <!-- Tempat Lahir -->
      <div class="input-group">
        <label for="tempat_lahir">Tempat Lahir</label>
        <Textarea v-model="formData.tempat_lahir" autoResize rows="2" />
      </div>

      <!-- Tanggal Lahir -->
      <div class="input-group">
        <label for="tanggal_lahir">Tanggal Lahir</label>
        <Calendar v-model="formData.tanggal_lahir" dateFormat="yy-mm-dd" showIcon />
      </div>

      <!-- Agama -->
      <div class="input-group">
        <label for="agama">Agama</label>
        <Textarea v-model="formData.agama" autoResize rows="2" />
      </div>

      <!-- Anak Ke -->
      <div class="input-group">
        <label for="anak_ke">Anak Ke</label>
        <Textarea v-model="formData.anak_ke" autoResize rows="1" />
      </div>

      <!-- Kolom Tambahan -->
      <div class="input-group" v-for="(field, key) in additionalFields" :key="key">
        <label :for="key">{{ field.label }}</label>
        <Textarea v-model="formData[key]" autoResize rows="2" />
      </div>

      <!-- Kirim OTP -->
      <Button label="Kirim OTP" @click="kirimOtp" :disabled="loading || otpSent"  class="otp-btn p-button-text" />

      <!-- Input OTP -->
      <div v-if="otpSent" class="input-group mt-3">
        <label for="otp">Masukkan Kode OTP</label>
        <Textarea v-model="otpCode" autoResize rows="1" />
        <Button label="Verifikasi OTP"  class="otp-btn p-button-text" @click="verifikasiOtp" :disabled="loading" />
      </div>

      <p v-if="message" :class="{ 'text-green-500': success, 'text-red-500': !success }"  class="otp-btn p-button-text"">
        {{ message }}
      </p>
    </form>

    <Toast />
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from "axios";
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Toast from 'primevue/toast';

export default {
  name: "InputDataSiswa",
  components: {
    Textarea,
    Dropdown,
    Button,
    Calendar,
    Toast,
  },
  data() {
    return {
      formData: {
        nama_lengkap_siswa: "",
        kelas_id: "",
        nis: "",
        nisn: "",
        tempat_lahir: "",
        tanggal_lahir: "",
        agama: "",
        anak_ke: "",
        alamat: "",
        rt_rw: "",
        dusun: "",
        kelurahan: "",
        kecamatan: "",
        kode_pos: "",
        nama_ayah: "",
        pekerjaan_ayah: "",
        pendidikan_ayah: "",
        nama_ibu: "",
        pekerjaan_ibu: "",
        pendidikan_ibu: "",
        nomor_telepon: "",
        email: "",
        jenis_kelamin_id: "",
      },
      kelasList: [],
      jenisKelaminList: [],
      otpCode: '',
      otpSent: false,
      loading: false,
      message: '',
      success: false,
      emailVerified: false,
      additionalFields: {
        alamat: { label: "Alamat" },
        rt_rw: { label: "RT/RW" },
        dusun: { label: "Dusun" },
        kelurahan: { label: "Kelurahan" },
        kecamatan: { label: "Kecamatan" },
        kode_pos: { label: "Kode Pos" },
        nama_ayah: { label: "Nama Ayah" },
        pekerjaan_ayah: { label: "Pekerjaan Ayah" },
        pendidikan_ayah: { label: "Pendidikan Ayah" },
        nama_ibu: { label: "Nama Ibu" },
        pekerjaan_ibu: { label: "Pekerjaan Ibu" },
        pendidikan_ibu: { label: "Pendidikan Ibu" },
        nomor_telepon: { label: "Nomor Telepon" },
        email: { label: "Email" },
      }
    };
  },
  methods: {
    fetchKelas() {
      axios.get('http://127.0.0.1:8000/api/kelas')
        .then(response => this.kelasList = response.data)
        .catch(error => console.error("Gagal fetch kelas:", error));
    },
    fetchJenisKelamin() {
      axios.get('http://127.0.0.1:8000/api/jenis-kelamin')
        .then(response => this.jenisKelaminList = response.data)
        .catch(error => console.error("Gagal fetch jenis kelamin:", error));
    },
    kirimOtp() {
      if (!this.formData.email) {
        this.message = "Email harus diisi sebelum mengirim OTP.";
        this.success = false;
        return;
      }
      this.loading = true;
      const token = localStorage.getItem('access_token');
      axios.post('http://127.0.0.1:8000/api/email-otp/send', { email: this.formData.email }, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then(() => {
          this.otpSent = true;
          this.message = 'Kode OTP berhasil dikirim ke email.';
          this.success = true;
        })
        .catch(() => {
          this.message = 'Gagal mengirim OTP.';
          this.success = false;
        })
        .finally(() => this.loading = false);
    },
    verifikasiOtp() {
      if (!this.otpCode) {
        this.message = "Kode OTP tidak boleh kosong.";
        this.success = false;
        return;
      }
      this.loading = true;
      const token = localStorage.getItem('access_token');
      axios.post('http://127.0.0.1:8000/api/email-otp/verify', {
        email: this.formData.email,
        otp: this.otpCode
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then(() => {
          this.message = 'OTP berhasil diverifikasi.';
          this.success = true;
          this.emailVerified = true;
        })
        .catch(() => {
          this.message = 'OTP tidak valid atau kadaluarsa.';
          this.success = false;
        })
        .finally(() => this.loading = false);
    },
    submitForm() {
      if (!this.emailVerified) {
        this.$toast.add({ severity: 'error', summary: 'Email belum diverifikasi.', life: 3000 });
        return;
      }
      for (const key in this.formData) {
        if (this.formData[key] === "" || this.formData[key] === null) {
          this.$toast.add({
            severity: 'error',
            summary: `Kolom ${key} harus diisi`,
            life: 3000,
          });
          return;
        }
      }
      const token = localStorage.getItem('access_token');
      axios.post('http://127.0.0.1:8000/api/siswa', this.formData, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then(() => {
          this.$toast.add({ severity: 'success', summary: 'Data berhasil disimpan', life: 3000 });
          this.resetForm();
        })
        .catch(() => {
          this.$toast.add({ severity: 'error', summary: 'Gagal menyimpan data', life: 3000 });
        });
    },
    resetForm() {
      for (const key in this.formData) this.formData[key] = "";
      this.otpCode = "";
      this.otpSent = false;
      this.message = "";
      this.success = false;
      this.emailVerified = false;
    }
  },
  mounted() {
    this.fetchKelas();
    this.fetchJenisKelamin();
  }
};
</script>

<style scoped>
.form-container {
  padding: 20px;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.input-group {
  display: flex;
  flex-direction: column;
}
label {
  margin-bottom: 5px;
}
.submit-btn {
  width: 150px;
  font-size: 16px;
  padding: 10px 20px;
  background-color: #0e52aa;
  color: white;
  border: none;
  border-radius: 4px;
}
.button-wrapper {
  grid-column: 1 / -1;
  display: flex;
  justify-content: flex-end;
  margin-bottom: 1rem;
}
.otp-btn {
  padding: 6px 12px !important;  /* cukup padding atas bawah */
  font-size: 0.85rem !important;
  color: #0e52aa !important;
  border: 1.5px solid #0e52aa !important;
  background-color: transparent !important;
  width: 150px !important;
  min-width: 100px !important;
  box-shadow: none !important;
  border-radius: 0 !important;
  cursor: pointer;
}

.otp-btn:hover {
  background-color: #0e52aa !important;
  color: white !important;
}
</style>
