<template>
  <div>
    <h2>🏥 Healthcare Module</h2>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mt-3">
      <li class="nav-item"><a class="nav-link" :class="{active: tab === 'records'}" href="#" @click.prevent="tab='records'">Medical Records</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab === 'visits'}" href="#" @click.prevent="tab='visits'">Clinic Visits</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab === 'alerts'}" href="#" @click.prevent="tab='alerts'">Preventive Care Alerts</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab === 'grants'}" href="#" @click.prevent="tab='grants'">Health Grants</a></li>
    </ul>

    <!-- Medical Records Tab -->
    <div v-if="tab === 'records'" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Medical Records</h5>
        <button class="btn btn-primary btn-sm" @click="showForm = showForm === 'record' ? '' : 'record'">+ Add Record</button>
      </div>
      <div class="card mb-3" v-if="showForm === 'record'">
        <div class="card-body">
          <form @submit.prevent="submitMedicalRecord">
            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label small">Villager *</label>
                <select v-model="recordForm.villager_record_id" class="form-select form-select-sm" required>
                  <option value="">Select Villager</option>
                  <option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }} ({{ v.unique_id }})</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Vaccination Type</label>
                <input v-model="recordForm.vaccination_type" class="form-control form-control-sm" placeholder="e.g. Polio, Measles">
              </div>
              <div class="col-md-2">
                <label class="form-label small">Vaccination Date</label>
                <input v-model="recordForm.vaccination_date" type="date" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small">Vaccination Status</label>
                <select v-model="recordForm.vaccination_status" class="form-select form-select-sm">
                  <option value="">N/A</option>
                  <option value="completed">Completed</option>
                  <option value="partial">Partial</option>
                  <option value="pending">Pending</option>
                </select>
              </div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col-md-4">
                <label class="form-label small">Chronic Conditions</label>
                <input v-model="recordForm.chronic_conditions" class="form-control form-control-sm" placeholder="Diabetes, hypertension...">
              </div>
              <div class="col-md-2">
                <label class="form-label small">Disability</label>
                <select v-model="recordForm.disability_status" class="form-select form-select-sm">
                  <option :value="false">No</option>
                  <option :value="true">Yes</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Allergies</label>
                <input v-model="recordForm.allergies" class="form-control form-control-sm" placeholder="Optional">
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100">Save Record</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-dark"><tr><th>ID</th><th>Villager</th><th>Vaccination</th><th>Date</th><th>Status</th><th>Chronic Conditions</th><th>Disability</th><th>Recorded By</th></tr></thead>
          <tbody>
            <tr v-for="r in medicalRecords" :key="r.id">
              <td><code>{{ r.record_id }}</code></td>
              <td>{{ r.villager_record?.full_name }}</td>
              <td>{{ r.vaccination_type || '—' }}</td>
              <td>{{ r.vaccination_date || '—' }}</td>
              <td><span v-if="r.vaccination_status" :class="vacBadge(r.vaccination_status)">{{ r.vaccination_status }}</span><span v-else>—</span></td>
              <td>{{ r.chronic_conditions || '—' }}</td>
              <td>{{ r.disability_status ? '✓ Yes' : 'No' }}</td>
              <td>{{ r.recorded_by_user?.name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Clinic Visits Tab -->
    <div v-if="tab === 'visits'" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Clinic Visits</h5>
        <button class="btn btn-primary btn-sm" @click="showForm = showForm === 'visit' ? '' : 'visit'">+ Record Visit</button>
      </div>
      <div class="card mb-3" v-if="showForm === 'visit'">
        <div class="card-body">
          <form @submit.prevent="submitClinicVisit">
            <div class="row g-2">
              <div class="col-md-3">
                <label class="form-label small">Villager *</label>
                <select v-model="visitForm.villager_record_id" class="form-select form-select-sm" required>
                  <option value="">Select Villager</option>
                  <option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Clinic Name *</label>
                <input v-model="visitForm.clinic_name" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Location</label>
                <input v-model="visitForm.clinic_location" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small">Visit Date *</label>
                <input v-model="visitForm.visit_date" type="date" class="form-control form-control-sm" required>
              </div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col-md-2">
                <label class="form-label small">Reason *</label>
                <select v-model="visitForm.reason" class="form-select form-select-sm" required>
                  <option value="diagnosis">Diagnosis</option>
                  <option value="check_up">Check-up</option>
                  <option value="emergency">Emergency</option>
                  <option value="follow_up">Follow-up</option>
                  <option value="vaccination">Vaccination</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Treatment</label>
                <input v-model="visitForm.treatment" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small">Health Worker *</label>
                <input v-model="visitForm.health_worker" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Notes</label>
                <input v-model="visitForm.notes" class="form-control form-control-sm">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100">Save</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-dark"><tr><th>Visit ID</th><th>Villager</th><th>Clinic</th><th>Date</th><th>Reason</th><th>Treatment</th><th>Health Worker</th></tr></thead>
          <tbody>
            <tr v-for="v in clinicVisits" :key="v.id">
              <td><code>{{ v.visit_id }}</code></td>
              <td>{{ v.villager_record?.full_name }}</td>
              <td>{{ v.clinic_name }}</td>
              <td>{{ v.visit_date }}</td>
              <td><span class="badge bg-info">{{ v.reason }}</span></td>
              <td>{{ v.treatment || '—' }}</td>
              <td>{{ v.health_worker }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Preventive Care Alerts Tab -->
    <div v-if="tab === 'alerts'" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Preventive Care Alerts</h5>
        <button class="btn btn-primary btn-sm" @click="showForm = showForm === 'alert' ? '' : 'alert'">+ Create Alert</button>
      </div>
      <div class="card mb-3" v-if="showForm === 'alert'">
        <div class="card-body">
          <form @submit.prevent="submitAlert">
            <div class="row g-2">
              <div class="col-md-3">
                <label class="form-label small">Villager *</label>
                <select v-model="alertForm.villager_record_id" class="form-select form-select-sm" required>
                  <option value="">Select Villager</option>
                  <option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Type *</label>
                <select v-model="alertForm.alert_type" class="form-select form-select-sm" required>
                  <option value="immunization">Immunization</option>
                  <option value="maternal">Maternal</option>
                  <option value="chronic">Chronic</option>
                  <option value="follow_up">Follow-up</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Description *</label>
                <input v-model="alertForm.description" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Due Date *</label>
                <input v-model="alertForm.due_date" type="date" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100">Create</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-dark"><tr><th>Alert ID</th><th>Villager</th><th>Type</th><th>Description</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
            <tr v-for="a in alerts" :key="a.id" :class="{'table-danger': a.status === 'overdue', 'table-success': a.status === 'completed'}">
              <td><code>{{ a.alert_id }}</code></td>
              <td>{{ a.villager_record?.full_name }}</td>
              <td><span class="badge bg-primary">{{ a.alert_type }}</span></td>
              <td>{{ a.description }}</td>
              <td>{{ a.due_date }}</td>
              <td><span :class="alertStatusBadge(a.status)">{{ a.status }}</span></td>
              <td><button v-if="a.status === 'pending'" class="btn btn-success btn-sm" @click="completeAlert(a.id)">✓ Done</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Health Grants Tab -->
    <div v-if="tab === 'grants'" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Health Grants</h5>
        <button class="btn btn-primary btn-sm" @click="showForm = showForm === 'hgrant' ? '' : 'hgrant'">+ New Health Grant</button>
      </div>
      <div class="card mb-3" v-if="showForm === 'hgrant'">
        <div class="card-body">
          <form @submit.prevent="submitHealthGrant">
            <div class="row g-2">
              <div class="col-md-3">
                <label class="form-label small">Villager *</label>
                <select v-model="hGrantForm.villager_record_id" class="form-select form-select-sm" required>
                  <option value="">Select Villager</option>
                  <option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Grant Type *</label>
                <select v-model="hGrantForm.grant_type" class="form-select form-select-sm" required>
                  <option value="medical_subsidy">Medical Subsidy</option>
                  <option value="elderly_health_stipend">Elderly Health Stipend</option>
                  <option value="disability_support">Disability Support</option>
                  <option value="maternal_care">Maternal Care</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Amount (₦) *</label>
                <input v-model="hGrantForm.amount" type="number" step="0.01" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-2">
                <label class="form-label small">Payment Method *</label>
                <select v-model="hGrantForm.payment_method" class="form-select form-select-sm" required>
                  <option value="bank_transfer">Bank Transfer</option>
                  <option value="proxy_account">Proxy Account</option>
                  <option value="mobile_wallet">Mobile Wallet</option>
                </select>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100">Create Grant</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-dark"><tr><th>Villager</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th><th>Created By</th><th>Actions</th></tr></thead>
          <tbody>
            <tr v-for="g in healthGrants" :key="g.id">
              <td>{{ g.villager_record?.full_name }}</td>
              <td class="text-capitalize">{{ g.grant_type?.replace('_', ' ') }}</td>
              <td>₦{{ Number(g.amount).toLocaleString() }}</td>
              <td class="text-capitalize">{{ g.payment_method?.replace('_', ' ') }}</td>
              <td><span :class="grantStatusBadge(g.approval_status)">{{ g.approval_status }}</span></td>
              <td>{{ g.created_by_user?.name }}</td>
              <td>
                <button v-if="g.approval_status === 'pending'" class="btn btn-success btn-sm me-1" @click="approveGrant(g.id)">Approve</button>
                <button v-if="g.approval_status === 'approved'" class="btn btn-info btn-sm" @click="markPaid(g.id)">Mark Paid</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      tab: 'records',
      showForm: '',
      villagers: [],
      medicalRecords: [],
      clinicVisits: [],
      alerts: [],
      healthGrants: [],
      recordForm: { villager_record_id: '', vaccination_type: '', vaccination_date: '', vaccination_status: '', chronic_conditions: '', disability_status: false, allergies: '', notes: '' },
      visitForm: { villager_record_id: '', clinic_name: '', clinic_location: '', visit_date: '', reason: 'check_up', treatment: '', health_worker: '', notes: '' },
      alertForm: { villager_record_id: '', alert_type: 'immunization', description: '', due_date: '' },
      hGrantForm: { villager_record_id: '', grant_type: 'medical_subsidy', amount: '', payment_method: 'bank_transfer' },
    };
  },
  async mounted() {
    await this.fetchVillagers();
    await this.loadTabData();
  },
  watch: {
    tab() { this.loadTabData(); },
  },
  methods: {
    async fetchVillagers() {
      const { data } = await axios.get('/villagers?status=active&per_page=1000');
      this.villagers = data.data || [];
    },
    async loadTabData() {
      if (this.tab === 'records') { const { data } = await axios.get('/healthcare/medical-records'); this.medicalRecords = data.data || []; }
      if (this.tab === 'visits') { const { data } = await axios.get('/healthcare/clinic-visits'); this.clinicVisits = data.data || []; }
      if (this.tab === 'alerts') { const { data } = await axios.get('/healthcare/alerts'); this.alerts = data.data || []; }
      if (this.tab === 'grants') { const { data } = await axios.get('/healthcare/health-grants'); this.healthGrants = data.data || []; }
    },
    async submitMedicalRecord() {
      try { await axios.post('/healthcare/medical-records', this.recordForm); alert('Record saved!'); this.showForm = ''; this.loadTabData(); } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    async submitClinicVisit() {
      try { await axios.post('/healthcare/clinic-visits', this.visitForm); alert('Visit recorded!'); this.showForm = ''; this.loadTabData(); } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    async submitAlert() {
      try { await axios.post('/healthcare/alerts', this.alertForm); alert('Alert created!'); this.showForm = ''; this.loadTabData(); } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    async completeAlert(id) {
      await axios.post(`/healthcare/alerts/${id}/complete`); this.loadTabData();
    },
    async submitHealthGrant() {
      try { await axios.post('/healthcare/health-grants', this.hGrantForm); alert('Health grant created!'); this.showForm = ''; this.loadTabData(); } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    async approveGrant(id) { await axios.post(`/healthcare/health-grants/${id}/approve`); this.loadTabData(); },
    async markPaid(id) { await axios.post(`/healthcare/health-grants/${id}/paid`); this.loadTabData(); },
    vacBadge(s) { return { completed: 'badge bg-success', partial: 'badge bg-warning', pending: 'badge bg-secondary' }[s]; },
    alertStatusBadge(s) { return { pending: 'badge bg-warning', completed: 'badge bg-success', overdue: 'badge bg-danger', cancelled: 'badge bg-secondary' }[s]; },
    grantStatusBadge(s) { return { pending: 'badge bg-warning', approved: 'badge bg-success', paid: 'badge bg-info', rejected: 'badge bg-danger' }[s]; },
  },
};
</script>
