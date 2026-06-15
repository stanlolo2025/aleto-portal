<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Community Registry</h2>
      <router-link to="/villagers/register" class="btn btn-primary">+ Register Villager</router-link>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <input v-model="search" class="form-control" placeholder="Search by name..." @input="fetchVillagers">
      </div>
      <div class="col-md-3">
        <select v-model="statusFilter" class="form-select" @change="fetchVillagers">
          <option value="">All Statuses</option>
          <option value="active">Active</option>
          <option value="deceased">Deceased</option>
          <option value="archived">Archived</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Unique ID</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Village</th>
            <th>Status</th>
            <th>Registration Date</th>
            <th>Registered By</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="v in villagers" :key="v.id">
            <td><code>{{ v.unique_id }}</code></td>
            <td>{{ v.full_name }}</td>
            <td>{{ calcAge(v.date_of_birth) }}</td>
            <td>{{ v.gender }}</td>
            <td>{{ v.village || '—' }}</td>
            <td>
              <span :class="statusBadge(v.status)">{{ v.status }}</span>
            </td>
            <td>{{ formatDate(v.created_at) }}</td>
            <td>{{ v.registered_by_user?.name || '-' }}</td>
            <td>
              <router-link :to="`/villagers/${v.unique_id}`" class="btn btn-sm btn-outline-primary">View</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <nav v-if="pagination.last_page > 1">
      <ul class="pagination">
        <li class="page-item" :class="{disabled: pagination.current_page === 1}">
          <button class="page-link" @click="goToPage(pagination.current_page - 1)">Previous</button>
        </li>
        <li class="page-item" :class="{disabled: pagination.current_page === pagination.last_page}">
          <button class="page-link" @click="goToPage(pagination.current_page + 1)">Next</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return { villagers: [], search: '', statusFilter: '', pagination: {} };
  },
  mounted() { this.fetchVillagers(); },
  methods: {
    async fetchVillagers(page = 1) {
      const params = { page };
      if (this.search) params.search = this.search;
      if (this.statusFilter) params.status = this.statusFilter;
      const { data } = await axios.get('/villagers', { params });
      this.villagers = data.data;
      this.pagination = { current_page: data.current_page, last_page: data.last_page };
    },
    goToPage(page) { this.fetchVillagers(page); },
    statusBadge(status) {
      const map = { active: 'badge bg-success', deceased: 'badge bg-secondary', archived: 'badge bg-warning' };
      return map[status] || 'badge bg-secondary';
    },
    calcAge(dob) {
      if (!dob) return '—';
      const today = new Date();
      const birth = new Date(dob);
      let age = today.getFullYear() - birth.getFullYear();
      const m = today.getMonth() - birth.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
      return age;
    },
    formatDate(d) { return d ? new Date(d).toLocaleDateString() : ''; },
  },
};
</script>
