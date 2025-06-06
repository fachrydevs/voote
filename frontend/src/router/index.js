import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/pages/auth/Login.vue'
import Register from '@/pages/auth/Register.vue'
import Home from '@/pages/Home.vue'
import Howtovote from '@/pages/Howtovote.vue'
import Dashboard from '@/pages/admin/Dashboard.vue'
import AuditLog from '@/pages/admin/AuditLog.vue'
import ElectionDetails from '@/pages/ElectionDetails.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {   
        path: '/',
        name: 'Home',
        component: Home
    },

        {
            path: "/login",
            name: 'Login',
            component: Login
        },

        {
            path: "/register",
            name: 'Register',
            component: Register
        },
        {
            path: '/howtovote',
            component: Howtovote,
            name: 'Howtovote',
        },
        {
            path: '/elections/:id',
            component: ElectionDetails,
            meta : {
                requiresAuth: true
            }
        },
        {
            path: '/admin/dashboard',
            component: Dashboard,
            name: 'Dashboard',
            meta: {
                requiresAuth: true,
                requiresAdmin: true
            }
        },
        {
            path: '/admin/audit-log',
            name: 'AuditLog',
            component: AuditLog,
            meta: {
                requiresAuth: true,
                requiresAdmin: true
            }
        },

        
  ],
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const userData = localStorage.getItem('user');
    const role = localStorage.getItem('role')
    const isLogin = !!token && !!userData;
    let userRole = null;

    if (userData) {
        try {
            userRole = role;
            console.log(role);
        } catch (e) {   
            console.error(e);
            localStorage.removeItem('user');
            localStorage.removeItem('token');
        }
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
      if(!isLogin) {
        next({
            name: 'Login'
        })
         return
      }

      if (to.matched.some(record => record.meta.requiresAdmin)) {
        if (userRole !== 'admin') {
            next({
                name: 'Home',
                query: { error: 'Unauthorized'}
            });
            return
        }
      }
    }

      next()    
})


export default router;
