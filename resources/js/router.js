import LoginPage from './pages/LoginPage.vue';
import DashboardPage from './pages/DashboardPage.vue';
import VillagerList from './pages/VillagerList.vue';
import VillagerRegister from './pages/VillagerRegister.vue';
import VillagerDetail from './pages/VillagerDetail.vue';
import HouseholdsPage from './pages/HouseholdsPage.vue';
import GrantManager from './pages/GrantManager.vue';
import GrantHistoryPage from './pages/GrantHistoryPage.vue';
import PaymentRunsPage from './pages/PaymentRunsPage.vue';
import HealthcarePage from './pages/HealthcarePage.vue';
import EducationPage from './pages/EducationPage.vue';
import ProjectsPage from './pages/ProjectsPage.vue';
import ReportsPage from './pages/ReportsPage.vue';
import AnnouncementsPage from './pages/AnnouncementsPage.vue';
import MessagesPage from './pages/MessagesPage.vue';
import AuditLogViewer from './pages/AuditLogViewer.vue';
import UserManager from './pages/UserManager.vue';

const routes = [
    { path: '/login', component: LoginPage, meta: { requiresAuth: false } },
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', component: DashboardPage, meta: { requiresAuth: true } },
    { path: '/villagers', component: VillagerList, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/villagers/register', component: VillagerRegister, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/villagers/:id', component: VillagerDetail, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/households', component: HouseholdsPage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/grants', component: GrantManager, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/grants/history', component: GrantHistoryPage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/payments', component: PaymentRunsPage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/healthcare', component: HealthcarePage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/education', component: EducationPage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/projects', component: ProjectsPage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/reports', component: ReportsPage, meta: { requiresAuth: true, roles: ['admin', 'government_official'] } },
    { path: '/announcements', component: AnnouncementsPage, meta: { requiresAuth: true, roles: ['admin'] } },
    { path: '/messages', component: MessagesPage, meta: { requiresAuth: true } },
    { path: '/audit', component: AuditLogViewer, meta: { requiresAuth: true, roles: ['admin', 'auditor'] } },
    { path: '/users', component: UserManager, meta: { requiresAuth: true, roles: ['admin'] } },
];

export default routes;
