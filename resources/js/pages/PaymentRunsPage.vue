<template>
  <div>
    <h2>💳 Payment Runs</h2>
    <p class="text-muted">Track and confirm payments to approved beneficiaries.</p>

    <!-- Create New Run -->
    <div class="card mt-3" v-if="showCreate">
      <div class="card-header bg-primary text-white"><h6 class="mb-0">Create Payment Run</h6></div>
      <div class="card-body">
        <label class="form-label">Select an Approved Beneficiary List</label>
        <select v-model="selectedListId" class="form-select mb-3">
          <option value="">— Select —</option>
          <option v-for="l in approvedLists" :key="l.id" :value="l.id">{{ l.grant?.name }} — List #{{ l.id }} ({{ l.items_count }} beneficiaries)</option>
        </select>
        <button class="btn btn-success" :disabled="!selectedListId" @click="createRun">Create Payment Run</button>
        <button class="btn btn-secondary ms-2" @click="showCreate=false">Cancel</button>
      </div>
    </div>

    <!-- Runs List -->
    <div class="card mt-3" v-if="!selectedRun">
      <div class="card-header d-flex justify-content-between"><strong>All Payment Runs</strong><button class="btn btn-primary btn-sm" @click="showCreate=!showCreate">+ New Payment Run</button></div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0"><thead class="table-dark"><tr><th>Run ID</th><th>Grant</th><th>Total</th><th>Paid</th><th>Failed</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead><tbody>
          <tr v-for="r in runs" :key="r.id"><td><code>{{ r.run_id }}</code></td><td>{{ r.beneficiary_list?.grant?.name }}</td><td>{{ r.total_beneficiaries }}</td><td class="text-success">{{ r.paid_count }}</td><td class="text-danger">{{ r.failed_count }}</td><td>₦{{ Number(r.total_amount).toLocaleString() }}</td><td><span :class="runBadge(r.status)">{{ r.status }}</span></td><td><button class="btn btn-sm btn-outline-primary" @click="viewRun(r)">Manage</button></td></tr>
        </tbody></table>
      </div>
    </div>

    <!-- Run Detail -->
    <div v-if="selectedRun" class="mt-3">
      <div class="d-flex justify-content-between mb-3"><h5>Payment Run: {{ selectedRun.run_id }}</h5><button class="btn btn-outline-secondary btn-sm" @click="selectedRun=null">← Back</button></div>
      <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card bg-light text-center p-2"><small>Total</small><h4>{{ selectedRun.total_beneficiaries }}</h4></div></div>
        <div class="col-md-3"><div class="card bg-success bg-opacity-10 text-center p-2"><small>Paid</small><h4 class="text-success">{{ selectedRun.paid_count }}</h4></div></div>
        <div class="col-md-3"><div class="card bg-danger bg-opacity-10 text-center p-2"><small>Failed</small><h4 class="text-danger">{{ selectedRun.failed_count }}</h4></div></div>
        <div class="col-md-3"><div class="card bg-info bg-opacity-10 text-center p-2"><small>Paid Amount</small><h4>₦{{ Number(selectedRun.paid_amount).toLocaleString() }}</h4></div></div>
      </div>
      <div class="table-responsive"><table class="table table-sm"><thead class="table-dark"><tr><th>Villager</th><th>Amount</th><th>Bank</th><th>Account</th><th>Status</th><th>Txn Ref</th><th>Actions</th></tr></thead><tbody>
        <tr v-for="item in runItems" :key="item.id" :class="{'table-success':item.status==='paid','table-danger':item.status==='failed'}">
          <td>{{ item.villager_record?.full_name }}</td><td>₦{{ Number(item.amount).toLocaleString() }}</td><td>{{ item.bank_name||'—' }}</td><td>{{ item.account_number||'—' }}</td>
          <td><span :class="itemBadge(item.status)">{{ item.status }}</span></td><td><code>{{ item.transaction_reference||'—' }}</code></td>
          <td v-if="item.status==='pending'">
            <button class="btn btn-success btn-sm me-1" @click="markPaid(item)">✓ Paid</button>
            <button class="btn btn-danger btn-sm" @click="markFailed(item)">✕ Failed</button>
          </td><td v-else>—</td>
        </tr>
      </tbody></table></div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() { return { runs: [], approvedLists: [], showCreate: false, selectedListId: '', selectedRun: null, runItems: [] }; },
  async mounted() { await this.fetchRuns(); await this.fetchApprovedLists(); },
  methods: {
    async fetchRuns() { const { data } = await axios.get('/payment-runs'); this.runs = data.data || []; },
    async fetchApprovedLists() {
      const { data } = await axios.get('/grants');
      const grants = data.data || [];
      this.approvedLists = [];
      for (const g of grants) {
        try { const r = await axios.get(`/grants/${g.grant_identifier}/beneficiaries/lists`);
          (r.data.data||[]).filter(l=>l.status==='approved').forEach(l=>this.approvedLists.push({...l, grant: g}));
        } catch(e){}
      }
    },
    async createRun() { try { await axios.post('/payment-runs', { beneficiary_list_id: this.selectedListId }); alert('Payment run created!'); this.showCreate=false; this.fetchRuns(); } catch(e) { alert(e.response?.data?.message||'Error'); } },
    async viewRun(r) { const { data } = await axios.get(`/payment-runs/${r.run_id}`); this.selectedRun = data.data; this.runItems = data.data.items||[]; },
    async markPaid(item) { const ref = prompt('Enter transaction reference:'); if (!ref) return; await axios.post(`/payment-runs/items/${item.id}/paid`, { transaction_reference: ref }); this.viewRun(this.selectedRun); },
    async markFailed(item) { const reason = prompt('Enter failure reason:'); if (!reason) return; await axios.post(`/payment-runs/items/${item.id}/failed`, { failure_reason: reason }); this.viewRun(this.selectedRun); },
    runBadge(s) { return { pending:'badge bg-secondary', in_progress:'badge bg-warning', completed:'badge bg-success', failed:'badge bg-danger' }[s]; },
    itemBadge(s) { return { pending:'badge bg-secondary', paid:'badge bg-success', failed:'badge bg-danger' }[s]; },
  },
};
</script>
