<template>
  <div>
    <h2>Grant Management</h2>

    <!-- Create Grant Form -->
    <div class="card mt-3" v-if="showCreateForm">
      <div class="card-header bg-primary text-white">
        <h6 class="mb-0">Create New Grant</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="createGrant">
          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">Grant ID <span class="text-danger">*</span></label>
              <input v-model="newGrant.grant_identifier" class="form-control" placeholder="e.g. GRT-2026-001" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Grant Name <span class="text-danger">*</span></label>
              <input v-model="newGrant.name" class="form-control" placeholder="e.g. Community Stipend Q1" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Amount per Beneficiary (₦) <span class="text-danger">*</span></label>
              <input v-model="newGrant.amount" type="number" step="0.01" min="0.01" class="form-control" required>
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Status</label>
              <select v-model="newGrant.status" class="form-select">
                <option value="draft">Draft</option>
                <option value="active">Active</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Description</label>
              <textarea v-model="newGrant.description" class="form-control" rows="2" placeholder="Grant description..."></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-success" :disabled="creating">{{ creating ? 'Creating...' : '✓ Create Grant' }}</button>
          <button type="button" class="btn btn-secondary ms-2" @click="showCreateForm = false">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Grant List -->
    <div class="card mt-3" v-if="!selectedGrant">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>All Grants</strong>
        <button class="btn btn-primary btn-sm" @click="showCreateForm = !showCreateForm">
          {{ showCreateForm ? '✕ Close' : '+ New Grant' }}
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-dark">
              <tr><th>Grant ID</th><th>Name</th><th>Amount</th><th>Status</th><th>Beneficiary Lists</th><th>Created</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr v-if="!grants.length">
                <td colspan="7" class="text-center text-muted py-4">No grants created yet</td>
              </tr>
              <tr v-for="g in grants" :key="g.id">
                <td><code>{{ g.grant_identifier }}</code></td>
                <td>{{ g.name }}</td>
                <td>₦{{ Number(g.amount).toLocaleString() }}</td>
                <td><span :class="statusClass(g.status)">{{ g.status }}</span></td>
                <td><span class="badge bg-info">{{ g.beneficiary_lists_count || 0 }}</span></td>
                <td>{{ formatDate(g.created_at) }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" @click="viewGrant(g)">View</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Grant Detail View -->
    <div v-if="selectedGrant" class="mt-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="mb-0">{{ selectedGrant.name }}</h4>
          <small class="text-muted"><code>{{ selectedGrant.grant_identifier }}</code> — ₦{{ Number(selectedGrant.amount).toLocaleString() }} per beneficiary</small>
        </div>
        <button class="btn btn-outline-secondary" @click="selectedGrant = null; beneficiaryLists = []">← Back to Grants</button>
      </div>

      <div class="row">
        <!-- Grant Info -->
        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary bg-opacity-10"><h6 class="mb-0">Grant Details</h6></div>
            <div class="card-body">
              <p><strong>Status:</strong> <span :class="statusClass(selectedGrant.status)">{{ selectedGrant.status }}</span></p>
              <p><strong>Amount:</strong> ₦{{ Number(selectedGrant.amount).toLocaleString() }}</p>
              <p><strong>Description:</strong> {{ selectedGrant.description || '—' }}</p>
              <p><strong>Created:</strong> {{ formatDate(selectedGrant.created_at) }}</p>
              <p><strong>Beneficiary Lists:</strong> {{ beneficiaryLists.length }}</p>
              <p><strong>Total Beneficiaries:</strong> {{ totalBeneficiaries }}</p>
            </div>
          </div>

          <button class="btn btn-success w-100 mb-3" @click="showBeneficiarySelection = true" v-if="!showBeneficiarySelection">
            + Add Beneficiaries
          </button>
        </div>

        <!-- Beneficiary Lists -->
        <div class="col-md-8">
          <div class="card shadow-sm mb-3" v-for="list in beneficiaryLists" :key="list.id">
            <div class="card-header d-flex justify-content-between align-items-center" :class="listHeaderClass(list.status)">
              <div>
                <h6 class="mb-0">
                  Beneficiary List #{{ list.id }}
                  <span :class="listStatusClass(list.status)" class="ms-2">{{ list.status }}</span>
                </h6>
                <small>Created: {{ formatDate(list.created_at) }} | Items: {{ list.items?.length || 0 }}</small>
              </div>
              <div>
                <button v-if="list.status === 'pending_review'" class="btn btn-success btn-sm me-1" @click="approveList(list)" :disabled="hasUnresolvedFlags(list)">
                  ✓ Approve
                </button>
                <button v-if="list.status === 'approved'" class="btn btn-primary btn-sm me-1" @click="exportList('pdf')">🖨️ Print for Bank</button>
                <button v-if="list.status === 'approved'" class="btn btn-outline-success btn-sm me-1" @click="exportList('csv')">📥 CSV</button>
                <button class="btn btn-outline-secondary btn-sm ms-1" @click="toggleListItems(list)">
                  {{ list.showItems ? 'Hide' : 'Show' }} Details
                </button>
              </div>
            </div>
            <div class="card-body p-0" v-if="list.showItems">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr><th>Unique ID</th><th>Name</th><th>Amount</th><th>Duplicate Flag</th><th>Status</th></tr>
                </thead>
                <tbody>
                  <tr v-for="item in list.items" :key="item.id" :class="{'table-warning': item.duplicate_flagged && !item.reviewed_not_duplicate}">
                    <td><code>{{ item.villager_record?.unique_id }}</code></td>
                    <td>{{ item.villager_record?.full_name }}</td>
                    <td>₦{{ Number(item.grant_amount).toLocaleString() }}</td>
                    <td>
                      <span v-if="item.duplicate_flagged && !item.reviewed_not_duplicate" class="badge bg-danger">⚠️ Flagged</span>
                      <span v-else-if="item.duplicate_flagged && item.reviewed_not_duplicate" class="badge bg-info">Reviewed ✓</span>
                      <span v-else class="badge bg-success">Clear</span>
                    </td>
                    <td>
                      <span v-if="list.status === 'approved'" class="badge bg-success">Approved</span>
                      <span v-else class="badge bg-secondary">{{ list.status }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="list.approved_by" class="p-2 bg-light border-top">
                <small><strong>Approved by:</strong> User #{{ list.approved_by }} on {{ formatDate(list.approved_at) }}</small>
              </div>
            </div>
          </div>

          <div v-if="!beneficiaryLists.length" class="card shadow-sm">
            <div class="card-body text-center text-muted py-4">
              <p class="mb-0">No beneficiary lists yet. Click "Add Beneficiaries" to create one.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Beneficiary Selection Panel -->
      <div class="card mt-3 shadow border-success" v-if="showBeneficiarySelection">
        <div class="card-header bg-success text-white d-flex justify-content-between">
          <h6 class="mb-0">Select Beneficiaries for {{ selectedGrant.name }}</h6>
          <button class="btn btn-sm btn-outline-light" @click="showBeneficiarySelection = false">✕ Close</button>
        </div>
        <div class="card-body">
          <div v-if="!eligible.length" class="text-center text-muted py-3">
            <p>No eligible villagers. All active villagers may already be on an approved list for this grant.</p>
          </div>
          <div v-else>
            <div class="mb-3">
              <input v-model="eligibleSearch" class="form-control" placeholder="Search by name...">
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-sm table-hover">
                <thead class="table-light sticky-top">
                  <tr>
                    <th><input type="checkbox" @change="toggleAll($event)"></th>
                    <th>Name</th>
                    <th>Unique ID</th>
                    <th>Household</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="v in filteredEligible" :key="v.id" :class="{'table-primary': selectedIds.includes(v.id)}">
                    <td><input type="checkbox" v-model="selectedIds" :value="v.id"></td>
                    <td>{{ v.full_name }}</td>
                    <td><code>{{ v.unique_id }}</code></td>
                    <td>{{ v.household_id }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
              <span>Selected: <strong>{{ selectedIds.length }}</strong> of {{ eligible.length }} eligible villagers</span>
              <button class="btn btn-success" @click="createList" :disabled="!selectedIds.length || creatingList">
                {{ creatingList ? 'Creating...' : '✓ Create Beneficiary List (' + selectedIds.length + ')' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Grant History / Activities -->
      <div class="card mt-3 shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0">📜 Grant Activity History</h6>
          <button class="btn btn-sm btn-outline-light" @click="showRecordPayment = !showRecordPayment">
            {{ showRecordPayment ? '✕ Close' : '+ Record Payment' }}
          </button>
        </div>

        <!-- Record Payment Form -->
        <div class="card-body border-bottom" v-if="showRecordPayment">
          <form @submit.prevent="recordHistory">
            <div class="row g-2">
              <div class="col-md-3">
                <label class="form-label small">Action Type *</label>
                <select v-model="historyForm.action_type" class="form-select form-select-sm" required>
                  <option value="payment">Payment</option>
                  <option value="registration">Registration</option>
                  <option value="update">Update</option>
                  <option value="cancellation">Cancellation</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Villager</label>
                <select v-model="historyForm.villager_record_id" class="form-select form-select-sm">
                  <option value="">All / General</option>
                  <option v-for="v in historyVillagers" :key="v.id" :value="v.id">{{ v.full_name }}</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Amount (₦)</label>
                <input v-model="historyForm.amount" type="number" step="0.01" class="form-control form-control-sm">
              </div>
              <div class="col-md-2">
                <label class="form-label small">Payment Method</label>
                <select v-model="historyForm.payment_method" class="form-select form-select-sm">
                  <option value="">N/A</option>
                  <option value="bank_transfer">Bank Transfer</option>
                  <option value="proxy_account">Proxy Account</option>
                  <option value="mobile_wallet">Mobile Wallet</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Transaction Ref</label>
                <input v-model="historyForm.transaction_reference" class="form-control form-control-sm" placeholder="TXN-123...">
              </div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col-md-10">
                <label class="form-label small">Remarks</label>
                <input v-model="historyForm.remarks" class="form-control form-control-sm" placeholder="Notes about this action...">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
              </div>
            </div>
          </form>
        </div>

        <!-- History Table -->
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>History ID</th>
                  <th>Date</th>
                  <th>Action</th>
                  <th>Villager</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Txn Ref</th>
                  <th>Performed By</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!grantHistory.length">
                  <td colspan="9" class="text-center text-muted py-3">No history records yet</td>
                </tr>
                <tr v-for="h in grantHistory" :key="h.id">
                  <td><code class="small">{{ h.history_id }}</code></td>
                  <td><small>{{ formatDate(h.action_date) }}</small></td>
                  <td><span :class="actionBadge(h.action_type)">{{ h.action_type }}</span></td>
                  <td>{{ h.villager_record?.full_name || '—' }}</td>
                  <td>{{ h.amount ? '₦' + Number(h.amount).toLocaleString() : '—' }}</td>
                  <td><small>{{ h.payment_method?.replace('_', ' ') || '—' }}</small></td>
                  <td><code class="small">{{ h.transaction_reference || '—' }}</code></td>
                  <td>{{ h.performed_by_user?.name || '—' }}</td>
                  <td><small>{{ h.remarks || '—' }}</small></td>
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
  data() {
    return {
      grants: [],
      showCreateForm: false,
      creating: false,
      creatingList: false,
      newGrant: { grant_identifier: '', name: '', description: '', amount: '', status: 'active' },
      selectedGrant: null,
      beneficiaryLists: [],
      showBeneficiarySelection: false,
      eligible: [],
      eligibleSearch: '',
      selectedIds: [],
      grantHistory: [],
      showRecordPayment: false,
      historyForm: { action_type: 'payment', villager_record_id: '', amount: '', payment_method: '', transaction_reference: '', remarks: '' },
      historyVillagers: [],
    };
  },
  computed: {
    totalBeneficiaries() {
      return this.beneficiaryLists.reduce((sum, list) => sum + (list.items?.length || 0), 0);
    },
    filteredEligible() {
      if (!this.eligibleSearch) return this.eligible;
      const s = this.eligibleSearch.toLowerCase();
      return this.eligible.filter(v => v.full_name.toLowerCase().includes(s));
    },
  },
  async mounted() {
    await this.fetchGrants();
  },
  methods: {
    async fetchGrants() {
      const { data } = await axios.get('/grants');
      this.grants = data.data;
    },
    async createGrant() {
      this.creating = true;
      try {
        await axios.post('/grants', this.newGrant);
        alert('Grant created successfully!');
        this.newGrant = { grant_identifier: '', name: '', description: '', amount: '', status: 'active' };
        this.showCreateForm = false;
        await this.fetchGrants();
      } catch (e) {
        alert(e.response?.data?.message || JSON.stringify(e.response?.data?.errors) || 'Error');
      } finally {
        this.creating = false;
      }
    },
    async viewGrant(g) {
      this.selectedGrant = g;
      this.showBeneficiarySelection = false;
      this.showRecordPayment = false;
      await this.fetchBeneficiaryLists(g);
      await this.fetchGrantHistory(g);
      await this.fetchHistoryVillagers(g);
    },
    async fetchBeneficiaryLists(g) {
      try {
        const { data } = await axios.get(`/grants/${g.grant_identifier}/beneficiaries/lists`);
        this.beneficiaryLists = (data.data || []).map(l => ({ ...l, showItems: false }));
      } catch (e) {
        this.beneficiaryLists = [];
      }
    },
    async selectBeneficiaries() {
      this.showBeneficiarySelection = true;
      this.selectedIds = [];
      try {
        const { data } = await axios.get(`/grants/${this.selectedGrant.grant_identifier}/eligible`);
        this.eligible = data.data;
      } catch (e) {
        alert(e.response?.data?.message || 'Error loading eligible villagers');
      }
    },
    toggleAll(e) {
      this.selectedIds = e.target.checked ? this.filteredEligible.map(v => v.id) : [];
    },
    toggleListItems(list) {
      list.showItems = !list.showItems;
    },
    hasUnresolvedFlags(list) {
      return (list.items || []).some(i => i.duplicate_flagged && !i.reviewed_not_duplicate);
    },
    async createList() {
      this.creatingList = true;
      try {
        await axios.post(`/grants/${this.selectedGrant.grant_identifier}/beneficiaries`, {
          villager_ids: this.selectedIds,
          grant_amount: this.selectedGrant.amount,
        });
        alert('Beneficiary list created!');
        this.showBeneficiarySelection = false;
        this.selectedIds = [];
        await this.fetchBeneficiaryLists(this.selectedGrant);
      } catch (e) {
        alert(e.response?.data?.message || 'Error');
      } finally {
        this.creatingList = false;
      }
    },
    async approveList(list) {
      if (!confirm('Approve this beneficiary list? This authorizes stipend distribution.')) return;
      try {
        await axios.post(`/grants/${this.selectedGrant.grant_identifier}/beneficiaries/approve`, { list_id: list.id });
        alert('List approved!');
        await this.fetchBeneficiaryLists(this.selectedGrant);
      } catch (e) {
        alert(e.response?.data?.message || 'Error');
      }
    },
    async exportList(format) {
      const token = localStorage.getItem('auth_token');
      const url = `/api/grants/${this.selectedGrant.grant_identifier}/beneficiaries/export/${format}?token=${token}`;
      window.open(url, '_blank');
    },
    async fetchGrantHistory(g) {
      try {
        const { data } = await axios.get(`/grants/${g.grant_identifier}/history`);
        this.grantHistory = data.data || [];
      } catch (e) {
        this.grantHistory = [];
      }
    },
    async fetchHistoryVillagers(g) {
      // Get villagers associated with this grant's approved lists
      try {
        const lists = this.beneficiaryLists.filter(l => l.status === 'approved');
        const villagers = [];
        lists.forEach(l => {
          (l.items || []).forEach(item => {
            if (item.villager_record && !villagers.find(v => v.id === item.villager_record.id)) {
              villagers.push(item.villager_record);
            }
          });
        });
        this.historyVillagers = villagers;
      } catch (e) {
        this.historyVillagers = [];
      }
    },
    async recordHistory() {
      try {
        await axios.post(`/grants/${this.selectedGrant.grant_identifier}/history`, this.historyForm);
        alert('History recorded successfully!');
        this.historyForm = { action_type: 'payment', villager_record_id: '', amount: '', payment_method: '', transaction_reference: '', remarks: '' };
        await this.fetchGrantHistory(this.selectedGrant);
      } catch (e) {
        alert(e.response?.data?.message || JSON.stringify(e.response?.data?.errors) || 'Error');
      }
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
    statusClass(status) {
      const map = { active: 'badge bg-success', draft: 'badge bg-warning text-dark', completed: 'badge bg-secondary' };
      return map[status] || 'badge bg-secondary';
    },
    listStatusClass(status) {
      const map = { draft: 'badge bg-secondary', pending_review: 'badge bg-warning text-dark', approved: 'badge bg-success', rejected: 'badge bg-danger' };
      return map[status] || 'badge bg-secondary';
    },
    listHeaderClass(status) {
      const map = { approved: 'bg-success bg-opacity-10', pending_review: 'bg-warning bg-opacity-10', rejected: 'bg-danger bg-opacity-10' };
      return map[status] || '';
    },
    formatDate(d) { return d ? new Date(d).toLocaleString() : '—'; },
  },
  watch: {
    showBeneficiarySelection(val) {
      if (val) this.selectBeneficiaries();
    },
  },
};
</script>
