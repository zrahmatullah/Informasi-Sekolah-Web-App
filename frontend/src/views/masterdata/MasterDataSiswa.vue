<template>
  <div class="siswa-container">
    <div class="header">
      <h2>Daftar Siswa</h2>
      <InputText 
        v-model="searchQuery" 
        class="p-inputtext-sm" 
        placeholder="Cari Nama Siswa, NIS dan NISN" 
        style="float: right; width: 245px; margin-bottom: 20px;"
      />
    </div>

    <DataTable :value="filteredSiswaList" :paginator="true" :rows="10" :responsiveLayout="'scroll'">
      <Column field="nama_lengkap_siswa" header="Nama"></Column>
      <Column field="nama_kelas" header="Kelas"></Column>
      <Column field="nis" header="NIS"></Column>
      <Column field="nisn" header="NISN"></Column>
      <Column field="tempat_lahir" header="Tempat Lahir"></Column>
      <Column field="tanggal_lahir" header="Tanggal Lahir"></Column>
      <Column field="email" header="Email"></Column>

      <Column header="Aksi" style="width: 150px">
        <template #body="slotProps">
          <div class="action-buttons">
            <Button
              icon="pi pi-eye"
              class="p-button-rounded p-button-info p-button-sm"
              @click="viewSiswa(slotProps.data.id)"
            />
            <Button
              icon="pi pi-pencil"
              class="p-button-rounded p-button-warning p-button-sm"
              @click="editSiswa(slotProps.data.id)"
            />
            <Button
              icon="pi pi-trash"
              class="p-button-rounded p-button-danger p-button-sm"
              @click="deleteSiswa(slotProps.data.id)"
            />
          </div>
        </template>
      </Column>
    </DataTable>

    <Dialog
      header="Edit Data Siswa"
      :visible="isModalVisible"
      @update:visible="isModalVisible = $event"
      :style="{ width: '80vw' }"
      :closable="true"
    >
      <div v-if="isEditMode">
        <div class="form-group">
          <label for="nama_lengkap_siswa">Nama Lengkap</label>
          <input v-model="selectedSiswa.nama_lengkap_siswa" class="form-control" id="nama_lengkap_siswa" />
        </div>
        <div class="form-group">
          <label for="nis">NIS</label>
          <input v-model="selectedSiswa.nis" class="form-control" id="nis" />
        </div>
        <div class="form-group">
          <label for="nisn">NISN</label>
          <input v-model="selectedSiswa.nisn" class="form-control" id="nisn" />
        </div>
        <div class="form-group">
          <label for="tempat_lahir">Tempat Lahir</label>
          <input v-model="selectedSiswa.tempat_lahir" class="form-control" id="tempat_lahir" />
        </div>
        <div class="form-group">
          <label for="tanggal_lahir">Tanggal Lahir</label>
          <input v-model="selectedSiswa.tanggal_lahir" type="date" class="form-control" id="tanggal_lahir" />
        </div>
        <div class="form-group">
          <label for="agama">Agama</label>
          <input v-model="selectedSiswa.agama" class="form-control" id="agama" />
        </div>
        <div class="form-group">
          <label for="anak_ke">Anak Ke</label>
          <input v-model="selectedSiswa.anak_ke" class="form-control" id="anak_ke" />
        </div>
        <div class="form-group">
          <label for="alamat">Alamat</label>
          <input v-model="selectedSiswa.alamat" class="form-control" id="alamat" />
        </div>
        <div class="form-group">
          <label for="rt_rw">RT/RW</label>
          <input v-model="selectedSiswa.rt_rw" class="form-control" id="rt_rw" />
        </div>
        <div class="form-group">
          <label for="dusun">Dusun</label>
          <input v-model="selectedSiswa.dusun" class="form-control" id="dusun" />
        </div>
        <div class="form-group">
          <label for="kelurahan">Kelurahan</label>
          <input v-model="selectedSiswa.kelurahan" class="form-control" id="kelurahan" />
        </div>
        <div class="form-group">
          <label for="kecamatan">Kecamatan</label>
          <input v-model="selectedSiswa.kecamatan" class="form-control" id="kecamatan" />
        </div>
        <div class="form-group">
          <label for="kode_pos">Kode Pos</label>
          <input v-model="selectedSiswa.kode_pos" class="form-control" id="kode_pos" />
        </div>
        <div class="form-group">
          <label for="nama_ayah">Nama Ayah</label>
          <input v-model="selectedSiswa.nama_ayah" class="form-control" id="nama_ayah" />
        </div>
        <div class="form-group">
          <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
          <input v-model="selectedSiswa.pekerjaan_ayah" class="form-control" id="pekerjaan_ayah" />
        </div>
        <div class="form-group">
          <label for="pendidikan_ayah">Pendidikan Ayah</label>
          <input v-model="selectedSiswa.pendidikan_ayah" class="form-control" id="pendidikan_ayah" />
        </div>
        <div class="form-group">
          <label for="nama_ibu">Nama Ibu</label>
          <input v-model="selectedSiswa.nama_ibu" class="form-control" id="nama_ibu" />
        </div>
        <div class="form-group">
          <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
          <input v-model="selectedSiswa.pekerjaan_ibu" class="form-control" id="pekerjaan_ibu" />
        </div>
        <div class="form-group">
          <label for="pendidikan_ibu">Pendidikan Ibu</label>
          <input v-model="selectedSiswa.pendidikan_ibu" class="form-control" id="pendidikan_ibu" />
        </div>
        <div class="form-group">
          <label for="nomor_telepon">Nomor Telepon</label>
          <input v-model="selectedSiswa.nomor_telepon" class="form-control" id="nomor_telepon" />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input v-model="selectedSiswa.email" class="form-control" id="email" />
        </div>

        <Button label="Simpan" @click="updateSiswa" class="p-button-success mt-3" />
      </div>

      <div v-else>
        <DataTable :value="[selectedSiswa]" responsiveLayout="scroll">
          <Column field="nama_lengkap_siswa" header="Nama Lengkap"></Column>
          <Column field="nama_kelas" header="Kelas"></Column>
          <Column field="nis" header="NIS"></Column>
          <Column field="nisn" header="NISN"></Column>
          <Column field="tempat_lahir" header="Tempat Lahir"></Column>
          <Column field="tanggal_lahir" header="Tanggal Lahir"></Column>
          <Column field="agama" header="Agama"></Column>
          <Column field="anak_ke" header="Anak Ke"></Column>
          <Column field="alamat" header="Alamat"></Column>
          <Column field="rt_rw" header="RT/RW"></Column>
          <Column field="dusun" header="Dusun"></Column>
          <Column field="kelurahan" header="Kelurahan"></Column>
          <Column field="kecamatan" header="Kecamatan"></Column>
          <Column field="kode_pos" header="Kode Pos"></Column>
          <Column field="nama_ayah" header="Nama Ayah"></Column>
          <Column field="pekerjaan_ayah" header="Pekerjaan Ayah"></Column>
          <Column field="pendidikan_ayah" header="Pendidikan Ayah"></Column>
          <Column field="nama_ibu" header="Nama Ibu"></Column>
          <Column field="pekerjaan_ibu" header="Pekerjaan Ibu"></Column>
          <Column field="pendidikan_ibu" header="Pendidikan Ibu"></Column>
          <Column field="nomor_telepon" header="Telepon"></Column>
          <Column field="email" header="Email"></Column>
        </DataTable>

        <!-- <div style="margin-top: 20px; text-align: right">
          <Button label="Cetak PDF" icon="pi pi-print" class="p-button-secondary" @click="printPDF" />
        </div> -->
      </div>
    </Dialog>
  </div>
