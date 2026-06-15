<template>
  <div>
    <h2>✉️ Messages</h2>
    <ul class="nav nav-tabs mt-3">
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='inbox'}" href="#" @click.prevent="tab='inbox'">Inbox <span v-if="unreadCount" class="badge bg-danger">{{ unreadCount }}</span></a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='sent'}" href="#" @click.prevent="tab='sent'">Sent</a></li>
      <li class="nav-item"><a class="nav-link" :class="{active: tab==='compose'}" href="#" @click.prevent="tab='compose'">✏️ Compose</a></li>
    </ul>

    <!-- Inbox -->
    <div v-if="tab==='inbox'" class="mt-3">
      <div v-for="msg in inbox" :key="msg.id" class="card mb-2 shadow-sm" :class="{'border-primary': !msg.is_read}" @click="readMessage(msg)" style="cursor:pointer;">
        <div class="card-body py-2 d-flex justify-content-between">
          <div>
            <strong :class="{'text-primary': !msg.is_read}">{{ msg.subject }}</strong>
            <br><small class="text-muted">From: {{ msg.sender?.name }} ({{ msg.sender?.role }})</small>
          </div>
          <div class="text-end">
            <small class="text-muted">{{ formatDate(msg.created_at) }}</small>
            <br><span v-if="!msg.is_read" class="badge bg-primary">New</span>
          </div>
        </div>
      </div>
      <div v-if="!inbox.length" class="text-center text-muted py-4">No messages</div>
    </div>

    <!-- Read Message Modal -->
    <div v-if="selectedMessage" class="card mt-3 shadow border-primary">
      <div class="card-header d-flex justify-content-between bg-primary text-white">
        <h6 class="mb-0">{{ selectedMessage.subject }}</h6>
        <button class="btn btn-sm btn-outline-light" @click="selectedMessage=null">✕</button>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-2">From: <strong>{{ selectedMessage.sender?.name }}</strong> — {{ formatDate(selectedMessage.created_at) }}</p>
        <hr>
        <p style="white-space: pre-wrap;">{{ selectedMessage.body }}</p>
      </div>
    </div>

    <!-- Sent -->
    <div v-if="tab==='sent'" class="mt-3">
      <div v-for="msg in sent" :key="msg.id" class="card mb-2 shadow-sm">
        <div class="card-body py-2 d-flex justify-content-between">
          <div>
            <strong>{{ msg.subject }}</strong>
            <br><small class="text-muted">To: {{ msg.receiver?.name }} ({{ msg.receiver?.role }})</small>
          </div>
          <small class="text-muted">{{ formatDate(msg.created_at) }}</small>
        </div>
      </div>
      <div v-if="!sent.length" class="text-center text-muted py-4">No sent messages</div>
    </div>

    <!-- Compose -->
    <div v-if="tab==='compose'" class="mt-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <form @submit.prevent="sendMessage">
            <div class="mb-3">
              <label class="form-label">To *</label>
              <select v-model="compose.receiver_id" class="form-select" required>
                <option value="">Select recipient</option>
                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.role }})</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Subject *</label>
              <input v-model="compose.subject" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Message *</label>
              <textarea v-model="compose.body" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() { return { tab: 'inbox', inbox: [], sent: [], users: [], unreadCount: 0, selectedMessage: null, compose: { receiver_id: '', subject: '', body: '' } }; },
  async mounted() { await this.loadInbox(); await this.loadUnread(); await this.loadUsers(); },
  watch: { tab(val) { if (val === 'inbox') this.loadInbox(); if (val === 'sent') this.loadSent(); } },
  methods: {
    async loadInbox() { const { data } = await axios.get('/messages/inbox'); this.inbox = data.data || []; },
    async loadSent() { const { data } = await axios.get('/messages/sent'); this.sent = data.data || []; },
    async loadUnread() { const { data } = await axios.get('/messages/unread-count'); this.unreadCount = data.count; },
    async loadUsers() { const { data } = await axios.get('/users'); this.users = data.data || []; },
    async readMessage(msg) { const { data } = await axios.post(`/messages/${msg.id}/read`); this.selectedMessage = data.data; this.loadUnread(); msg.is_read = true; },
    async sendMessage() { try { await axios.post('/messages/send', this.compose); alert('Message sent!'); this.compose = { receiver_id: '', subject: '', body: '' }; this.tab = 'sent'; this.loadSent(); } catch(e) { alert(e.response?.data?.message || 'Error'); } },
    formatDate(d) { return d ? new Date(d).toLocaleString() : ''; },
  },
};
</script>
