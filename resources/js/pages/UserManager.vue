<template>
  <div>
    <h2>User Management</h2>
    <div class="card mt-3">
      <div class="card-header d-flex justify-content-between">
        <strong>Portal Users</strong>
        <button class="btn btn-primary btn-sm" @click="showCreate = true">+ Add User</button>
      </div>
      <div class="card-body">
        <table class="table">
          <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
          <tbody>
            <tr v-for="u in users" :key="u.id">
              <td>{{ u.name }}</td>
              <td>{{ u.username }}</td>
              <td>{{ u.email }}</td>
              <td>
                <select :value="u.role" @change="changeRole(u.id, $event.target.value)" class="form-select form-select-sm" style="width:auto;display:inline">
                  <option value="admin">Admin</option>
                  <option value="auditor">Auditor</option>
                  <option value="government_official">Government Official</option>
                </select>
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create User Form -->
    <div class="card mt-3" v-if="showCreate">
      <div class="card-header"><strong>Create New User</strong></div>
      <div class="card-body">
        <form @submit.prevent="createUser">
          <div class="row">
            <div class="col-md-4 mb-2">
              <input v-model="newUser.name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="col-md-4 mb-2">
              <input v-model="newUser.username" class="form-control" placeholder="Username" required>
            </div>
            <div class="col-md-4 mb-2">
              <input v-model="newUser.email" type="email" class="form-control" placeholder="Email" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-2">
              <input v-model="newUser.password" type="password" class="form-control" placeholder="Password (min 8, mixed)" required>
            </div>
            <div class="col-md-4 mb-2">
              <select v-model="newUser.role" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="auditor">Auditor</option>
                <option value="government_official">Government Official</option>
              </select>
            </div>
            <div class="col-md-4 mb-2">
              <button type="submit" class="btn btn-success">Create</button>
              <button type="button" class="btn btn-secondary ms-1" @click="showCreate = false">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return { users: [], showCreate: false, newUser: { name: '', username: '', email: '', password: '', role: 'admin' } };
  },
  async mounted() {
    const { data } = await axios.get('/users');
    this.users = data.data;
  },
  methods: {
    async createUser() {
      try {
        await axios.post('/users', this.newUser);
        alert('User created!');
        this.showCreate = false;
        this.newUser = { name: '', username: '', email: '', password: '', role: 'admin' };
        const { data } = await axios.get('/users');
        this.users = data.data;
      } catch (e) { alert(e.response?.data?.message || JSON.stringify(e.response?.data?.errors)); }
    },
    async changeRole(id, role) {
      try {
        await axios.put(`/users/${id}/role`, { role });
        alert('Role updated!');
      } catch (e) { alert(e.response?.data?.message || 'Error'); }
    },
  },
};
</script>
