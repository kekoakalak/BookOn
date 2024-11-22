import ChartComponent from '../../../components/AnalyticsChartComponent.vue';
import { ref } from "vue";
import { api } from '../../../boot/axios';

export default {
  components: {
    ChartComponent,
  },
  data() {
    return {
      services: [],
      pendingAppointments: 0,
      completedAppointments: 0,
      cancelledAppointments: 0,
      pendingData: [],       // Data for Ongoing Appointments
      completedData: [],     // Data for Completed Appointments
      cancelledData: [],     // Data for Cancelled Appointments
      showFilter: false,
      showGenerateReportDialog: false, // This controls the visibility of the dialog
      filter: {
        startDate: null,
        endDate: null,
      },
      showStartDatePicker: false,
      showEndDatePicker: false,
      providerId: null, // Store provider_id here
    };
  },
  mounted() {
    this.providerId = this.getProviderId();
    console.log("Provider ID on mount:", this.providerId);

    if (this.providerId) {
      this.fetchServices();
    } else {
      this.handleProviderIdError();
    }
  },

  methods: {
    getProviderId() {
      return sessionStorage.getItem('provider_id');
    },

    handleProviderIdError() {
      console.error('Provider ID not found. Unable to fetch services.');
    },

    async fetchServices() {
      if (!this.providerId) {
        console.error('Provider ID is required to fetch services.');
        return;
      }

      try {
        const params = new URLSearchParams();
        params.append('providerId', this.providerId);
        if (this.filter.startDate) params.append('startDate', this.filter.startDate);
        if (this.filter.endDate) params.append('endDate', this.filter.endDate);

        const response = await api.get(`/services/read_all.php?${params.toString()}`);
        console.log("API response data:", response.data);

        if (response.data && response.data.length) {
          this.services = response.data;
          this.processServiceData(response.data);
        } else {
          this.services = [];  // Clear services if no data is returned
          console.warn('No data returned from the API');
        }
      } catch (error) {
        console.error('Error fetching services:', error);
        this.services = [];  // Clear services on error
      }
    },

    openGenerateReportDialog() {
      this.showGenerateReportDialog = true;
    },
    generateReport() {
      const reportContent = `
        <html>
          <head>
            <title>Analytics Report</title>
            <style>
              body {
                font-family: Arial, sans-serif;
                background-color: #ebebeb;
                color: #333;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
              }
              .q-card {
                border-radius: 16px;
                padding: 16px;
                margin: 20px auto;
                max-width: 600px;
                background-color: white;
                box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
              }
              .q-card-section {
                margin-bottom: 20px;
              }
              .text-h6 {
                font-size: 1.25em;
                font-weight: bold;
              }
              .text-caption {
                font-size: 0.9em;
                color: #666;
              }
              .q-list {
                border-top: 1px solid #ddd;
              }
              .q-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: 1px solid #249990;
                border-radius: 8px;
                margin-bottom: 10px;
              }
              .q-badge {
                font-size: 1.2em;
                font-weight: bold;
                background-color: white;
                color: black;
                padding: 3px 10px;
                border-radius: 5px;
              }
              .chart-container {
                position: relative;
                margin: 20px auto;
                width: 100%;
                max-width: 400px;
              }

              /* Mobile responsive styles */
              @media (max-width: 768px) {
                body {
                  padding: 10px;
                }
                .q-card {
                  max-width: 100%;
                  padding: 12px;
                }
                .q-card-section {
                  margin-bottom: 15px;
                }
                .text-h6 {
                  font-size: 1.1em;
                }
                .q-item {
                  font-size: 0.9em;
                  padding: 10px;
                }
                .chart-container {
                  max-width: 100%;
                }
              }
            </style>
          </head>
          <body>
            <h1 style="text-align:center; color:#333;">Analytics Report</h1>

            <div class="q-card">
              <div class="q-card-section">
                <div class="text-h6">Services</div>
              </div>
              <div class="q-list">
                ${this.services.map(service => `
                  <div class="q-item">
                    <strong>${service.name}</strong>
                    <strong class="q-badge">${service.total}</strong>
                  </div>
                `).join('')}
              </div>
            </div>

            <div class="q-card">
              <div class="q-card-section flex-between">
                <div class="text-h6">Ongoing Appointments</div>
                <div class="text-caption">${this.pendingAppointments} totals</div>
              </div>
              <div class="q-card-section chart-container">
                <canvas id="ongoingChart"></canvas>
              </div>
            </div>

            <div class="q-card">
              <div class="q-card-section flex-between">
                <div class="text-h6">Completed Appointments</div>
                <div class="text-caption">${this.completedAppointments} totals</div>
              </div>
              <div class="q-card-section chart-container">
                <canvas id="completedChart"></canvas>
              </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
              // Data for the charts
              const pendingData = ${JSON.stringify(this.pendingData)};
              const completedData = ${JSON.stringify(this.completedData)};

              // Function to initialize a chart with lines
              function createChart(ctx, data, label) {
                new Chart(ctx, {
                  type: 'line', // Ensure it's a line chart
                  data: {
                    labels: data.map(item => item.name), // Use service name as labels
                    datasets: [{
                      label: label,
                      data: data.map(item => item.value), // Appointment counts as data
                      borderColor: '#249990', // Line color
                      backgroundColor: 'rgba(36, 153, 144, 0.2)', // Background fill color
                      fill: true, // Fill area under the line
                      tension: 0.4, // Smooth lines (curved)
                    }]
                  },
                  options: {
                    responsive: true,
                    scales: {
                      x: {
                        title: {
                          display: true,
                          text: 'Services'
                        }
                      },
                      y: {
                        title: {
                          display: true,
                          text: 'Appointments Count'
                        },
                        beginAtZero: true
                      }
                    }
                  }
                });
              }

              // Render the charts after DOM loads
              document.addEventListener('DOMContentLoaded', function() {
                const ongoingCtx = document.getElementById('ongoingChart').getContext('2d');
                const completedCtx = document.getElementById('completedChart').getContext('2d');

                createChart(ongoingCtx, pendingData, 'Ongoing Appointments');
                createChart(completedCtx, completedData, 'Completed Appointments');
              });
            </script>
          </body>
        </html>
      `;

      const blob = new Blob([reportContent], { type: 'text/html' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = "AnalyticsReport.html";
      link.click();
      URL.revokeObjectURL(url);

      this.$q.notify({
        message: 'Report generated and downloaded',
        color: 'positive',
        position: 'top',
      });

      this.showGenerateReportDialog = false;
    },



    // Helper function to generate chart image from data
    async generateChartImage(appointmentData) {
      // Generate the chart image (you might use html2canvas, Chart.js, or other methods)
      // This is a placeholder and should be replaced with actual chart image generation logic
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      canvas.width = 400;
      canvas.height = 200;

      // Draw a simple line as a placeholder for the chart
      ctx.beginPath();
      ctx.moveTo(20, 100);
      ctx.lineTo(380, 100);
      ctx.strokeStyle = '#00aaff';
      ctx.lineWidth = 3;
      ctx.stroke();

      return canvas.toDataURL();
    },



    processServiceData(data) {
      // Reset totals and data arrays for charts
      this.pendingAppointments = 0;
      this.completedAppointments = 0;
      this.cancelledAppointments = 0;
      this.pendingData = [];
      this.completedData = [];
      this.cancelledData = [];

      // Process data to populate counts and chart data
      data.forEach(service => {
        const pendingCount = parseInt(service.Pending, 10) || 0;
        const completedCount = parseInt(service.Completed, 10) || 0;
        const cancelledCount = parseInt(service.Cancelled, 10) || 0;

        // Update total counts
        this.pendingAppointments += pendingCount;
        this.completedAppointments += completedCount;
        this.cancelledAppointments += cancelledCount;

        // Push data to respective arrays for chart visualization
        if (pendingCount > 0) {
          this.pendingData.push({ name: service.name, value: pendingCount });
        }
        if (completedCount > 0) {
          this.completedData.push({ name: service.name, value: completedCount });
        }
        if (cancelledCount > 0) {
          this.cancelledData.push({ name: service.name, value: cancelledCount });
        }
      });
    },

    applyFilter() {
      this.fetchServices();
      console.log('Filter applied with:', this.filter.startDate, this.filter.endDate);
      this.showFilter = false;
    },

    applyPreset(type) {
      const now = new Date();
      switch (type) {
        case 'allTime':
          this.filter.startDate = null;
          this.filter.endDate = null;
          break;
        case 'today':
          this.filter.startDate = this.formatDate(now);
          this.filter.endDate = this.formatDate(now);
          break;
        case 'yesterday':
          now.setDate(now.getDate() - 1);
          this.filter.startDate = this.formatDate(now);
          this.filter.endDate = this.formatDate(now);
          break;
        case 'past7':
          this.filter.startDate = this.formatDate(new Date(now.setDate(now.getDate() - 6)));
          this.filter.endDate = this.formatDate(new Date());
          break;
        case 'past30':
          this.filter.startDate = this.formatDate(new Date(now.setDate(now.getDate() - 29)));
          this.filter.endDate = this.formatDate(new Date());
          break;
      }
      this.fetchServices();
    },

    openStartDatePicker() {
      this.showFilter = false;
      this.showStartDatePicker = true;
    },

    openEndDatePicker() {
      this.showFilter = false;
      this.showEndDatePicker = true;
    },

    saveStartDate(date) {
      this.filter.startDate = date;
      this.showStartDatePicker = false;
      this.showFilter = true;

      console.log('Start date saved:', this.filter.startDate);
    },

    saveEndDate(date) {
      this.filter.endDate = date;
      this.showEndDatePicker = false;
      this.showFilter = true;

      console.log('End date saved:', this.filter.endDate);
    },

    formatDate(date) {
      const d = new Date(date);
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      const year = d.getFullYear();
      return `${year}-${month}-${day}`;
    },
  },
};
