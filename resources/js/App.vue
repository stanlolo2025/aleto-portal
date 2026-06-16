<template>
  <div id="app">
    <!-- Top Navbar -->
    <nav v-if="isAuthenticated" class="navbar navbar-expand-lg navbar-dark bg-dark shadow d-none d-lg-block">
      <div class="container-fluid">
        <router-link class="navbar-brand fw-bold" to="/dashboard">🏘️ Aleto Clan Portal</router-link>
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto">
            <li class="nav-item dropdown" v-if="isAdmin">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Registry</a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/villagers">👤 All Members</router-link></li>
                <li><router-link class="dropdown-item" to="/villagers/register">➕ Register New</router-link></li>
                <li><router-link class="dropdown-item" to="/households">🏠 Households</router-link></li>
              </ul>
            </li>
            <li class="nav-item dropdown" v-if="isAdmin">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Grants</a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/grants">💰 Manage Grants</router-link></li>
                <li><router-link class="dropdown-item" to="/grants/history">📜 Grant History</router-link></li>
                <li><router-link class="dropdown-item" to="/payments">💳 Payment Runs</router-link></li>
              </ul>
            </li>
            <li class="nav-item dropdown" v-if="isAdmin">
              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Modules</a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/healthcare">🏥 Healthcare</router-link></li>
                <li><router-link class="dropdown-item" to="/education">📚 Education</router-link></li>
                <li><router-link class="dropdown-item" to="/projects">🏗️ Projects</router-link></li>
              </ul>
            </li>
            <li class="nav-item" v-if="isAdmin"><router-link class="nav-link" to="/reports">📊 Reports</router-link></li>
            <li class="nav-item" v-if="isAdmin"><router-link class="nav-link" to="/announcements">📢 Notices</router-link></li>
            <li class="nav-item" v-if="isAdmin || isAuditor"><router-link class="nav-link" to="/audit">🔍 Audit</router-link></li>
            <li class="nav-item" v-if="isAdmin"><router-link class="nav-link" to="/users">⚙️ Users</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/messages">✉️ Messages</router-link></li>
            <li class="nav-item"><router-link class="nav-link" to="/settings">⚙️ Settings</router-link></li>
            <li class="nav-item" v-if="isGovOfficial"><router-link class="nav-link" to="/reports">📊 Reports</router-link></li>
          </ul>
          <div class="d-flex align-items-center">
            <span class="text-white me-3 small">{{ user?.name }}<br><span class="badge bg-secondary">{{ user?.role?.replace('_', ' ') }}</span></span>
            <button class="btn btn-outline-light btn-sm" @click="logout">Logout</button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Top Bar -->
    <div v-if="isAuthenticated" class="d-lg-none mobile-top-bar">
      <div class="d-flex justify-content-between align-items-center px-3 py-2">
        <router-link to="/dashboard" class="text-white text-decoration-none fw-bold">🏘️ Aleto Clan</router-link>
        <div>
          <router-link to="/messages" class="text-white me-3"><i class="bi bi-envelope"></i></router-link>
          <button class="btn btn-sm btn-outline-light" @click="showMobileMenu = !showMobileMenu">☰</button>
        </div>
      </div>

      <!-- Mobile Slide Menu -->
      <div v-if="showMobileMenu" class="mobile-menu">
        <div class="mobile-menu-overlay" @click="showMobileMenu = false"></div>
        <div class="mobile-menu-content">
          <div class="p-3 bg-dark text-white">
            <h6 class="mb-0">{{ user?.name }}</h6>
            <small class="text-muted text-capitalize">{{ user?.role?.replace('_', ' ') }}</small>
          </div>
          <div class="mobile-menu-items">
            <router-link to="/dashboard" class="mobile-menu-item" @click="showMobileMenu=false">🏠 Dashboard</router-link>
            <div class="mobile-menu-divider">Registry</div>
            <router-link to="/villagers" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">👤 All Members</router-link>
            <router-link to="/villagers/register" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">➕ Register New</router-link>
            <router-link to="/households" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">🏠 Households</router-link>
            <div class="mobile-menu-divider">Grants</div>
            <router-link to="/grants" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">💰 Manage Grants</router-link>
            <router-link to="/grants/history" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">📜 Grant History</router-link>
            <router-link to="/payments" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">💳 Payment Runs</router-link>
            <div class="mobile-menu-divider">Modules</div>
            <router-link to="/healthcare" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">🏥 Healthcare</router-link>
            <router-link to="/education" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">📚 Education</router-link>
            <router-link to="/projects" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">🏗️ Projects</router-link>
            <div class="mobile-menu-divider">Other</div>
            <router-link to="/reports" class="mobile-menu-item" @click="showMobileMenu=false">📊 Reports</router-link>
            <router-link to="/announcements" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">📢 Notices</router-link>
            <router-link to="/messages" class="mobile-menu-item" @click="showMobileMenu=false">✉️ Messages</router-link>
            <router-link to="/audit" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin || isAuditor">🔍 Audit Log</router-link>
            <router-link to="/users" class="mobile-menu-item" @click="showMobileMenu=false" v-if="isAdmin">⚙️ Users</router-link>
            <router-link to="/settings" class="mobile-menu-item" @click="showMobileMenu=false">🔧 Settings</router-link>
            <a href="#" class="mobile-menu-item text-danger" @click.prevent="logout">🚪 Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" :class="{'pb-mobile': isAuthenticated}">
      <div class="container-fluid px-2 px-lg-3 pt-3">
        <router-view></router-view>
      </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div v-if="isAuthenticated && isAdmin" class="d-lg-none mobile-bottom-nav">
      <router-link to="/dashboard" class="mobile-nav-item" :class="{active: $route.path === '/dashboard'}">
        <span class="mobile-nav-icon">🏠</span>
        <span class="mobile-nav-label">Home</span>
      </router-link>
      <router-link to="/villagers" class="mobile-nav-item" :class="{active: $route.path.startsWith('/villagers')}">
        <span class="mobile-nav-icon">👥</span>
        <span class="mobile-nav-label">Registry</span>
      </router-link>
      <router-link to="/villagers/register" class="mobile-nav-item mobile-nav-add">
        <span class="mobile-nav-icon">➕</span>
        <span class="mobile-nav-label">Register</span>
      </router-link>
      <router-link to="/grants" class="mobile-nav-item" :class="{active: $route.path.startsWith('/grants')}">
        <span class="mobile-nav-icon">💰</span>
        <span class="mobile-nav-label">Grants</span>
      </router-link>
      <router-link to="/messages" class="mobile-nav-item" :class="{active: $route.path === '/messages'}">
        <span class="mobile-nav-icon">✉️</span>
        <span class="mobile-nav-label">Messages</span>
      </router-link>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      user: JSON.parse(localStorage.getItem('user') || 'null'),
      showMobileMenu: false,
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
      this.showMobileMenu = false;
      this.$router.push('/login');
    },
  },
};
</script>

