<template>
  <div>
    <h2>Audit Log</h2>
    <div class="card mt-3">
      <div class="card-header">
        <div class="row g-2">
          <div class="col-md-2">
            <input v-model="filters.date_from" type="date" class="form-control form-control-sm" placeholder="From">
          </div>
          <div class="col-md-2">
            <input v-model="filters.date_to" type="date" class="form-control form-control-sm" placeholder="To">
          </div>
          <div class="col-md-2">
            <select v-model="filters.event_type" class="form-select form-select-sm">
              <option value="">All Events</option>
              <option value="registration">Registration</option>
              <option value="update">Update</option>
              <option value="status_change">Status Change</option>
              <option value="grant_approved">Grant Approved</option>
              <option value="login">Login</option>
              <option value="logout">Logout</option>
              <option value="access_denied">Access Denied</option>
            </select>
          </div>
          <div class="col-md-2">
            <input v-model="filters.villager_id" type="text" class="form-control form-control-sm" placeholder="Villager ID">
          </div>
          <div class="col-md-2">
            <button class="btn btn-sm btn-primary" @click="fetchLogs">Filter</button>
            <button class="btn btn-sm btn-outline-secondary ms-1" @click="exportCsv">Export CSV</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
          <thead><tr><th>Timestamp</th><th>Event</th><th>User</th><th>Villager</th><th>Description</th></tr></thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id">
              <td><small>{{ formatDate(log.event_timestamp) }}</small></td>
              <td><span class="badge bg-info">{{ log.event_type }}</span></td>
              <td>{{ log.user_id }}</td>
              <td><code>{{ log.affected_villager_id || '-' }}</code></td>
              <td><small>{{ log.description }}</small></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <nav class="mt-2" v-if="pagination.last_page > 1">
      <ul class="pagination pagination-sm">
        <li class="page-item" :class="{disabled: pagination.current_page === 1}">
          <button class="page-link" @click="fetchLogs(pagination.current_page - 1)">Prev</button>
        </li>
        <li class="page-item" :class="{disabled: pagination.current_page >= pagination.last_page}">
          <button class="page-link" @click="fetchLogs(pagination.current_page + 1)">Next</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() { return { logs: [], filters: {}, pagination: {} }; },
  mounted() { this.fetchLogs(); },
  methods: {
    async fetchLogs(page = 1) {
      const params = { ...this.filters, page };
      const { data } = await axios.get('/audit', { params });
      this.logs = data.data;
      this.pagination = { current_page: data.current_page, last_page: data.last_page };
    },
    async exportCsv() {
      const params = new URLSearchParams(this.filters).toString();
      window.open(`/api/audit/export?${params}`, '_blank');
    },
    formatDate(d) { return d ? new Date(d).toLocaleString() : ''; },
  },
};
</script>
