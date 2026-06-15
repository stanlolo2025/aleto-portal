<template>
  <div>
    <h2>🏠 Household Management</h2>
    <div class="row g-2 mb-3 mt-3">
      <div class="col-md-3"><input v-model="search" class="form-control" placeholder="Search household ID..." @input="fetch"></div>
      <div class="col-md-2"><select v-model="village" class="form-select" @change="fetch"><option value="">All Villages</option><option v-for="v in villages" :key="v" :value="v">{{ v }}</option></select></div>
      <div class="col-md-2"><select v-model="ward" class="form-select" @change="fetch"><option value="">All Wards</option><option v-for="w in wards" :key="w" :value="w">{{ w }}</option></select></div>
    </div>

    <!-- Household Detail -->
    <div v-if="selectedHousehold" class="card shadow mb-3">
      <div class="card-header bg-primary text-white d-flex justify-content-between">
        <h6 class="mb-0">Household: {{ selectedHousehold }}</h6>
        <button class="btn btn-sm btn-outline-light" @click="selectedHousehold=null">✕ Close</button>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-3"><div class="bg-light p-2 rounded text-center"><small class="text-muted">Total</small><h4>{{ householdStats.total_members }}</h4></div></div>
          <div class="col-md-3"><div class="bg-light p-2 rounded text-center"><small class="text-muted">Active</small><h4 class="text-success">{{ householdStats.active }}</h4></div></div>
          <div class="col-md-3"><div class="bg-light p-2 rounded text-center"><small class="text-muted">Deceased</small><h4 class="text-secondary">{{ householdStats.deceased }}</h4></div></div>
          <div class="col-md-3"><div class="bg-light p-2 rounded text-center"><small class="text-muted">Archived</small><h4 class="text-warning">{{ householdStats.archived }}</h4></div></div>
        </div>
        <table class="table table-sm"><thead><tr><th>Name</th><th>ID</th><th>Gender</th><th>Status</th><th>Action</th></tr></thead><tbody>
          <tr v-for="m in householdMembers" :key="m.id"><td>{{ m.full_name }}</td><td><code>{{ m.unique_id }}</code></td><td>{{ m.gender }}</td><td><span :class="'badge bg-'+(m.status==='active'?'success':'secondary')">{{ m.status }}</span></td><td><router-link :to="'/villagers/'+m.unique_id" class="btn btn-sm btn-outline-primary">View</router-link></td></tr>
        </tbody></table>
      </div>
    </div>

    <!-- Households List -->
    <div class="card" v-if="!selectedHousehold">
      <div class="card-body p-0">
        <table class="table table-hover mb-0"><thead class="table-dark"><tr><th>Household ID</th><th>Village</th><th>Ward</th><th>Members</th><th>Active</th><th>Actions</th></tr></thead><tbody>
          <tr v-for="h in households" :key="h.household_id"><td><code>{{ h.household_id }}</code></td><td>{{ h.village || '—' }}</td><td>{{ h.ward || '—' }}</td><td>{{ h.member_count }}</td><td><span class="badge bg-success">{{ h.active_count }}</span></td><td><button class="btn btn-sm btn-outline-primary" @click="viewHousehold(h.household_id)">View Members</button></td></tr>
        </tbody></table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() { return { households: [], villages: [], wards: [], search: '', village: '', ward: '', selectedHousehold: null, householdMembers: [], householdStats: {} }; },
  async mounted() { await this.fetch(); await this.fetchLocations(); },
  methods: {
    async fetch() { const { data } = await axios.get('/households', { params: { search: this.search, village: this.village, ward: this.ward } }); this.households = data.data || []; },
    async fetchLocations() { const { data } = await axios.get('/households/locations'); this.villages = data.villages; this.wards = data.wards; },
    async viewHousehold(id) { this.selectedHousehold = id; const { data } = await axios.get(`/households/${id}`); this.householdMembers = data.members; this.householdStats = data.stats; },
  },
};
</script>
