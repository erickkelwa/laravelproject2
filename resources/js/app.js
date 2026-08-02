import './bootstrap';
import { createApp } from 'vue';
import StudentProfile from './components/StudentProfile.vue';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const app = createApp({});
app.component('student-profile', StudentProfile);
app.mount('#app');
