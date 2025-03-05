import { createRouter, createWebHistory } from "vue-router";
import LoginForm from "@/components/LoginForm.vue";
import RegisterForm from "@/components/RegisterForm.vue";
import DashboardView from "@/view/DashboardView.vue";
import { useAuthStore } from "@/store/AuthStore";

const routes = [
  { path: "/", component: LoginForm },
  { path: "/register", component: RegisterForm },
  { path: "/dashboard", component: DashboardView }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach(async (to) => {
    const publicPages = ['/','/register'];
    const authRequired = !publicPages.includes(to.path);
    const auth = useAuthStore();
    if (!authRequired && auth.token) {
        window.location.href = '/dashboard' 
    }
    if (authRequired && !auth.token) {
        return '/';
    }
    
});

export default router;