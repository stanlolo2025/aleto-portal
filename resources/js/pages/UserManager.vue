<template>
  <div>
    <h2>User Management</h2>

    <!-- Create User -->
    <div class="card mt-3" v-if="showCreate">
      <div class="card-header bg-primary text-white"><h6 class="mb-0">Create New User</h6></div>
      <div class="card-body">
        <form @submit.prevent="createUser">
          <div class="row g-2">
            <div class="col-md-3 mb-2"><label class="form-label small">Full Name *</label><input v-model="newUser.name" class="form-control" required></div>
            <div class="col-md-2 mb-2"><label class="form-label small">Username *</label><input v-model="newUser.username" class="form-control" required></div>
            <div class="col-md-3 mb-2"><label class="form-label small">Email *</label><input v-model="newUser.email" type="email" class="form-control" required></div>
            <div class="col-md-2 mb-2"><label class="form-label small">Password *</label><input v-model="newUser.password" type="password" class="form-control" required></div>
            <div class="col-md-2 mb-2"><label class="form-label small">Role *</label>
              <select v-model="newUser.role" class="form-select">
                <option value="admin">Admin</option>
                <option value="auditor">Auditor</option>
                <option value="government_official">Gov. Official</option>
              </select>
            </div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-md-3"><label class="form-label small">Phone</label><input v-model="newUser.phone" class="form-control"></div>
            <div class="col-md-9">
              <label class="form-label small">Permissions</label>
              <div class="d-flex flex-wrap gap-2">
                <div v-for="p in allPermissions" :key="p" class="form-check">
                  <input type="checkbox" class="form-check-input" :id="'new_'+p" :value="p" v-model="newUser.permissions">
                  <label class="form-check-label small" :for="'new_'+p">{{ p }}</label>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-success mt-2">Create User</button>
          <button type="button" class="btn btn-secondary mt-2 ms-2" @click="showCreate=false">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card mt-3">
      <div class="card-header d-flex justify-content-between">
        <strong>Portal Users</strong>
        <button class="btn btn-primary btn-sm" @click="showCreate=!showCreate">{{ showCreate ? 'Close' : '+ Add User' }}</button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-dark"><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Permissions</th><th>Actions</th></tr></thead>
            <tbody>
              <tr v-for="u in users" :key="u.id">
                <td>{{ u.name }}</td>
                <td><code>{{ u.username }}</code></td>
                <td>{{ u.email }}</td>
                <td>
                  <select :value="u.role" @change="changeRole(u.id, $event.target.value)" class="form-select form-select-sm" style="width:140px;">
                    <option value="admin">Admin</option>
                    <option value="auditor">Auditor</option>
                    <option value="government_official">Gov. Official</option>
                  </select>
                </td>
                <td>
                  <small v-if="u.permissions && u.permissions.length">{{ u.permissions.join(', ') }}</small>
                  <small v-else class="text-muted">All (by role)</small>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" @click="editUser(u)">Edit</button>
                  <button class="btn btn-sm btn-outline-warning" @click="resetPassword(u)">Reset PW</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div class="card mt-3 shadow border-primary" v-if="editingUser">
      <div class="card-header bg-primary text-white d-flex justify-content-between">
        <h6 class="mb-0">Edit User: {{ editingUser.name }}</h6>
        <button class="btn btn-sm btn-outline-light" @click="editingUser=null">Close</button>
      </div>
      <div class="card-body">
        <form @submit.prevent="saveUser">
          <div class="row g-2">
            <div class="col-md-4 mb-2"><label class="form-label small">Name</label><input v-model="editForm.name" class="form-control"></div>
            <div class="col-md-4 mb-2"><label class="form-label small">Email</label><input v-model="editForm.email" type="email" class="form-control"></div>
            <div class="col-md-4 mb-2"><label class="form-label small">Phone</label><input v-model="editForm.phone" class="form-control"></div>
          </div>
          <div class="mt-3">
            <label class="form-label small fw-bold">Module Permissions (what this user can access):</label>
            <div class="row g-2">
              <div v-for="p in allPermissions" :key="p" class="col-md-3 col-6">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" :id="'edit_'+p" :value="p" v-model="editForm.permissions">
                  <label class="form-check-label" :for="'edit_'+p">{{ p }}</label>
                </div>
              </div>
            </div>
            <small class="text-muted d-block mt-1">If no permissions are selected, user gets full access based on their role.</small>
          </div>
          <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      users: [],
      showCreate: false,
      newUser: { name: '', username: '', email: '', password: '', role: 'auditor', phone: '', permissions: [] },
      editingUser: null,
      editForm: { name: '', email: '', phone: '', permissions: [] },
      allPermissions: [
        'registry', 'grants', 'grant_history', 'payments',
        'healthcare', 'education', 'projects',
        'reports', 'announcements', 'audit_log', 'users', 'messages',
      ],
    };
  },
  async mounted() { await this.fetchUsers(); },
  methods: {
    async fetchUsers() { const { data } = await axios.get('/users'); this.users = data.data || []; },
    async createUser() {
      try {
        await axios.post('/users', this.newUser);
        alert('User created!');
        this.showCreate = false;
        this.newUser = { name: '', username: '', email: '', password: '', role: 'auditor', phone: '', permissions: [] };
        this.fetchUsers();
      } catch (e) { alert(e.response?.data?.message || JSON.stringify(e.response?.data?.errors)); }
    },
    async changeRole(id, role) {
      try { await axios.put(`/users/${id}/role`, { role }); alert('Role updated!'); this.fetchUsers(); } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    editUser(u) {
      this.editingUser = u;
      this.editForm = { name: u.name, email: u.email, phone: u.phone || '', permissions: u.permissions || [] };
    },
    async saveUser() {
      try {
        await axios.put(`/users/${this.editingUser.id}`, this.editForm);
        alert('User updated!');
        this.editingUser = null;
        this.fetchUsers();
      } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
    async resetPassword(u) {
      const pw = prompt(`Enter new password for ${u.username}:\n(Min 8 chars, uppercase, lowercase, number, special char)`);
      if (!pw) return;
      try {
        await axios.post(`/users/${u.id}/reset-password`, { new_password: pw });
        alert(`Password reset for ${u.username}`);
      } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
  },
};
</script>
