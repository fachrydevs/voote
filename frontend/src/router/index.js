import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/pages/auth/Login.vue'
import Register from '@/pages/auth/Register.vue'
import Home from '@/pages/Home.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
        path: '/',
        component: Home
    },

        {
            path: "/login",
            component: Login
        },

        {
            path: "/register",
            component: Register
        },

        {
        path: '/howtovote',
        name: 'HowToVote',
        component: () => import('../pages/Howtovote.vue')
        }
  ],
})

export default router;
