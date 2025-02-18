<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div id="admin-dashboard">
    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Bookings</h5>
                        <h2 class="card-text">{{ stats.total_bookings }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Bookings</h5>
                        <h2 class="card-text">{{ stats.pending_bookings }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="card-text">{{ stats.total_users }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Active Services</h5>
                        <h2 class="card-text">{{ stats.active_services }}/{{ stats.total_services }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="recent-bookings mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Recent Bookings</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="booking in recentBookings" :key="booking.id">
                            <td>{{ booking.title }}</td>
                            <td>{{ booking.user_name }}</td>
                            <td>{{ booking.service_name }}</td>
                            <td>{{ formatDate(booking.start_time) }}</td>
                            <td>
                                <span :class="'badge ' + getStatusClass(booking.status)">
                                    {{ booking.status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" @click="viewBooking(booking)">
                                    View
                                </button>
                                <button v-if="booking.status === 'pending'" 
                                        class="btn btn-sm btn-success" 
                                        @click="updateStatus(booking.id, 'approved')">
                                    Approve
                                </button>
                                <button v-if="booking.status === 'pending'" 
                                        class="btn btn-sm btn-danger" 
                                        @click="updateStatus(booking.id, 'rejected')">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Booking Calendar -->
    <div class="booking-calendar mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Booking Calendar</h5>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Service Stats -->
    <div class="service-stats mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Service Statistics</h5>
            </div>
            <div class="card-body">
                <canvas id="serviceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div v-if="selectedBooking">
                    <p><strong>Title:</strong> {{ selectedBooking.title }}</p>
                    <p><strong>Description:</strong> {{ selectedBooking.description }}</p>
                    <p><strong>User:</strong> {{ selectedBooking.user_name }}</p>
                    <p><strong>Service:</strong> {{ selectedBooking.service_name }}</p>
                    <p><strong>Date:</strong> {{ formatDate(selectedBooking.start_time) }}</p>
                    <p><strong>Time:</strong> {{ formatTime(selectedBooking.start_time) }} - {{ formatTime(selectedBooking.end_time) }}</p>
                    <p><strong>Status:</strong> 
                        <span :class="'badge ' + getStatusClass(selectedBooking.status)">
                            {{ selectedBooking.status }}
                        </span>
                    </p>
                    <div v-if="selectedBooking.notes">
                        <p><strong>Notes:</strong></p>
                        <p>{{ selectedBooking.notes }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <template v-if="selectedBooking && selectedBooking.status === 'pending'">
                    <button type="button" class="btn btn-success" @click="updateStatus(selectedBooking.id, 'approved')">
                        Approve
                    </button>
                    <button type="button" class="btn btn-danger" @click="updateStatus(selectedBooking.id, 'rejected')">
                        Reject
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const stats = ref({
            total_bookings: 0,
            pending_bookings: 0,
            total_users: 0,
            total_services: 0,
            active_services: 0
        });
        const recentBookings = ref([]);
        const selectedBooking = ref(null);
        const calendar = ref(null);
        const serviceChart = ref(null);

        const loadDashboardStats = async () => {
            try {
                const response = await axios.get('/api/admin/dashboard-stats');
                stats.value = response.data;
                recentBookings.value = response.data.recent_bookings;
            } catch (error) {
                console.error('Failed to load dashboard stats:', error);
            }
        };

        const initializeCalendar = () => {
            const calendarEl = document.getElementById('calendar');
            calendar.value = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '/api/admin/bookings',
                eventClick: (info) => {
                    viewBooking(info.event.extendedProps);
                }
            });
            calendar.value.render();
        };

        const initializeServiceChart = async () => {
            try {
                const response = await axios.get('/api/admin/service-stats');
                const ctx = document.getElementById('serviceChart').getContext('2d');
                
                const labels = response.data.map(s => s.service.name);
                const data = response.data.map(s => s.total_bookings);
                
                serviceChart.value = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Bookings',
                            data: data,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Failed to load service stats:', error);
            }
        };

        const updateStatus = async (bookingId, status) => {
            try {
                await axios.post('/api/admin/update-booking-status', {
                    booking_id: bookingId,
                    status: status
                });
                
                // Refresh data
                await loadDashboardStats();
                calendar.value.refetchEvents();
                
                // Close modal if open
                const modal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Booking status updated successfully'
                });
            } catch (error) {
                console.error('Failed to update booking status:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update booking status'
                });
            }
        };

        const viewBooking = (booking) => {
            selectedBooking.value = booking;
            const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
            modal.show();
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        };

        const formatTime = (date) => {
            return new Date(date).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        };

        const getStatusClass = (status) => {
            const classes = {
                pending: 'bg-warning',
                approved: 'bg-success',
                rejected: 'bg-danger',
                cancelled: 'bg-secondary'
            };
            return classes[status] || 'bg-primary';
        };

        onMounted(() => {
            loadDashboardStats();
            initializeCalendar();
            initializeServiceChart();
        });

        return {
            stats,
            recentBookings,
            selectedBooking,
            updateStatus,
            viewBooking,
            formatDate,
            formatTime,
            getStatusClass
        };
    }
}).mount('#admin-dashboard');
</script>
<?= $this->endSection() ?>
