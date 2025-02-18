<template>
  <div class="home">
    <h1>Welcome to Booking System</h1>
    <div class="services-grid">
      <div v-for="service in services" :key="service.id" class="service-card">
        <h3>{{ service.name }}</h3>
        <p>{{ service.description }}</p>
        <button @click="bookService(service.id)" class="btn btn-primary">Book Now</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'Home',
  setup() {
    const services = ref([])

    const fetchServices = async () => {
      try {
        const response = await axios.get('/api/services')
        services.value = response.data
      } catch (error) {
        console.error('Error fetching services:', error)
      }
    }

    const bookService = (serviceId) => {
      // Navigate to booking page with service ID
      router.push({ name: 'Bookings', params: { serviceId } })
    }

    onMounted(() => {
      fetchServices()
    })

    return {
      services,
      bookService
    }
  }
}
</script>

<style scoped>
.home {
  padding: 2rem;
}

.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.service-card {
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  background: white;
  transition: transform 0.2s;
}

.service-card:hover {
  transform: translateY(-5px);
}

.btn {
  margin-top: 1rem;
}
</style>