</template>

<script>
import axios from "axios";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";

export default {
  components: {
    DataTable,
    Column,
    Button,
    Dialog,
    InputText
  },
  data() {
    return {
      siswaList: [],
      kelasList: [],
      isModalVisible: false,
      selectedSiswa: null,
      isEditMode: false,
      searchQuery: "",
    };
  },
  computed: {
    filteredSiswaList() {
      if (!this.searchQuery) return this.siswaList;
      return this.siswaList.filter(siswa =>
        siswa.nama_lengkap_siswa.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        siswa.nis.toString().includes(this.searchQuery) ||
        siswa.nisn.toString().includes(this.searchQuery)
      );
    }
  },
  methods: {
    fetchKelas() {
      return axios
        .get("http://127.0.0.1:8000/api/kelas")
        .then((response) => {
          this.kelasList = response.data;
        })
        .catch((error) => {
          console.error("Gagal mengambil data kelas:", error);
        });
    },
    fetchSiswa() {
      axios
        .get("http://127.0.0.1:8000/api/siswa")
        .then((response) => {
          const siswaData = response.data;
          this.siswaList = siswaData
            .filter((siswa) => siswa.id != null)
            .map((siswa) => {
              const kelas = this.kelasList.find((k) => k.id === siswa.kelas_id);
              return {
                ...siswa,
                nama_kelas: kelas ? kelas.nama_kelas : "-",
              };
            });
        })
        .catch((error) => {
          console.error("Error fetching siswa:", error);
        });
    },
    viewSiswa(id) {
      const siswa = this.siswaList.find((siswa) => siswa.id === id);
      if (siswa) {
        this.selectedSiswa = { ...siswa };
        this.isModalVisible = true;
        this.isEditMode = false;
      }
    },
    editSiswa(id) {
      const siswa = this.siswaList.find((siswa) => siswa.id === id);
      if (siswa) {
        this.selectedSiswa = { ...siswa };
        this.isModalVisible = true;
        this.isEditMode = true;
      }
    },
    updateSiswa() {
      axios
        .put(`http://127.0.0.1:8000/api/siswa/${this.selectedSiswa.id}`, this.selectedSiswa)
        .then((response) => {
          this.fetchSiswa();
          this.isModalVisible = false;
        })
        .catch((error) => {
          console.error("Error updating siswa:", error);
        });
    },
    deleteSiswa(id) {
      axios
        .delete(`http://127.0.0.1:8000/api/siswa/${id}`)
        .then((response) => {
          this.fetchSiswa();
        })
        .catch((error) => {
          console.error("Error deleting siswa:", error);
        });
    },
    printPDF() {
      console.log("Mencetak PDF untuk siswa", this.selectedSiswa);
    }
  },
  mounted() {
    this.fetchKelas();
    this.fetchSiswa();
  }
};
</script>

<style scoped>
.siswa-container {
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
h2 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #333;
}
.action-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 5px;
}
.form-group {
  margin-bottom: 15px;
}
input.form-control {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
@media (max-width: 768px) {
  .p-datatable {
    font-size: 12px;
  }
}
</style>
