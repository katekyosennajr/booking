<template>
  <div class="calendar-container">
    <FullCalendar 
      :options="calendarOptions"
      class="demo-app-calendar"
    />
  </div>
</template>

<script>
import { ref } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import axios from 'axios'

export default {
  components: {
    FullCalendar
  },
  setup() {
    const calendarOptions = ref({
      plugins: [dayGridPlugin, interactionPlugin],
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek'
      },
      editable: true,
      selectable: true,
      selectMirror: true,
      dayMaxEvents: true,
      weekends: true,
      select: async (selectInfo) => {
        const title = prompt('Please enter a title for your booking:')
        if (title) {
          try {
            const response = await axios.post('/bookings/create', {
              title: title,
              start: selectInfo.startStr,
              end: selectInfo.endStr
            })
            
            if (response.data.success) {
              calendarOptions.value.events = [
                ...calendarOptions.value.events,
                {
                  title,
                  start: selectInfo.startStr,
                  end: selectInfo.endStr
                }
              ]
            }
          } catch (error) {
            console.error('Error creating booking:', error)
            alert('Failed to create booking. Please try again.')
          }
        }
      },
      eventClick: (info) => {
        if (confirm('Are you sure you want to delete this booking?')) {
          info.event.remove()
        }
      }
    })

    return {
      calendarOptions
    }
  }
}
</script>

<style>
.calendar-container {
  margin: 20px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.demo-app-calendar {
  margin: 0;
  padding: 0;
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}
</style>
