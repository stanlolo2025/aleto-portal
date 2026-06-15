<template>
  <div>
    <h2>🏗️ Development Projects</h2>
    <ul class="nav nav-tabs mt-3">
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='projects'}" href="#" @click.prevent="tab='projects'">Projects</a></li>
      <li class="nav-item" v-if="selectedProject"><a class="nav-link" :class="{active: tab==='beneficiaries'}" href="#" @click.prevent="tab='beneficiaries'">Beneficiaries</a></li>
      <li class="nav-item" v-if="selectedProject"><a class="nav-link" :class="{active: tab==='feedback'}" href="#" @click.prevent="tab='feedback'">Feedback</a></li>
      <li class="nav-item" v-if="selectedProject"><a class="nav-link" :class="{active: tab==='impact'}" href="#" @click.prevent="tab='impact'">Impact Analytics</a></li>
    </ul>

    <!-- Projects List -->
    <div v-if="tab==='projects'" class="mt-3">
      <div class="d-flex justify-content-between mb-3"><h5>All Projects</h5><button class="btn btn-primary btn-sm" @click="showForm=!showForm">{{ showForm ? '✕ Close' : '+ New Project' }}</button></div>
      <div class="card mb-3" v-if="showForm">
        <div class="card-body">
          <form @submit.prevent="createProject">
            <div class="row g-2">
              <div class="col-md-3"><label class="form-label small">Project Name *</label><input v-model="projectForm.name" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><label class="form-label small">Type *</label><select v-model="projectForm.project_type" class="form-select form-select-sm" required><option value="water">Water</option><option value="road">Road</option><option value="electrification">Electrification</option><option value="health_facility">Health Facility</option><option value="school">School</option><option value="market">Market</option><option value="other">Other</option></select></div>
              <div class="col-md-2"><label class="form-label small">Location *</label><input v-model="projectForm.location" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><label class="form-label small">Start Date *</label><input v-model="projectForm.start_date" type="date" class="form-control form-control-sm" required></div>
              <div class="col-md-1"><label class="form-label small">Status</label><select v-model="projectForm.status" class="form-select form-select-sm"><option value="planned">Planned</option><option value="ongoing">Ongoing</option><option value="completed">Completed</option></select></div>
              <div class="col-md-2"><label class="form-label small">Budget (₦)</label><input v-model="projectForm.budget" type="number" class="form-control form-control-sm"></div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col-md-10"><input v-model="projectForm.description" class="form-control form-control-sm" placeholder="Description"></div>
              <div class="col-md-2"><button type="submit" class="btn btn-success btn-sm w-100">Create</button></div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Type</th><th>Location</th><th>Status</th><th>Budget</th><th>Beneficiaries</th><th>Actions</th></tr></thead><tbody>
        <tr v-for="p in projects" :key="p.id">
          <td><code>{{ p.project_id }}</code></td><td>{{ p.name }}</td><td class="text-capitalize">{{ p.project_type?.replace('_', ' ') }}</td><td>{{ p.location }}</td>
          <td><span :class="statusBadge(p.status)">{{ p.status }}</span></td>
          <td>{{ p.budget ? '₦' + Number(p.budget).toLocaleString() : '—' }}</td>
          <td>{{ p.beneficiaries_count || 0 }}</td>
          <td><button class="btn btn-sm btn-outline-primary" @click="selectProject(p)">Manage</button></td>
        </tr>
      </tbody></table></div>
    </div>

    <!-- Beneficiaries Tab -->
    <div v-if="tab==='beneficiaries' && selectedProject" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Beneficiaries — {{ selectedProject.name }}</h5>
        <button class="btn btn-primary btn-sm" @click="showBenForm=!showBenForm">+ Add Beneficiary</button>
      </div>
      <div class="card mb-3" v-if="showBenForm">
        <div class="card-body">
          <form @submit.prevent="addBeneficiary">
            <div class="row g-2">
              <div class="col-md-4"><select v-model="benForm.villager_record_id" class="form-select form-select-sm" required><option value="">Select Villager</option><option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option></select></div>
              <div class="col-md-4"><input v-model="benForm.benefit_type" class="form-control form-control-sm" placeholder="Benefit type (water access, electricity...)" required></div>
              <div class="col-md-2"><input v-model="benForm.remarks" class="form-control form-control-sm" placeholder="Remarks"></div>
              <div class="col-md-2"><button type="submit" class="btn btn-success btn-sm w-100">Add</button></div>
            </div>
          </form>
        </div>
      </div>
      <table class="table table-sm"><thead class="table-light"><tr><th>ID</th><th>Villager</th><th>Benefit Type</th><th>Remarks</th></tr></thead><tbody>
        <tr v-for="b in beneficiaries" :key="b.id"><td><code>{{ b.mapping_id }}</code></td><td>{{ b.villager_record?.full_name }}</td><td>{{ b.benefit_type }}</td><td>{{ b.remarks || '—' }}</td></tr>
      </tbody></table>
    </div>

    <!-- Feedback Tab -->
    <div v-if="tab==='feedback' && selectedProject" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Community Feedback — {{ selectedProject.name }}</h5>
        <button class="btn btn-primary btn-sm" @click="showFbForm=!showFbForm">+ Add Feedback</button>
      </div>
      <div class="card mb-3" v-if="showFbForm">
        <div class="card-body">
          <form @submit.prevent="addFeedback">
            <div class="row g-2">
              <div class="col-md-3"><select v-model="fbForm.villager_record_id" class="form-select form-select-sm" required><option value="">Villager</option><option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option></select></div>
              <div class="col-md-5"><input v-model="fbForm.feedback_text" class="form-control form-control-sm" placeholder="Feedback text" required></div>
              <div class="col-md-2"><input v-model="fbForm.date_submitted" type="date" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><button type="submit" class="btn btn-success btn-sm w-100">Submit</button></div>
            </div>
          </form>
        </div>
      </div>
      <table class="table table-sm"><thead class="table-light"><tr><th>ID</th><th>Villager</th><th>Feedback</th><th>Date</th></tr></thead><tbody>
        <tr v-for="f in feedback" :key="f.id"><td><code>{{ f.feedback_id }}</code></td><td>{{ f.villager_record?.full_name }}</td><td>{{ f.feedback_text }}</td><td>{{ f.date_submitted }}</td></tr>
      </tbody></table>
    </div>

    <!-- Impact Tab -->
    <div v-if="tab==='impact' && selectedProject" class="mt-3">
      <div class="d-flex justify-content-between mb-3">
        <h5>Impact Analytics — {{ selectedProject.name }}</h5>
        <button class="btn btn-primary btn-sm" @click="showImpForm=!showImpForm">+ Record Metric</button>
      </div>
      <div class="card mb-3" v-if="showImpForm">
        <div class="card-body">
          <form @submit.prevent="addImpact">
            <div class="row g-2">
              <div class="col-md-4"><input v-model="impForm.metric" class="form-control form-control-sm" placeholder="Metric (e.g. % households with water)" required></div>
              <div class="col-md-2"><input v-model="impForm.value" type="number" step="0.01" class="form-control form-control-sm" placeholder="Value" required></div>
              <div class="col-md-2"><input v-model="impForm.date_recorded" type="date" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><input v-model="impForm.notes" class="form-control form-control-sm" placeholder="Notes"></div>
              <div class="col-md-2"><button type="submit" class="btn btn-success btn-sm w-100">Record</button></div>
            </div>
          </form>
        </div>
      </div>
      <table class="table table-sm"><thead class="table-light"><tr><th>ID</th><th>Metric</th><th>Value</th><th>Date</th><th>Notes</th></tr></thead><tbody>
        <tr v-for="i in impact" :key="i.id"><td><code>{{ i.impact_id }}</code></td><td>{{ i.metric }}</td><td><strong>{{ i.value }}</strong></td><td>{{ i.date_recorded }}</td><td>{{ i.notes || '—' }}</td></tr>
      </tbody></table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      tab: 'projects', showForm: false, showBenForm: false, showFbForm: false, showImpForm: false,
      projects: [], villagers: [], selectedProject: null,
      beneficiaries: [], feedback: [], impact: [],
      projectForm: { name: '', project_type: 'water', location: '', start_date: '', status: 'planned', budget: '', description: '' },
      benForm: { villager_record_id: '', benefit_type: '', remarks: '' },
      fbForm: { villager_record_id: '', feedback_text: '', date_submitted: '' },
      impForm: { metric: '', value: '', date_recorded: '', notes: '' },
    };
  },
  async mounted() { await this.fetchProjects(); await this.fetchVillagers(); },
  watch: {
    tab(val) {
      if (val === 'beneficiaries') this.loadBeneficiaries();
      if (val === 'feedback') this.loadFeedback();
      if (val === 'impact') this.loadImpact();
    },
  },
  methods: {
    async fetchProjects() { const { data } = await axios.get('/projects'); this.projects = data.data || []; },
    async fetchVillagers() { const { data } = await axios.get('/villagers?status=active&per_page=1000'); this.villagers = data.data || []; },
    async createProject() { try { await axios.post('/projects', this.projectForm); alert('Project created!'); this.showForm = false; this.fetchProjects(); } catch(e) { alert(e.response?.data?.message || 'Error'); } },
    selectProject(p) { this.selectedProject = p; this.tab = 'beneficiaries'; this.loadBeneficiaries(); },
    async loadBeneficiaries() { const { data } = await axios.get(`/projects/${this.selectedProject.project_id}/beneficiaries`); this.beneficiaries = data.data || []; },
    async loadFeedback() { const { data } = await axios.get(`/projects/${this.selectedProject.project_id}/feedback`); this.feedback = data.data || []; },
    async loadImpact() { const { data } = await axios.get(`/projects/${this.selectedProject.project_id}/impact`); this.impact = data.data || []; },
    async addBeneficiary() { try { await axios.post(`/projects/${this.selectedProject.project_id}/beneficiaries`, this.benForm); this.showBenForm = false; this.loadBeneficiaries(); } catch(e) { alert(e.response?.data?.message || 'Error'); } },
    async addFeedback() { try { await axios.post(`/projects/${this.selectedProject.project_id}/feedback`, this.fbForm); this.showFbForm = false; this.loadFeedback(); } catch(e) { alert(e.response?.data?.message || 'Error'); } },
    async addImpact() { try { await axios.post(`/projects/${this.selectedProject.project_id}/impact`, this.impForm); this.showImpForm = false; this.loadImpact(); } catch(e) { alert(e.response?.data?.message || 'Error'); } },
    statusBadge(s) { return { planned: 'badge bg-secondary', ongoing: 'badge bg-primary', completed: 'badge bg-success', delayed: 'badge bg-warning', cancelled: 'badge bg-danger' }[s]; },
  },
};
</script>
