<template>
  <div id="app">
    <Toast />
    <div class="app-wrapper">
      <router-view />
    </div>
  </div>
</template>

<script>
if (typeof document !== 'undefined') {
  const link = document.createElement('link');
  link.href = 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap';
  link.rel = 'stylesheet';
  document.head.appendChild(link);
}

export default {
  name: 'App',
};
</script>

<style>
body {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  line-height: 1.5;
  margin: 0;
  padding: 0;
  background-color: #f5f8fc;
  overflow-x: hidden;
}

.app-wrapper {
  max-width: 100%;
  padding: 1rem;
  box-sizing: border-box;
}
</style>
