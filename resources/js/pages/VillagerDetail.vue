<template>
  <div v-if="villager">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div class="d-flex align-items-center">
        <div class="me-3">
          <img v-if="villager.passport_photo" :src="'/storage/' + villager.passport_photo" alt="Passport" style="width:90px; height:110px; object-fit:cover; border:3px solid #1a5276; border-radius:6px;">
          <div v-else style="width:90px; height:110px; background:#e9ecef; border:3px solid #ccc; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#999; font-size:2rem;">👤</div>
        </div>
        <div>
          <h2 class="mb-1">{{ villager.full_name }}</h2>
          <p class="text-muted mb-0">
            <code class="me-3">{{ villager.unique_id }}</code>
            <span :class="statusBadge(villager.status)" class="fs-6">{{ villager.status.toUpperCase() }}</span>
          </p>
        </div>
      </div>
      <div>
        <router-link to="/villagers" class="btn btn-outline-secondary me-2">← Back to Registry</router-link>
        <button class="btn btn-outline-primary" @click="editing = !editing">
          {{ editing ? 'Cancel Edit' : '✏️ Edit' }}
        </button>
      </div>
    </div>

    <div class="row">
      <!-- Left Column: Main Info -->
      <div class="col-lg-8">
        <!-- Personal Information Card -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h6 class="mb-0">👤 Personal Information</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-muted small mb-0">Full Name</label>
                <p class="fw-semibold mb-2">{{ villager.full_name }}</p>
              </div>
              <div class="col-md-3">
                <label class="form-label text-muted small mb-0">Date of Birth</label>
                <p class="fw-semibold mb-2">{{ formatDateShort(villager.date_of_birth) }}</p>
              </div>
              <div class="col-md-3">
                <label class="form-label text-muted small mb-0">Gender</label>
                <p class="fw-semibold mb-2 text-capitalize">{{ villager.gender }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small mb-0">Marital Status</label>
                <p class="fw-semibold mb-2 text-capitalize">{{ villager.marital_status || '—' }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small mb-0">Occupation</label>
                <p class="fw-semibold mb-2">{{ villager.occupation || '—' }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small mb-0">Education Level</label>
                <p class="fw-semibold mb-2 text-capitalize">{{ villager.education_level || '—' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small mb-0">Household ID</label>
                <p class="fw-semibold mb-2"><code>{{ villager.household_id }}</code></p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small mb-0">Health Status</label>
                <p class="fw-semibold mb-2">{{ villager.health_status || '—' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Identity & Verification Card -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-warning bg-opacity-75">
            <h6 class="mb-0">🛡️ Identity & Verification</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label text-muted small mb-0">National ID (NIN)</label>
                <p class="fw-semibold mb-2">
                  {{ villager.nin || '—' }}
                  <span v-if="villager.nin" :class="ninStatusClass" class="ms-2">{{ ninStatusLabel }}</span>
                </p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small mb-0">Biometric Data</label>
                <p class="fw-semibold mb-2">
                  <span v-if="villager.biometric_data" class="badge bg-success">✓ Captured</span>
                  <span v-else class="badge bg-secondary">Not captured</span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact & Financial Card -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-success bg-opacity-75 text-white">
            <h6 class="mb-0">💰 Contact & Financial</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label text-muted small mb-0">Phone Number</label>
                <p class="fw-semibold mb-2">{{ villager.phone_number || '—' }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small mb-0">Email</label>
                <p class="fw-semibold mb-2">{{ villager.email || '—' }}</p>
              </div>
              <div class="col-md-4">
                <label class="form-label text-muted small mb-0">Bank Name</label>
                <p class="fw-semibold mb-2">{{ villager.bank_name || '—' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label text-muted small mb-0">Bank Account Number</label>
                <p class="fw-semibold mb-2">{{ villager.bank_account_number || '—' }}</p>
              </div>
              <div class="col-md-6" v-if="villager.proxy_account">
                <label class="form-label text-muted small mb-0">Proxy Account</label>
                <p class="fw-semibold mb-2">
                  {{ villager.proxy_account.representative_name }} 
                  <small class="text-muted">({{ villager.proxy_account.relationship }})</small>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Change History Card -->
        <div class="card shadow-sm mb-4" v-if="villager.history && villager.history.length">
          <div class="card-header bg-secondary bg-opacity-25">
            <h6 class="mb-0">📋 Change History</h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                  <tr><th>Date</th><th>Field</th><th>Previous Value</th><th>New Value</th></tr>
                </thead>
                <tbody>
                  <tr v-for="h in villager.history" :key="h.id">
                    <td><small>{{ formatDate(h.created_at) }}</small></td>
                    <td><code>{{ h.field_name }}</code></td>
                    <td class="text-danger"><small>{{ h.old_value || '—' }}</small></td>
                    <td class="text-success"><small>{{ h.new_value || '—' }}</small></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Actions & Audit -->
      <div class="col-lg-4">
        <!-- Registration Info Card -->
        <div class="card shadow-sm mb-4 border-info">
          <div class="card-header bg-info bg-opacity-10">
            <h6 class="mb-0">📝 Registration Info</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label text-muted small mb-0">Registration Date</label>
              <p class="fw-bold mb-0">{{ formatDate(villager.created_at) }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small mb-0">Registered By</label>
              <p class="fw-bold mb-0">
                {{ villager.registered_by_user?.name || 'System' }}
                <br><small class="text-muted">User ID: {{ villager.registered_by || '—' }}</small>
              </p>
            </div>
            <div>
              <label class="form-label text-muted small mb-0">Last Updated</label>
              <p class="fw-bold mb-0">{{ formatDate(villager.updated_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Status Management Card -->
        <div class="card shadow-sm mb-4" v-if="villager.status === 'active' || villager.status === 'archived'">
          <div class="card-header bg-danger bg-opacity-10">
            <h6 class="mb-0">⚡ Lifecycle Management</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label small">Change Status To:</label>
              <select v-model="newStatus" class="form-select">
                <option value="">— Select —</option>
                <option v-if="villager.status === 'active'" value="deceased">☠️ Deceased</option>
                <option v-if="villager.status === 'active'" value="archived">📦 Archived</option>
                <option v-if="villager.status === 'archived'" value="deceased">☠️ Deceased</option>
              </select>
            </div>
            <div v-if="newStatus === 'deceased'" class="mb-3">
              <label class="form-label small">Date of Death</label>
              <input v-model="deathDate" type="date" class="form-control">
            </div>
            <div v-if="newStatus === 'archived'" class="mb-3">
              <label class="form-label small">Reason for Archival</label>
              <textarea v-model="archiveReason" class="form-control" rows="2" maxlength="500" placeholder="Reason..."></textarea>
            </div>
            <button class="btn btn-danger w-100" @click="changeStatus" :disabled="!newStatus">
              Apply Status Change
            </button>
          </div>
        </div>

        <!-- Deceased/Archived Info -->
        <div class="card shadow-sm mb-4 border-secondary" v-if="villager.status === 'deceased'">
          <div class="card-body text-center">
            <span class="badge bg-secondary fs-5 mb-2">☠️ DECEASED</span>
            <p class="mb-0"><strong>Date of Death:</strong> {{ formatDateShort(villager.date_of_death) }}</p>
          </div>
        </div>
        <div class="card shadow-sm mb-4 border-warning" v-if="villager.status === 'archived'">
          <div class="card-body text-center">
            <span class="badge bg-warning text-dark fs-5 mb-2">📦 ARCHIVED</span>
            <p class="mb-0"><strong>Reason:</strong> {{ villager.archive_reason }}</p>
          </div>
        </div>

        <!-- Proxy Account Card -->
        <div class="card shadow-sm mb-4" v-if="villager.status === 'active'">
          <div class="card-header bg-light">
            <h6 class="mb-0">👨‍👩‍👧‍👦 Family Members</h6>
          </div>
          <div class="card-body">
            <div v-if="familyMembers.length">
              <div v-for="fm in familyMembers" :key="fm.id" class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                  <strong>{{ fm.full_name }}</strong>
                  <br><small class="text-muted text-capitalize">{{ fm.relationship }} {{ fm.date_of_birth ? '- Age: ' + calcAge(fm.date_of_birth) : '' }}</small>
                  <span v-if="fm.occupation" class="ms-1 badge bg-light text-dark">{{ fm.occupation }}</span>
                </div>
                <button class="btn btn-outline-danger btn-sm" @click="removeFamilyMember(fm.id)">x</button>
              </div>
            </div>
            <div v-else><p class="text-muted small mb-2">No family members recorded</p></div>
            <button class="btn btn-outline-primary btn-sm mt-2" @click="showFamilyForm = !showFamilyForm">+ Add Family Member</button>
            <div v-if="showFamilyForm" class="mt-2">
              <input v-model="familyForm.full_name" class="form-control form-control-sm mb-1" placeholder="Full Name">
              <select v-model="familyForm.relationship" class="form-select form-select-sm mb-1">
                <option value="spouse">Spouse</option>
                <option value="son">Son</option>
                <option value="daughter">Daughter</option>
                <option value="other">Other</option>
              </select>
              <input v-model="familyForm.date_of_birth" type="date" class="form-control form-control-sm mb-1" placeholder="DOB">
              <input v-model="familyForm.occupation" class="form-control form-control-sm mb-1" placeholder="Occupation (optional)">
              <button class="btn btn-success btn-sm" @click="addFamilyMember">Save</button>
            </div>
          </div>
        </div>

        <!-- Proxy Account Card -->
        <div class="card shadow-sm mb-4" v-if="villager.status === 'active'">
          <div class="card-header bg-light">
            <h6 class="mb-0">🤝 Proxy Account</h6>
          </div>
          <div class="card-body">
            <div v-if="villager.proxy_account">
              <p class="mb-1"><strong>{{ villager.proxy_account.representative_name }}</strong></p>
              <p class="mb-1 text-muted text-capitalize">{{ villager.proxy_account.relationship }}</p>
              <p class="mb-1"><small>Bank: {{ villager.proxy_account.proxy_bank_name }}</small></p>
              <p class="mb-1"><small>Account: {{ villager.proxy_account.proxy_bank_account }}</small></p>
              <button class="btn btn-outline-danger btn-sm mt-2" @click="removeProxy">Remove Proxy</button>
            </div>
            <div v-else>
              <p class="text-muted small">No proxy assigned</p>
              <button class="btn btn-outline-primary btn-sm" @click="showProxyForm = !showProxyForm">+ Assign Proxy</button>
              <div v-if="showProxyForm" class="mt-3">
                <input v-model="proxyForm.representative_name" class="form-control form-control-sm mb-2" placeholder="Representative Name">
                <select v-model="proxyForm.relationship" class="form-select form-select-sm mb-2">
                  <option value="">Relationship</option>
                  <option value="spouse">Spouse</option>
                  <option value="child">Child</option>
                  <option value="sibling">Sibling</option>
                  <option value="parent">Parent</option>
                  <option value="grandchild">Grandchild</option>
                  <option value="legal_guardian">Legal Guardian</option>
                </select>
                <input v-model="proxyForm.proxy_bank_name" class="form-control form-control-sm mb-2" placeholder="Proxy Bank Name (e.g. GTBank)">
                <input v-model="proxyForm.proxy_bank_account" class="form-control form-control-sm mb-2" placeholder="Proxy Bank Account Number">
                <button class="btn btn-primary btn-sm w-100" @click="assignProxy">Save Proxy</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="text-center mt-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-2">Loading villager record...</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      villager: null,
      editing: false,
      newStatus: '',
      deathDate: '',
      archiveReason: '',
      showProxyForm: false,
      proxyForm: { representative_name: '', relationship: '', proxy_bank_name: '', proxy_bank_account: '' },
      familyMembers: [],
      showFamilyForm: false,
      familyForm: { full_name: '', relationship: 'spouse', date_of_birth: '', occupation: '' },
    };
  },
  computed: {
    ninStatusClass() {
      const map = {
        verified: 'badge bg-success',
        pending: 'badge bg-warning text-dark',
        unverified: 'badge bg-danger',
        not_submitted: 'badge bg-secondary',
      };
      return map[this.villager?.nin_verification_status] || 'badge bg-secondary';
    },
    ninStatusLabel() {
      const map = {
        verified: '✓ Verified',
        pending: '⏳ Pending',
        unverified: '✗ Unverified',
        not_submitted: 'Not Submitted',
      };
      return map[this.villager?.nin_verification_status] || '';
    },
  },
  async mounted() {
    const id = this.$route.params.id;
    try {
      const { data } = await axios.get(`/villagers/${id}`);
      this.villager = data.data;
      await this.loadFamily();
    } catch (e) {
      alert('Failed to load record: ' + (e.response?.data?.message || e.message));
    }
  },
  methods: {
    statusBadge(status) {
      const map = { active: 'badge bg-success', deceased: 'badge bg-secondary', archived: 'badge bg-warning text-dark' };
      return map[status] || 'badge bg-secondary';
    },
    formatDate(d) { return d ? new Date(d).toLocaleString() : '—'; },
    formatDateShort(d) { return d ? new Date(d).toLocaleDateString() : '—'; },
    async changeStatus() {
      if (!confirm(`Are you sure you want to mark this villager as "${this.newStatus}"? This cannot be undone.`)) return;
      try {
        await axios.post(`/villagers/${this.villager.unique_id}/status`, {
          status: this.newStatus,
          date_of_death: this.deathDate || undefined,
          archive_reason: this.archiveReason || undefined,
        });
        alert('Status updated successfully.');
        location.reload();
      } catch (e) {
        alert(e.response?.data?.message || 'Error updating status');
      }
    },
    async assignProxy() {
      try {
        await axios.post(`/villagers/${this.villager.unique_id}/proxy`, this.proxyForm);
        alert('Proxy assigned!');
        location.reload();
      } catch (e) {
        alert(e.response?.data?.message || 'Error assigning proxy');
      }
    },
    async removeProxy() {
      if (!confirm('Remove proxy account? Stipends will revert to personal bank account.')) return;
      try {
        await axios.delete(`/villagers/${this.villager.unique_id}/proxy`);
        alert('Proxy removed.');
        location.reload();
      } catch (e) {
        alert(e.response?.data?.message || 'Error');
      }
    },
    async loadFamily() {
      try {
        const { data } = await axios.get(`/villagers/${this.villager.unique_id}/family`);
        this.familyMembers = data.data || [];
      } catch (e) { this.familyMembers = []; }
    },
    async addFamilyMember() {
      try {
        await axios.post(`/villagers/${this.villager.unique_id}/family`, this.familyForm);
        this.familyForm = { full_name: '', relationship: 'spouse', date_of_birth: '', occupation: '' };
        this.showFamilyForm = false;
        await this.loadFamily();
      } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    async removeFamilyMember(id) {
      if (!confirm('Remove this family member?')) return;
      await axios.delete(`/villagers/family/${id}`);
      await this.loadFamily();
    },
    calcAge(dob) {
      if (!dob) return '';
      const today = new Date(); const birth = new Date(dob);
      let age = today.getFullYear() - birth.getFullYear();
      const m = today.getMonth() - birth.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
      return age;
    },
  },
};
</script>
