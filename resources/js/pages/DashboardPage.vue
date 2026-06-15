<template>
  <div>
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-0">Welcome back, {{ user?.name }} 👋</h2>
        <p class="text-muted mb-0">Aleto Clan Community Portal — Dashboard Overview</p>
      </div>
      <div class="text-end">
        <small class="text-muted">{{ currentDate }}</small>
        <br><span class="badge bg-primary">{{ user?.role?.replace('_', ' ') }}</span>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4" v-if="isAdmin">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">Total Members</p>
                <h3 class="mb-0">{{ stats.total_villagers || 0 }}</h3>
              </div>
              <div class="text-primary fs-1 opacity-50">👥</div>
            </div>
            <small class="text-success">{{ stats.active_villagers || 0 }} active</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">Active Grants</p>
                <h3 class="mb-0">{{ stats.active_grants || 0 }}</h3>
              </div>
              <div class="text-success fs-1 opacity-50">💰</div>
            </div>
            <small class="text-muted">{{ stats.total_grants || 0 }} total</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">Total Disbursed</p>
                <h3 class="mb-0">₦{{ formatMoney(stats.total_disbursed) }}</h3>
              </div>
              <div class="text-info fs-1 opacity-50">📊</div>
            </div>
            <small class="text-muted">All time payments</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <p class="text-muted small mb-1">Pending Actions</p>
                <h3 class="mb-0">{{ pendingTotal }}</h3>
              </div>
              <div class="text-warning fs-1 opacity-50">⚠️</div>
            </div>
            <small class="text-danger" v-if="stats.pending_flags">{{ stats.pending_flags }} flagged registrations</small>
            <small class="text-muted" v-else>All clear</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Secondary Stats Row -->
    <div class="row g-3 mb-4" v-if="isAdmin">
      <div class="col-md-2">
        <div class="card border-0 bg-light text-center py-2">
          <small class="text-muted">Projects</small>
          <h5 class="mb-0">{{ stats.total_projects || 0 }}</h5>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 bg-light text-center py-2">
          <small class="text-muted">Ongoing</small>
          <h5 class="mb-0 text-primary">{{ stats.ongoing_projects || 0 }}</h5>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 bg-light text-center py-2">
          <small class="text-muted">Deceased</small>
          <h5 class="mb-0 text-secondary">{{ stats.deceased_villagers || 0 }}</h5>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 bg-light text-center py-2">
          <small class="text-muted">Archived</small>
          <h5 class="mb-0 text-warning">{{ stats.archived_villagers || 0 }}</h5>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 bg-light text-center py-2">
          <small class="text-muted">Scholarships</small>
          <h5 class="mb-0 text-info">{{ stats.pending_scholarships || 0 }} pending</h5>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 bg-light text-center py-2">
          <small class="text-muted">Health Grants</small>
          <h5 class="mb-0 text-success">{{ stats.pending_health_grants || 0 }} pending</h5>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4" v-if="isAdmin">
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-white"><h6 class="mb-0">Gender Distribution</h6></div>
          <div class="card-body d-flex align-items-center justify-content-center">
            <canvas ref="genderChart" style="max-height: 200px;"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-white"><h6 class="mb-0">Registration Trend (Last 6 Months)</h6></div>
          <div class="card-body">
            <canvas ref="trendChart" style="max-height: 200px;"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row g-3" v-if="isAdmin">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between">
            <h6 class="mb-0">Recent Registrations</h6>
            <router-link to="/villagers" class="btn btn-sm btn-outline-primary">View All</router-link>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <div v-for="r in recentRegistrations" :key="r.id" class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>{{ r.full_name }}</strong>
                  <br><small class="text-muted"><code>{{ r.unique_id }}</code> — by {{ r.registered_by_user?.name }}</small>
                </div>
                <div class="text-end">
                  <span :class="'badge bg-' + (r.status === 'active' ? 'success' : 'secondary')">{{ r.status }}</span>
                  <br><small class="text-muted">{{ timeAgo(r.created_at) }}</small>
                </div>
              </div>
              <div v-if="!recentRegistrations.length" class="list-group-item text-center text-muted py-3">No registrations yet</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between">
            <h6 class="mb-0">Recent Grant Activity</h6>
            <router-link to="/grants/history" class="btn btn-sm btn-outline-primary">View All</router-link>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <div v-for="a in recentGrantActivity" :key="a.id" class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span :class="actionBadge(a.action_type)">{{ a.action_type }}</span>
                  <span class="ms-2">{{ a.grant?.name || '—' }}</span>
                  <br><small class="text-muted">by {{ a.performed_by_user?.name }} {{ a.amount ? '— ₦' + Number(a.amount).toLocaleString() : '' }}</small>
                </div>
                <small class="text-muted">{{ timeAgo(a.action_date) }}</small>
              </div>
              <div v-if="!recentGrantActivity.length" class="list-group-item text-center text-muted py-3">No grant activity yet</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Non-Admin View -->
    <div v-if="!isAdmin" class="row justify-content-center mt-4">
      <div class="col-md-6">
        <div class="card shadow-sm text-center py-5">
          <div class="card-body">
            <div class="fs-1 mb-3">🏘️</div>
            <h4>Welcome, {{ user?.name }}</h4>
            <p class="text-muted">Role: <strong class="text-capitalize">{{ user?.role?.replace('_', ' ') }}</strong></p>
            <div class="mt-4">
              <router-link v-if="isAuditor" to="/audit" class="btn btn-primary">View Audit Log</router-link>
              <router-link v-if="isGovOfficial" to="/grants/history" class="btn btn-primary">View Grant Reports</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user') || 'null'),
      stats: {},
      charts: {},
      recentRegistrations: [],
      recentGrantActivity: [],
    };
  },
  computed: {
    isAdmin() { return this.user?.role === 'admin'; },
    isAuditor() { return this.user?.role === 'auditor'; },
    isGovOfficial() { return this.user?.role === 'government_official'; },
    currentDate() { return new Date().toLocaleDateString('en-NG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }); },
    pendingTotal() { return (this.stats.pending_flags || 0) + (this.stats.pending_scholarships || 0) + (this.stats.pending_health_grants || 0); },
  },
  async mounted() {
    if (this.isAdmin) {
      try {
        const { data } = await axios.get('/dashboard/stats');
        this.stats = data.stats;
        this.charts = data.charts;
        this.recentRegistrations = data.recent_registrations;
        this.recentGrantActivity = data.recent_grant_activity;
        this.$nextTick(() => {
          this.renderGenderChart();
          this.renderTrendChart();
        });
      } catch (e) { console.error(e); }
    }
  },
  methods: {
    formatMoney(val) {
      if (!val) return '0';
      return Number(val).toLocaleString();
    },
    timeAgo(date) {
      if (!date) return '';
      const now = new Date();
      const d = new Date(date);
      const diff = Math.floor((now - d) / 1000);
      if (diff < 60) return 'just now';
      if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
      if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
      return Math.floor(diff / 86400) + 'd ago';
    },
    actionBadge(type) {
      const map = { registration: 'badge bg-primary', approval: 'badge bg-success', payment: 'badge bg-info', update: 'badge bg-warning text-dark', cancellation: 'badge bg-danger' };
      return map[type] || 'badge bg-secondary';
    },
    renderGenderChart() {
      const ctx = this.$refs.genderChart;
      if (!ctx) return;
      const data = this.charts.gender_distribution || {};
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: Object.keys(data).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
          datasets: [{
            data: Object.values(data),
            backgroundColor: ['#0d6efd', '#e91e8e', '#6c757d'],
            borderWidth: 0,
          }],
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
      });
    },
    renderTrendChart() {
      const ctx = this.$refs.trendChart;
      if (!ctx) return;
      const data = this.charts.registration_trend || {};
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: Object.keys(data),
          datasets: [{
            label: 'New Registrations',
            data: Object.values(data),
            backgroundColor: '#0d6efd',
            borderRadius: 6,
          }],
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
          plugins: { legend: { display: false } },
        },
      });
    },
  },
};
</script>
