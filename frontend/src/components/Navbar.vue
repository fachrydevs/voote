<template>
  <nav class="flex items-center justify-between border-purple-400 pb-4 mb-6 relative">
    <h1 class="text-xl font-semibold text-purple-700"><RouterLink to="/">voote</RouterLink></h1>
    
    <!-- Mobile menu button -->
    <button @click="toggleMenu" class="md:hidden text-purple-700 focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path v-if="isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    
    <!-- Desktop menu -->
    <div class="hidden md:flex items-center gap-4">
      <RouterLink class="px-2 py-1.5 rounded-md transition text-sm" active-class="text-purple-600" :to="{ name: 'Home' }">Home</RouterLink>
      <RouterLink class="px-2 py-1.5 rounded-md transition text-sm" active-class="text-purple-600" :to="{ name: 'Howtovote' }">How to vote</RouterLink>
      <button v-if="!isLogin" class="bg-purple-600 text-white px-4 py-1.5 rounded-md hover:bg-purple-700 transition text-sm font-semibold">
        <RouterLink to="/login">Login</RouterLink>
      </button>
      <button v-if="isAdmin" class="px-2 py-1.5 rounded-md transition text-sm">
        <RouterLink active-class="text-purple-600" exact-active-class="text-purple-400" :to="{ name: 'Dashboard' }">Dashboard</RouterLink>
      </button>
      <button v-if="isLogin" @click="Logout" class="bg-purple-600 text-white px-4 py-1.5 rounded-md hover:bg-purple-700 transition text-sm font-semibold">
        Logout
      </button>
    </div>
    
    <!-- Mobile menu (dropdown) -->
    <div v-if="isMenuOpen" class="absolute top-full right-0 left-0 bg-white shadow-md rounded-md py-2 z-10 md:hidden">
      <RouterLink @click="closeMenu" class="block px-4 py-2 hover:bg-purple-50 transition text-sm" active-class="text-purple-600" :to="{ name: 'Home' }">Home</RouterLink>
      <RouterLink @click="closeMenu" class="block px-4 py-2 hover:bg-purple-50 transition text-sm" active-class="text-purple-600" :to="{ name: 'Howtovote' }">How to vote</RouterLink>
      <RouterLink v-if="!isLogin" @click="closeMenu" to="/login" class="block px-4 py-2 hover:bg-purple-50 transition text-sm font-semibold">Login</RouterLink>
      <RouterLink v-if="isAdmin" @click="closeMenu" class="block px-4 py-2 hover:bg-purple-50 transition text-sm" active-class="text-purple-600" exact-active-class="text-purple-400" :to="{ name: 'Dashboard' }">Dashboard</RouterLink>
      <button v-if="isLogin" @click="handleLogout" class="w-full text-left px-4 py-2 hover:bg-purple-50 transition text-sm font-semibold">Logout</button>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isAdmin = ref(false);
const isLogin = ref(false);
const isMenuOpen = ref(false);

const toggleMenu = () => {
isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = () => {
isMenuOpen.value = false;
};

const handleLogout = () => {
Logout();
closeMenu();
};

const Logout = () => {
localStorage.removeItem('token');
localStorage.removeItem('accessToken');
localStorage.removeItem('user');
localStorage.removeItem('role');
isLogin.value = false;
router.push('/login');
};

if(localStorage.getItem('token') !== null) {
isLogin.value = true;
}

if(localStorage.getItem('role') === 'admin') {
isAdmin.value = true;
}
</script>