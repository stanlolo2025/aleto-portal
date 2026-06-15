<template>
  <div id="app">
    <nav v-if="isAuthenticated" class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
      <div class="container-fluid">
        <router-link class="navbar-brand fw-bold" to="/dashboard">
          🏘️ Aleto Clan Portal
        </router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/dashboard">Dashboard</router-link>
            </li>

            <!-- Registry Dropdown -->
            <li class="nav-item dropdown" v-if="isAdmin">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Registry</a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/villagers">👤 All Members</router-link></li>
                <li><router-link class="dropdown-item" to="/villagers/register">➕ Register New</router-link></li>
                <li><router-link class="dropdown-item" to="/households">🏠 Households</router-link></li>
              </ul>
            </li>

            <!-- Grants Dropdown -->
            <li class="nav-item dropdown" v-if="isAdmin">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Grants</a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/grants">💰 Manage Grants</router-link></li>
                <li><router-link class="dropdown-item" to="/grants/history">📜 Grant History</router-link></li>
                <li><router-link class="dropdown-item" to="/payments">💳 Payment Runs</router-link></li>
              </ul>
            </li>

            <!-- Modules Dropdown -->
            <li class="nav-item dropdown" v-if="isAdmin">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Modules</a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/healthcare">🏥 Healthcare</router-link></li>
                <li><router-link class="dropdown-item" to="/education">📚 Education</router-link></li>
                <li><router-link class="dropdown-item" to="/projects">🏗️ Projects</router-link></li>
              </ul>
            </li>

            <li class="nav-item" v-if="isAdmin">
              <router-link class="nav-link" to="/reports">📊 Reports</router-link>
            </li>
            <li class="nav-item" v-if="isAdmin">
              <router-link class="nav-link" to="/announcements">📢 Notices</router-link>
            </li>
            <li class="nav-item" v-if="isAdmin || isAuditor">
              <router-link class="nav-link" to="/audit">🔍 Audit</router-link>
            </li>
            <li class="nav-item" v-if="isAdmin">
              <router-link class="nav-link" to="/users">⚙️ Users</router-link>
            </li>
            <li class="nav-item" v-if="isGovOfficial">
              <router-link class="nav-link" to="/reports">📊 Reports</router-link>
            </li>
          </ul>
          <div class="d-flex align-items-center">
            <span class="text-white me-3 small">{{ user?.name }}<br><span class="badge bg-secondary">{{ user?.role?.replace('_', ' ') }}</span></span>
            <button class="btn btn-outline-light btn-sm" @click="logout">Logout</button>
          </div>
        </div>
      </div>
    </nav>
    <div class="container-fluid" style="padding-top: 20px;">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user') || 'null'),
    };
  },
  computed: {
    isAuthenticated() { return !!localStorage.getItem('auth_token'); },
    isAdmin() { return this.user?.role === 'admin'; },
    isAuditor() { return this.user?.role === 'auditor'; },
    isGovOfficial() { return this.user?.role === 'government_official'; },
  },
  methods: {
    async logout() {
      try { await axios.post('/auth/logout'); } catch (e) {}
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.user = null;
      this.$router.push('/login');
    },
  },
};
</script>
