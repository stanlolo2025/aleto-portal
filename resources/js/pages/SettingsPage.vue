<template>
  <div>
    <h2>Settings</h2>

    <div class="row mt-3">
      <!-- Profile Info -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white"><h6 class="mb-0">Profile Information</h6></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <p class="form-control-plaintext fw-bold">{{ user?.name }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <p class="form-control-plaintext"><code>{{ user?.username }}</code></p>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <p class="form-control-plaintext">{{ user?.email }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <p class="form-control-plaintext"><span class="badge bg-primary text-capitalize">{{ user?.role?.replace('_', ' ') }}</span></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Change Password -->
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-warning bg-opacity-75"><h6 class="mb-0">Change Password</h6></div>
          <div class="card-body">
            <form @submit.prevent="changePassword">
              <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input v-model="passwordForm.current_password" type="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">New Password</label>
                <input v-model="passwordForm.new_password" type="password" class="form-control" required>
                <small class="text-muted">Min 8 chars, uppercase, lowercase, number, special character</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input v-model="passwordForm.new_password_confirmation" type="password" class="form-control" required>
              </div>
              <div v-if="passwordError" class="alert alert-danger">{{ passwordError }}</div>
              <div v-if="passwordSuccess" class="alert alert-success">{{ passwordSuccess }}</div>
              <button type="submit" class="btn btn-warning w-100">Update Password</button>
            </form>
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
      user: JSON.parse(localStorage.getItem('user') || 'null'),
      passwordForm: { current_password: '', new_password: '', new_password_confirmation: '' },
      passwordError: '',
      passwordSuccess: '',
    };
  },
  methods: {
    async changePassword() {
      this.passwordError = '';
      this.passwordSuccess = '';
      try {
        const { data } = await axios.post('/auth/change-password', this.passwordForm);
        this.passwordSuccess = data.message;
        this.passwordForm = { current_password: '', new_password: '', new_password_confirmation: '' };
      } catch (e) {
        this.passwordError = e.response?.data?.message || 'Error changing password';
      }
    },
  },
};
</script>
