<template>
  <div>
    <h2>📚 Education Module</h2>
    <ul class="nav nav-tabs mt-3">
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='students'}" href="#" @click.prevent="tab='students'">Student Registry</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='scholarships'}" href="#" @click.prevent="tab='scholarships'">Scholarships</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='exams'}" href="#" @click.prevent="tab='exams'">Exam Performance</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='literacy'}" href="#" @click.prevent="tab='literacy'">Adult Literacy</a></li>
    </ul>

    <!-- Students Tab -->
    <div v-if="tab==='students'" class="mt-3">
      <div class="d-flex justify-content-between mb-3"><h5>Student Registry</h5><button class="btn btn-primary btn-sm" @click="showForm=showForm==='student'?'':'student'">+ Enroll Student</button></div>
      <div class="card mb-3" v-if="showForm==='student'">
        <div class="card-body">
          <form @submit.prevent="submitStudent">
            <div class="row g-2">
              <div class="col-md-3"><label class="form-label small">Villager *</label><select v-model="studentForm.villager_record_id" class="form-select form-select-sm" required><option value="">Select</option><option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option></select></div>
              <div class="col-md-3"><label class="form-label small">School Name *</label><input v-model="studentForm.school_name" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><label class="form-label small">Class Level *</label><input v-model="studentForm.class_level" class="form-control form-control-sm" required placeholder="JSS1, SS2..."></div>
              <div class="col-md-2"><label class="form-label small">Status</label><select v-model="studentForm.enrollment_status" class="form-select form-select-sm"><option value="enrolled">Enrolled</option><option value="not_enrolled">Not Enrolled</option><option value="dropped_out">Dropped Out</option><option value="graduated">Graduated</option></select></div>
              <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-success btn-sm w-100">Save</button></div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-dark"><tr><th>Student ID</th><th>Villager</th><th>School</th><th>Class</th><th>Status</th><th>Present</th><th>Absent</th></tr></thead><tbody>
        <tr v-for="s in students" :key="s.id"><td><code>{{ s.student_id }}</code></td><td>{{ s.villager_record?.full_name }}</td><td>{{ s.school_name }}</td><td>{{ s.class_level }}</td><td><span :class="enrollBadge(s.enrollment_status)">{{ s.enrollment_status }}</span></td><td>{{ s.days_present }}</td><td>{{ s.days_absent }}</td></tr>
      </tbody></table></div>
    </div>

    <!-- Scholarships Tab -->
    <div v-if="tab==='scholarships'" class="mt-3">
      <div class="d-flex justify-content-between mb-3"><h5>Scholarships</h5><button class="btn btn-primary btn-sm" @click="showForm=showForm==='scholarship'?'':'scholarship'">+ New Scholarship</button></div>
      <div class="card mb-3" v-if="showForm==='scholarship'">
        <div class="card-body">
          <form @submit.prevent="submitScholarship">
            <div class="row g-2">
              <div class="col-md-3"><label class="form-label small">Student *</label><select v-model="scholarshipForm.student_id" class="form-select form-select-sm" required><option value="">Select</option><option v-for="s in students" :key="s.id" :value="s.id">{{ s.villager_record?.full_name }} ({{ s.school_name }})</option></select></div>
              <div class="col-md-2"><label class="form-label small">Type *</label><select v-model="scholarshipForm.scholarship_type" class="form-select form-select-sm" required><option value="government_bursary">Govt Bursary</option><option value="community_grant">Community Grant</option><option value="ngo_sponsorship">NGO Sponsorship</option><option value="other">Other</option></select></div>
              <div class="col-md-2"><label class="form-label small">Amount (₦) *</label><input v-model="scholarshipForm.amount" type="number" step="0.01" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><label class="form-label small">Payment *</label><select v-model="scholarshipForm.payment_method" class="form-select form-select-sm" required><option value="bank_transfer">Bank Transfer</option><option value="proxy_account">Proxy Account</option><option value="mobile_wallet">Mobile Wallet</option></select></div>
              <div class="col-md-1"><label class="form-label small">Year</label><input v-model="scholarshipForm.academic_year" class="form-control form-control-sm" placeholder="2026"></div>
              <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-success btn-sm w-100">Create</button></div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-dark"><tr><th>ID</th><th>Student</th><th>Type</th><th>Amount</th><th>Year</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        <tr v-for="s in scholarships" :key="s.id"><td><code>{{ s.scholarship_id }}</code></td><td>{{ s.student?.villager_record?.full_name }}</td><td class="text-capitalize">{{ s.scholarship_type?.replace('_',' ') }}</td><td>₦{{ Number(s.amount).toLocaleString() }}</td><td>{{ s.academic_year || '—' }}</td><td><span :class="statusBadge(s.approval_status)">{{ s.approval_status }}</span></td>
          <td><button v-if="s.approval_status==='pending'" class="btn btn-success btn-sm me-1" @click="approveScholarship(s.id)">Approve</button><button v-if="s.approval_status==='approved'" class="btn btn-info btn-sm" @click="markScholarshipPaid(s.id)">Paid</button></td></tr>
      </tbody></table></div>
    </div>

    <!-- Exams Tab -->
    <div v-if="tab==='exams'" class="mt-3">
      <div class="d-flex justify-content-between mb-3"><h5>Exam Performance</h5><button class="btn btn-primary btn-sm" @click="showForm=showForm==='exam'?'':'exam'">+ Record Result</button></div>
      <div class="card mb-3" v-if="showForm==='exam'">
        <div class="card-body">
          <form @submit.prevent="submitExam">
            <div class="row g-2">
              <div class="col-md-3"><label class="form-label small">Student *</label><select v-model="examForm.student_id" class="form-select form-select-sm" required><option value="">Select</option><option v-for="s in students" :key="s.id" :value="s.id">{{ s.villager_record?.full_name }}</option></select></div>
              <div class="col-md-2"><label class="form-label small">Subject *</label><input v-model="examForm.subject" class="form-control form-control-sm" required></div>
              <div class="col-md-1"><label class="form-label small">Score *</label><input v-model="examForm.score" class="form-control form-control-sm" required></div>
              <div class="col-md-1"><label class="form-label small">Grade</label><input v-model="examForm.grade" class="form-control form-control-sm" placeholder="A, B..."></div>
              <div class="col-md-2"><label class="form-label small">Date *</label><input v-model="examForm.exam_date" type="date" class="form-control form-control-sm" required></div>
              <div class="col-md-1"><label class="form-label small">Type</label><input v-model="examForm.exam_type" class="form-control form-control-sm" placeholder="Final"></div>
              <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-success btn-sm w-100">Save</button></div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-dark"><tr><th>Exam ID</th><th>Student</th><th>Subject</th><th>Score</th><th>Grade</th><th>Date</th><th>Type</th></tr></thead><tbody>
        <tr v-for="e in exams" :key="e.id"><td><code>{{ e.exam_id }}</code></td><td>{{ e.student?.villager_record?.full_name }}</td><td>{{ e.subject }}</td><td><strong>{{ e.score }}</strong></td><td>{{ e.grade || '—' }}</td><td>{{ e.exam_date }}</td><td>{{ e.exam_type || '—' }}</td></tr>
      </tbody></table></div>
    </div>

    <!-- Adult Literacy Tab -->
    <div v-if="tab==='literacy'" class="mt-3">
      <div class="d-flex justify-content-between mb-3"><h5>Adult Literacy Programs</h5><button class="btn btn-primary btn-sm" @click="showForm=showForm==='literacy'?'':'literacy'">+ Enroll Participant</button></div>
      <div class="card mb-3" v-if="showForm==='literacy'">
        <div class="card-body">
          <form @submit.prevent="submitLiteracy">
            <div class="row g-2">
              <div class="col-md-3"><label class="form-label small">Villager *</label><select v-model="literacyForm.villager_record_id" class="form-select form-select-sm" required><option value="">Select</option><option v-for="v in villagers" :key="v.id" :value="v.id">{{ v.full_name }}</option></select></div>
              <div class="col-md-3"><label class="form-label small">Program Name *</label><input v-model="literacyForm.program_name" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><label class="form-label small">Total Sessions *</label><input v-model="literacyForm.total_sessions" type="number" min="1" class="form-control form-control-sm" required></div>
              <div class="col-md-2"><label class="form-label small">Start Date *</label><input v-model="literacyForm.start_date" type="date" class="form-control form-control-sm" required></div>
              <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-success btn-sm w-100">Enroll</button></div>
            </div>
          </form>
        </div>
      </div>
      <div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-dark"><tr><th>Program ID</th><th>Villager</th><th>Program</th><th>Attended</th><th>Total</th><th>Status</th><th>Completion</th></tr></thead><tbody>
        <tr v-for="l in literacy" :key="l.id"><td><code>{{ l.program_id }}</code></td><td>{{ l.villager_record?.full_name }}</td><td>{{ l.program_name }}</td><td>{{ l.sessions_attended }}</td><td>{{ l.total_sessions }}</td><td><span :class="enrollBadge(l.enrollment_status)">{{ l.enrollment_status }}</span></td><td><span :class="completionBadge(l.completion_status)">{{ l.completion_status }}</span></td></tr>
      </tbody></table></div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      tab: 'students', showForm: '', villagers: [], students: [], scholarships: [], exams: [], literacy: [],
      studentForm: { villager_record_id: '', school_name: '', class_level: '', enrollment_status: 'enrolled', days_present: 0, days_absent: 0 },
      scholarshipForm: { student_id: '', scholarship_type: 'government_bursary', amount: '', payment_method: 'bank_transfer', academic_year: '' },
      examForm: { student_id: '', subject: '', score: '', grade: '', exam_date: '', exam_type: '' },
      literacyForm: { villager_record_id: '', program_name: '', total_sessions: '', start_date: '', enrollment_status: 'enrolled' },
    };
  },
  async mounted() { await this.fetchVillagers(); await this.loadTab(); },
  watch: { tab() { this.loadTab(); } },
  methods: {
    async fetchVillagers() { const { data } = await axios.get('/villagers?status=active&per_page=1000'); this.villagers = data.data || []; },
    async loadTab() {
      if (this.tab === 'students') { const { data } = await axios.get('/education/students'); this.students = data.data || []; }
      if (this.tab === 'scholarships') { const { data } = await axios.get('/education/scholarships'); this.scholarships = data.data || []; if (!this.students.length) { const s = await axios.get('/education/students'); this.students = s.data.data || []; } }
      if (this.tab === 'exams') { const { data } = await axios.get('/education/exams'); this.exams = data.data || []; if (!this.students.length) { const s = await axios.get('/education/students'); this.students = s.data.data || []; } }
      if (this.tab === 'literacy') { const { data } = await axios.get('/education/literacy'); this.literacy = data.data || []; }
    },
    async submitStudent() { try { await axios.post('/education/students', this.studentForm); alert('Student enrolled!'); this.showForm=''; this.loadTab(); } catch(e) { alert(e.response?.data?.message||'Error'); } },
    async submitScholarship() { try { await axios.post('/education/scholarships', this.scholarshipForm); alert('Scholarship created!'); this.showForm=''; this.loadTab(); } catch(e) { alert(e.response?.data?.message||'Error'); } },
    async approveScholarship(id) { await axios.post(`/education/scholarships/${id}/approve`); this.loadTab(); },
    async markScholarshipPaid(id) { await axios.post(`/education/scholarships/${id}/paid`); this.loadTab(); },
    async submitExam() { try { await axios.post('/education/exams', this.examForm); alert('Result recorded!'); this.showForm=''; this.loadTab(); } catch(e) { alert(e.response?.data?.message||'Error'); } },
    async submitLiteracy() { try { await axios.post('/education/literacy', this.literacyForm); alert('Enrolled!'); this.showForm=''; this.loadTab(); } catch(e) { alert(e.response?.data?.message||'Error'); } },
    enrollBadge(s) { return { enrolled: 'badge bg-success', not_enrolled: 'badge bg-secondary', dropped_out: 'badge bg-danger', graduated: 'badge bg-info' }[s] || 'badge bg-secondary'; },
    statusBadge(s) { return { pending: 'badge bg-warning', approved: 'badge bg-success', paid: 'badge bg-info', rejected: 'badge bg-danger' }[s]; },
    completionBadge(s) { return { in_progress: 'badge bg-warning', completed: 'badge bg-success', incomplete: 'badge bg-danger' }[s]; },
  },
};
</script>
