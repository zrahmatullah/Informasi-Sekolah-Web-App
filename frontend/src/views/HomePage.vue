<template>
  <div>
    <!-- Navbar -->
    <p-menubar class="navbar">
      <template #start>
        <div class="navbar-start">
          <h3 class="school-name">Sekolah Dasar Al Muhajirin</h3>
        </div>
      </template>
      <template #end>
        <div class="navbar-end">
          <Button v-if="!isAuthenticated" icon="pi pi-sign-in" label="Login" class="p-button-primary p-button-rounded p-button-sm" @click="goToLogin" />
        </div>
      </template>
    </p-menubar>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-text">
        <h1>Selamat Datang di <span class="highlight">Sekolah Dasar Al Muhajirin</span></h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus lacinia odio vitae vestibulum vestibulum. Integer nec odio. Praesent libero.</p>
      </div>
    </section>

    <!-- Tentang Kami -->
    <section class="section-profil">
      <h2><i class="pi pi-info-circle"></i> Tentang Kami</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ullamcorper, lorem vel dapibus feugiat, justo sem ultricies felis, sed facilisis sapien nulla sed erat.</p>

      <div class="profil-content">
        <div class="profil-item" @click="showContent('visi-misi')">
          <i class="pi pi-eye icon"></i>
          <h3>Visi dan Misi</h3>
          <p>Klik untuk melihat visi dan misi</p>
        </div>
        <div class="profil-item" @click="showContent('prestasi')">
          <i class="pi pi-star icon"></i>
          <h3>Prestasi</h3>
          <p>Klik untuk melihat prestasi siswa</p>
        </div>
        <div class="profil-item" @click="showContent('ekstrakurikuler')">
          <i class="pi pi-users icon"></i>
          <h3>Ekstrakurikuler</h3>
          <p>Klik untuk melihat kegiatan siswa</p>
        </div>
        <div class="profil-item" @click="showContent('program-pembelajaran')">
          <i class="pi pi-book icon"></i>
          <h3>Program Pembelajaran</h3>
          <p>Klik untuk melihat program belajar</p>
        </div>
        <div class="profil-item" @click="showContent('guru-staf')">
          <i class="pi pi-id-card icon"></i>
          <h3>Guru & Staf</h3>
          <p>Klik untuk melihat tenaga pendidik</p>
        </div>
        <div class="profil-item" @click="showContent('fasilitas')">
          <i class="pi pi-building icon"></i>
          <h3>Fasilitas</h3>
          <p>Klik untuk melihat fasilitas sekolah</p>
        </div>
      </div>
    </section>

    <!-- Konten yang dipilih -->
    <section v-if="selectedContent" class="section-content">
      <h3><i class="pi pi-folder-open"></i> {{ selectedContent.title }}</h3>
      <p>{{ selectedContent.description }}</p>
    </section>
  </div>
</template>

<script>
import Button from 'primevue/button';
import Menubar from 'primevue/menubar';

export default {
  name: 'HomePage',
  components: {
    Button,
    Menubar
  },
  data() {
    return {
      isAuthenticated: false,
      selectedContent: null
    };
  },
  methods: {
    goToLogin() {
      this.$router.push('/login');
    },
    showContent(contentType) {
      const dummyText = `Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
      Cras vel sapien massa. Vivamus eu turpis ut nunc malesuada hendrerit. 
      Sed sagittis felis eget justo scelerisque, vel vestibulum mi fermentum. 
      Nulla facilisi. Suspendisse potenti. Morbi in tincidunt sapien. 
      Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae. 
      In euismod sem sit amet metus fermentum, sed finibus arcu congue. 
      Donec euismod eros at nisi facilisis, a fermentum lorem iaculis. 
      Etiam pulvinar diam nec purus scelerisque dignissim.`;

      const contentData = {
        'visi-misi': {
          title: 'Visi dan Misi',
          description: dummyText
        },
        'prestasi': {
          title: 'Prestasi',
          description: dummyText
        },
        'ekstrakurikuler': {
          title: 'Ekstrakurikuler',
          description: dummyText
        },
        'program-pembelajaran': {
          title: 'Program Pembelajaran',
          description: dummyText
        },
        'guru-staf': {
          title: 'Guru dan Staf',
          description: dummyText
        },
        'fasilitas': {
          title: 'Fasilitas',
          description: dummyText
        }
      };

      this.selectedContent = contentData[contentType];
    }
  }
};
</script>

<style scoped>
/* Navbar */
.p-menubar {
  background-color: #0056b3;
  padding: 0 30px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.school-name {
  color: #fff;
  font-size: 1.8rem;
  font-weight: bold;
}
.navbar-end {
  display: flex;
  align-items: center;
  gap: 20px;
}

/* Hero */
.hero {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  padding: 40px 20px;
  background: #f0f8ff;
}
.hero-text {
  flex: 1;
}
.highlight {
  color: #007bff;
}

/* Section Profil */
.section-profil {
  padding: 40px 20px;
}
.section-profil h2 {
  font-size: 2rem;
  margin-bottom: 10px;
  color: #0056b3;
}
.profil-content {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-top: 20px;
}
.profil-item {
  flex: 1 1 calc(33.333% - 20px);
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  padding: 20px;
  cursor: pointer;
  transition: transform 0.2s;
  text-align: center;
}
.profil-item:hover {
  transform: scale(1.05);
  background-color: #f0f8ff;
}
.icon {
  font-size: 2rem;
  color: #007bff;
  margin-bottom: 10px;
}

/* Section Content */
.section-content {
  background-color: #fff;
  margin: 20px;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}
.section-content h3 {
  color: #0056b3;
  margin-bottom: 10px;
}
</style>
