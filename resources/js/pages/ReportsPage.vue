<template>
  <div>
    <h2>📊 Reports & Export Center</h2>
    <p class="text-muted">Generate reports for government, banks, or internal record-keeping.</p>
    <div class="row mt-3">
      <div class="col-md-4" v-for="r in reports" :key="r.id">
        <div class="card shadow-sm mb-3 h-100">
          <div class="card-body">
            <h6>{{ r.name }}</h6>
            <p class="text-muted small">{{ r.description }}</p>
            <button class="btn btn-primary btn-sm" @click="generateReport(r)">Generate</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Grant selector for beneficiaries_by_grant -->
    <div v-if="showGrantPicker" class="card mt-3">
      <div class="card-body">
        <label class="form-label">Select Grant:</label>
        <select v-model="selectedGrantId" class="form-select mb-2">
          <option v-for="g in grants" :key="g.grant_identifier" :value="g.grant_identifier">{{ g.name }} ({{ g.grant_identifier }})</option>
        </select>
        <button class="btn btn-primary btn-sm" @click="runGrantReport">Generate</button>
        <button class="btn btn-secondary btn-sm ms-2" @click="showGrantPicker=false">Cancel</button>
      </div>
    </div>

    <!-- Report Results -->
    <div class="card mt-3 shadow" v-if="reportResult">
      <div class="card-header bg-dark text-white d-flex justify-content-between">
        <h6 class="mb-0">{{ reportResult.title }}</h6>
        <button class="btn btn-sm btn-outline-light" @click="reportResult=null">✕ Close</button>
      </div>
      <div class="card-body">
        <!-- Total count -->
        <div v-if="reportResult.total !== undefined" class="mb-3">
          <span class="badge bg-primary fs-6">Total: {{ reportResult.total }}</span>
        </div>

        <!-- Array data = table -->
        <div v-if="isArrayData" class="table-responsive">
          <table class="table table-sm table-striped table-hover">
            <thead class="table-light">
              <tr><th v-for="key in arrayColumns" :key="key">{{ formatKey(key) }}</th></tr>
            </thead>
            <tbody>
              <tr v-for="(row, i) in reportResult.data" :key="i">
                <td v-for="key in arrayColumns" :key="key">{{ getCellValue(row, key) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Object data (demographic summary, etc) -->
        <div v-else-if="reportResult.data && typeof reportResult.data === 'object'">
          <!-- Total Active -->
          <div v-if="reportResult.data.total_active" class="mb-3">
            <h5>Total Active Members: <span class="badge bg-success fs-5">{{ reportResult.data.total_active }}</span></h5>
          </div>

          <!-- By Gender -->
          <div v-if="reportResult.data.by_gender" class="mb-4">
            <h6>By Gender</h6>
            <div class="row">
              <div class="col-md-3" v-for="g in reportResult.data.by_gender" :key="g.gender">
                <div class="card bg-light text-center py-2">
                  <span class="text-capitalize">{{ g.gender }}</span>
                  <h4 class="mb-0">{{ g.count }}</h4>
                </div>
              </div>
            </div>
          </div>

          <!-- By Village -->
          <div v-if="reportResult.data.by_village && reportResult.data.by_village.length">
            <h6>By Village</h6>
            <table class="table table-sm">
              <thead><tr><th>Village</th><th>Count</th></tr></thead>
              <tbody>
                <tr v-for="v in reportResult.data.by_village" :key="v.village">
                  <td>{{ v.village }}</td><td><strong>{{ v.count }}</strong></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Fallback for other object data -->
          <div v-if="!reportResult.data.total_active && !reportResult.data.by_gender">
            <table class="table table-sm">
              <tbody>
                <tr v-for="(val, key) in reportResult.data" :key="key">
                  <th>{{ formatKey(key) }}</th>
                  <td>{{ typeof val === 'object' ? JSON.stringify(val) : val }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() { return { reports: [], reportResult: null, showGrantPicker: false, grants: [], selectedGrantId: '' }; },
  computed: {
    isArrayData() {
      return Array.isArray(this.reportResult?.data) && this.reportResult.data.length > 0;
    },
    arrayColumns() {
      if (!this.isArrayData) return [];
      const row = this.reportResult.data[0];
      // Get flat keys, skip nested objects
      return Object.keys(row).filter(k => {
        const val = row[k];
        return val === null || typeof val !== 'object';
      });
    },
  },
  async mounted() { const { data } = await axios.get('/reports'); this.reports = data.reports; },
  methods: {
    async generateReport(r) {
      if (r.id === 'beneficiaries_by_grant') {
        const { data } = await axios.get('/grants'); this.grants = data.data || []; this.showGrantPicker = true; return;
      }
      this.showGrantPicker = false;
      try {
        const { data } = await axios.post('/reports/generate', { report_id: r.id });
        this.reportResult = data;
      } catch (e) {
        alert(e.response?.data?.message || 'Error generating report');
      }
    },
    async runGrantReport() {
      this.showGrantPicker = false;
      try {
        const { data } = await axios.post('/reports/generate', { report_id: 'beneficiaries_by_grant', params: { grant_identifier: this.selectedGrantId } });
        this.reportResult = data;
      } catch (e) {
        alert(e.response?.data?.message || 'Error');
      }
    },
    getCellValue(row, key) {
      const val = row[key];
      if (val === null || val === undefined) return '—';
      return val;
    },
    formatKey(k) { return k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); },
  },
};
</script>
