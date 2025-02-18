import { createApp } from 'vue';
import BookingCalendar from './components/BookingCalendar.vue';

const app = createApp({});

app.component('booking-calendar', BookingCalendar);
app.mount('#app');
