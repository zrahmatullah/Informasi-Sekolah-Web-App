import { createApp } from 'vue';
import App from './App.vue';
import router from './router/router';
import axios from 'axios';

import PrimeVue from 'primevue/config';
import Button from 'primevue/button';
import Menubar from 'primevue/menubar';
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import Dropdown from 'primevue/dropdown';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Chart from 'primevue/chart'
import Password from 'primevue/password'
import ConfirmDialog from 'primevue/confirmdialog'
import ConfirmationService from 'primevue/confirmationservice'

import 'primevue/resources/themes/lara-light-indigo/theme.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css';

axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('access_token')}`;

const app = createApp(App);

app.use(router);
app.use(PrimeVue);
app.use(ToastService);
app.use(ConfirmationService) 

app.component('Toast', Toast);
app.component('Button', Button);
app.component('p-menubar', Menubar);
app.component('Dropdown', Dropdown);
app.component('Dialog', Dialog);
app.component('InputText', InputText);
app.component('ConfirmDialog', ConfirmDialog)
app.component('DataTable', DataTable);
app.component('Column', Column);
app.component('Chart', Chart);
app.component('Password', Password)

app.mount('#app');
