<template>
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body p-4">
          <h3 class="text-center mb-4">🏘️ Aleto Clan Portal</h3>
          <p class="text-center text-muted mb-4">Community Registry & Grant Management</p>

          <!-- Login Form -->
          <form v-if="!showMfa" @submit.prevent="login">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input v-model="username" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input v-model="password" type="password" class="form-control" required>
            </div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">
              {{ loading ? 'Logging in...' : 'Login' }}
            </button>
          </form>

          <!-- MFA Form -->
          <form v-else @submit.prevent="verifyMfa">
            <div class="alert alert-info">A 6-digit code has been sent to your registered email/phone.</div>
            <div class="mb-3">
              <label class="form-label">MFA Code</label>
              <input v-model="mfaCode" type="text" class="form-control" maxlength="6" required placeholder="Enter 6-digit code">
            </div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">Verify</button>
            <button type="button" class="btn btn-link w-100 mt-2" @click="resendMfa" :disabled="loading">
              Resend Code
            </button>
          </form>
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
      username: '',
      password: '',
      mfaCode: '',
      showMfa: false,
      mfaUserId: null,
      error: '',
      loading: false,
    };
  },
  methods: {
    async login() {
      this.error = '';
      this.loading = true;
      try {
        const { data } = await axios.post('/auth/login', {
          username: this.username,
          password: this.password,
        });

        if (data.requires_mfa) {
          this.showMfa = true;
          this.mfaUserId = data.user_id;
        } else {
          this.completeLogin(data);
        }
      } catch (e) {
        this.error = e.response?.data?.message || 'Login failed';
      } finally {
        this.loading = false;
      }
    },
    async verifyMfa() {
      this.error = '';
      this.loading = true;
      try {
        const { data } = await axios.post('/auth/mfa/verify', {
          user_id: this.mfaUserId,
          code: this.mfaCode,
        });
        this.completeLogin(data);
      } catch (e) {
        this.error = e.response?.data?.message || 'Invalid code';
      } finally {
        this.loading = false;
      }
    },
    async resendMfa() {
      try {
        await axios.post('/auth/mfa/resend', { user_id: this.mfaUserId });
        this.error = '';
        alert('New code sent!');
      } catch (e) {
        this.error = e.response?.data?.message || 'Could not resend';
      }
    },
    completeLogin(data) {
      localStorage.setItem('auth_token', data.token);
      localStorage.setItem('user', JSON.stringify(data.user));
      window.location.href = '/dashboard';
    },
  },
};
</script>
