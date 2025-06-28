<template>
  <div class="login-page">
    <div class="login-container">
      <h1>Selamat Datang di Portal Login Sekolah Dasar Al Muhajirin</h1>
      <p class="welcome-message">
        Portal ini digunakan untuk mengakses berbagai fitur penting di Sekolah Dasar Al Muhajirin. 
        Silakan masuk dengan akun Anda untuk melanjutkan.
      </p>
      <form @submit.prevent="login">
        <div class="input-group">
          <input
            v-model="username"
            type="text"
            placeholder="Username"
            required
          />
        </div>
        <div class="input-group">
          <input
            v-model="password"
            type="password"
            placeholder="Password"
            required
          />
        </div>
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { useRouter } from "vue-router";

export default {
  data() {
    return {
      username: "",
      password: "",
    };
  },
  methods: {
    async login() {
      try {
        console.log("Attempting login with:", this.username);

        const response = await axios.post(
          "http://127.0.0.1:8000/api/login",
          {
            username: this.username,
            password: this.password,
          }
        );

        console.log("Response from API:", response.data);

       if (response.data.access_token) {
          localStorage.setItem("access_token", response.data.access_token);
          localStorage.setItem('user_id', response.data.user_id);
          localStorage.setItem("username", this.username);
          axios.defaults.headers.common["Authorization"] = `Bearer ${response.data.access_token}`;
          localStorage.setItem("role", response.data.role);
          this.$toast.add({
            severity: "success",
            summary: "Login Berhasil",
            detail: `Selamat datang, ${this.username}!`,
            life: 3000, 
          });

          console.log('Access Token:', localStorage.getItem('access_token'));
          console.log('Role:', localStorage.getItem('role'));

          if (response.data.role === "Admin Sekolah") {
            this.$router.push("/dashboard/admin");
          } else if (response.data.role === "Guru") {
            this.$router.push("/dashboard/guru");
          } else if (response.data.role === "Siswa") {
            this.$router.push("/dashboard/siswa");
          }
        } else {
          console.error("No access token received");

          this.$toast.add({
            severity: "error",
            summary: "Login Gagal",
            detail: "Login gagal! Tidak ada token akses yang diterima.",
            life: 3000,
          });
        }
      } catch (error) {
        console.error("Login error:", error);

        this.$toast.add({
          severity: "error",
          summary: "Login Gagal",
          detail: "Username dan Password tidak sesuai, Harap hubungi tim IT.",
          life: 3000,
        });
      }
    },
  },
};
</script>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f0f8ff;
}

.login-container {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 90%;
  max-width: 600px;
  text-align: center;
  border: 2px solid #56a7dd;
}

h1 {
  font-size: 2rem;
  color: #003080;
  margin-bottom: 10px;
}

.welcome-message {
  font-size: 1.1rem;
  color: #003080;
  margin-bottom: 20px;
  padding: 0 15px;
}

.input-group {
  margin-bottom: 15px;
}

input {
  width: 100%;
  padding: 12px;
  margin: 8px 0;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: #2ea1cf;
}

button {
  width: 100%;
  padding: 12px;
  background-color: #003080;
  border: none;
  color: white;
  font-size: 1.2rem;
  border-radius: 8px;
  cursor: pointer;
  margin-top: 15px;
}

button:hover {
  background-color: #004aad;
}

button:focus {
  outline: none;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
</style>
