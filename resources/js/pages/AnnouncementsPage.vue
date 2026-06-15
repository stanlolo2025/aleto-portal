<template>
  <div>
    <h2>📢 Announcements</h2>
    <div class="d-flex justify-content-between mt-3 mb-3"><p class="text-muted mb-0">Community notices and grant announcements.</p><button class="btn btn-primary btn-sm" @click="showForm=!showForm">+ New Announcement</button></div>

    <div class="card mb-3" v-if="showForm">
      <div class="card-body">
        <form @submit.prevent="create">
          <div class="row g-2">
            <div class="col-md-4"><input v-model="form.title" class="form-control" placeholder="Title *" required></div>
            <div class="col-md-2"><select v-model="form.type" class="form-select"><option value="general">General</option><option value="grant">Grant</option><option value="registration">Registration</option><option value="event">Event</option><option value="urgent">Urgent</option></select></div>
            <div class="col-md-2"><input v-model="form.expires_at" type="date" class="form-control" placeholder="Expires"></div>
            <div class="col-md-4"><input v-model="form.content" class="form-control" placeholder="Content *" required></div>
          </div>
          <button type="submit" class="btn btn-success btn-sm mt-2">Publish</button>
        </form>
      </div>
    </div>

    <div v-for="a in announcements" :key="a.id" class="card mb-2 shadow-sm" :class="{'border-danger': a.type==='urgent'}">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span :class="typeBadge(a.type)" class="me-2">{{ a.type }}</span>
          <strong>{{ a.title }}</strong>
          <p class="mb-0 mt-1 text-muted">{{ a.content }}</p>
          <small class="text-muted">Posted by {{ a.created_by_user?.name }} — {{ formatDate(a.created_at) }}</small>
        </div>
        <button class="btn btn-outline-danger btn-sm" @click="deactivate(a.id)">Remove</button>
      </div>
    </div>
    <div v-if="!announcements.length" class="text-center text-muted py-4">No announcements</div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() { return { announcements: [], showForm: false, form: { title: '', content: '', type: 'general', expires_at: '' } }; },
  async mounted() { await this.fetch(); },
  methods: {
    async fetch() { const { data } = await axios.get('/announcements/all'); this.announcements = data.data || []; },
    async create() { try { await axios.post('/announcements', this.form); this.showForm=false; this.form = { title:'',content:'',type:'general',expires_at:'' }; this.fetch(); } catch(e) { alert(e.response?.data?.message||'Error'); } },
    async deactivate(id) { await axios.post(`/announcements/${id}/deactivate`); this.fetch(); },
    typeBadge(t) { return { general:'badge bg-secondary', grant:'badge bg-success', registration:'badge bg-primary', event:'badge bg-info', urgent:'badge bg-danger' }[t]; },
    formatDate(d) { return d ? new Date(d).toLocaleDateString() : ''; },
  },
};
</script>