<style>
/* Mobile Top Bar */
.mobile-top-bar { background: #1a1a2e; position: sticky; top: 0; z-index: 1000; }

/* Mobile Slide Menu */
.mobile-menu { position: fixed; inset: 0; z-index: 9999; }
.mobile-menu-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
.mobile-menu-content { position: absolute; top: 0; right: 0; width: 280px; height: 100%; background: #fff; overflow-y: auto; box-shadow: -5px 0 15px rgba(0,0,0,0.2); animation: slideIn 0.2s ease; }
@keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
.mobile-menu-items { padding: 0; }
.mobile-menu-item { display: block; padding: 14px 20px; color: #333; text-decoration: none; border-bottom: 1px solid #f0f0f0; font-size: 15px; }
.mobile-menu-item:hover, .mobile-menu-item.router-link-active { background: #f8f9fa; color: #0d6efd; }
.mobile-menu-divider { padding: 8px 20px; background: #f8f9fa; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #999; letter-spacing: 1px; }

/* Mobile Bottom Navigation */
.mobile-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e0e0e0; display: flex; justify-content: space-around; align-items: center; padding: 6px 0 env(safe-area-inset-bottom, 6px); z-index: 1000; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); }
.mobile-nav-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #666; font-size: 10px; padding: 4px 8px; min-width: 50px; }
.mobile-nav-item.active { color: #0d6efd; }
.mobile-nav-icon { font-size: 20px; line-height: 1; }
.mobile-nav-label { margin-top: 2px; }
.mobile-nav-add .mobile-nav-icon { background: #0d6efd; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-top: -15px; box-shadow: 0 2px 8px rgba(13,110,253,0.3); }

/* Main content padding for mobile bottom nav */
.pb-mobile { padding-bottom: 80px; }

/* Mobile responsive improvements */
@media (max-width: 768px) {
  .table-responsive { font-size: 12px; -webkit-overflow-scrolling: touch; }
  .table-responsive table { min-width: 600px; }
  h2 { font-size: 1.3rem; }
  h5 { font-size: 1.1rem; }
  .card { border-radius: 10px; }
  .card-body { padding: 0.75rem; }
  .card-header { padding: 0.6rem 0.75rem; font-size: 14px; }
  .btn { font-size: 13px; padding: 0.4rem 0.8rem; border-radius: 8px; }
  .btn-lg { font-size: 15px; padding: 0.6rem 1.2rem; }
  .form-control, .form-select { font-size: 16px !important; padding: 0.5rem 0.75rem; border-radius: 8px; }
  .form-label { font-size: 13px; margin-bottom: 0.2rem; }
  .container-fluid { padding-left: 10px; padding-right: 10px; }
  .row.g-3 { --bs-gutter-x: 0.5rem; --bs-gutter-y: 0.5rem; }
  .nav-tabs { font-size: 13px; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .nav-tabs .nav-link { white-space: nowrap; padding: 0.5rem 0.8rem; }
  .badge { font-size: 10px; }
  .modal-dialog { margin: 0.5rem; }
  .dropdown-menu { font-size: 14px; }
  
  /* Stack stat cards vertically on small phones */
  .stat-card-row .col-md-3 { flex: 0 0 50%; max-width: 50%; }
  
  /* Full-width buttons on mobile */
  .btn-mobile-full { width: 100%; margin-bottom: 0.5rem; }
  
  /* Better touch targets */
  .table td, .table th { padding: 0.5rem 0.4rem; }
  input[type="checkbox"] { width: 20px; height: 20px; }
  
  /* Mobile-friendly alerts */
  .alert { font-size: 13px; padding: 0.6rem; border-radius: 8px; }
}
</style>
