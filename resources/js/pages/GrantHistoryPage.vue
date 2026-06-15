<template>
  <div>
    <h2>📜 Grant History</h2>
    <p class="text-muted">Central view of all grant activities across all grants and beneficiaries.</p>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-2">
            <label class="form-label small">From Date</label>
            <input v-model="filters.date_from" type="date" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label small">To Date</label>
            <input v-model="filters.date_to" type="date" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label small">Grant</label>
            <select v-model="filters.grant_id" class="form-select form-select-sm">
              <option value="">All Grants</option>
              <option v-for="g in grants" :key="g.id" :value="g.grant_identifier">{{ g.name }}</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Action Type</label>
            <select v-model="filters.action_type" class="form-select form-select-sm">
              <option value="">All Actions</option>
              <option value="registration">Registration</option>
              <option value="approval">Approval</option>
              <option value="payment">Payment</option>
              <option value="update">Update</option>
              <option value="cancellation">Cancellation</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small">Payment Method</label>
            <select v-model="filters.payment_method" class="form-select form-select-sm">
              <option value="">All Methods</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="proxy_account">Proxy Account</option>
              <option value="mobile_wallet">Mobile Wallet</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-sm me-2" @click="fetchHistory">Filter</button>
            <button class="btn btn-outline-secondary btn-sm me-2" @click="clearFilters">Clear</button>
            <button class="btn btn-outline-success btn-sm" @click="exportCsv">📥 CSV</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-3">
      <div class="col-md-3">
        <div class="card bg-primary bg-opacity-10">
          <div class="card-body py-2 text-center">
            <small class="text-muted">Total Records</small>
            <h4 class="mb-0">{{ summary.total }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success bg-opacity-10">
          <div class="card-body py-2 text-center">
            <small class="text-muted">Total Payments</small>
            <h4 class="mb-0">{{ summary.payments }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-info bg-opacity-10">
          <div class="card-body py-2 text-center">
            <small class="text-muted">Total Amount Disbursed</small>
            <h4 class="mb-0">₦{{ summary.total_amount }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning bg-opacity-10">
          <div class="card-body py-2 text-center">
            <small class="text-muted">Cancellations</small>
            <h4 class="mb-0">{{ summary.cancellations }}</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- History Table -->
    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-dark">
              <tr>
                <th>History ID</th>
                <th>Date</th>
                <th>Grant</th>
                <th>Action</th>
                <th>Villager</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Txn Reference</th>
                <th>Performed By</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!history.length">
                <td colspan="10" class="text-center text-muted py-4">No grant history records found</td>
              </tr>
              <tr v-for="h in history" :key="h.id">
                <td><code class="small">{{ h.history_id }}</code></td>
                <td><small>{{ formatDate(h.action_date) }}</small></td>
                <td><span class="badge bg-light text-dark">{{ h.grant?.grant_identifier }}</span></td>
                <td><span :class="actionBadge(h.action_type)">{{ h.action_type }}</span></td>
                <td>
                  <span v-if="h.villager_record">
                    {{ h.villager_record.full_name }}
                    <br><code class="small text-muted">{{ h.villager_record.unique_id }}</code>
                  </span>
                  <span v-else class="text-muted">—</span>
                </td>
                <td>{{ h.amount ? '₦' + Number(h.amount).toLocaleString() : '—' }}</td>
                <td><small class="text-capitalize">{{ h.payment_method?.replace('_', ' ') || '—' }}</small></td>
                <td><code class="small">{{ h.transaction_reference || '—' }}</code></td>
                <td>{{ h.performed_by_user?.name || '—' }}</td>
                <td><small>{{ h.remarks || '—' }}</small></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <nav class="mt-3" v-if="pagination.last_page > 1">
      <ul class="pagination pagination-sm justify-content-center">
        <li class="page-item" :class="{disabled: pagination.current_page === 1}">
          <button class="page-link" @click="fetchHistory(pagination.current_page - 1)">← Previous</button>
        </li>
        <li class="page-item disabled">
          <span class="page-link">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        </li>
        <li class="page-item" :class="{disabled: pagination.current_page >= pagination.last_page}">
          <button class="page-link" @click="fetchHistory(pagination.current_page + 1)">Next →</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      history: [],
      grants: [],
      filters: { date_from: '', date_to: '', grant_id: '', action_type: '', payment_method: '' },
      pagination: {},
      summary: { total: 0, payments: 0, total_amount: '0', cancellations: 0 },
    };
  },
  async mounted() {
    await this.fetchGrants();
    await this.fetchHistory();
  },
  methods: {
    async fetchGrants() {
      try {
        const { data } = await axios.get('/grants');
        this.grants = data.data || [];
      } catch (e) {}
    },
    async fetchHistory(page = 1) {
      try {
        const params = { page, ...this.filters };
        // Remove empty filters
        Object.keys(params).forEach(k => { if (!params[k]) delete params[k]; });
        const { data } = await axios.get('/grants/history/all', { params });
        this.history = data.data || [];
        this.pagination = { current_page: data.current_page, last_page: data.last_page };
        this.summary = data.summary || { total: 0, payments: 0, total_amount: '0', cancellations: 0 };
      } catch (e) {
        this.history = [];
      }
    },
    clearFilters() {
      this.filters = { date_from: '', date_to: '', grant_id: '', action_type: '', payment_method: '' };
      this.fetchHistory();
    },
    exportCsv() {
      const params = new URLSearchParams(this.filters);
      window.open(`/api/grants/history/export?${params.toString()}`, '_blank');
    },
    actionBadge(type) {
      const map = {
        registration: 'badge bg-primary',
        approval: 'badge bg-success',
        payment: 'badge bg-info',
        update: 'badge bg-warning text-dark',
        cancellation: 'badge bg-danger',
      };
      return map[type] || 'badge bg-secondary';
    },
    formatDate(d) { return d ? new Date(d).toLocaleString() : '—'; },
  },
};
</script>
