<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Masuk voote</h2>
        <p class="text-sm text-gray-500 mt-1">Masuk terlebih dahulu sebelum melakukan voting</p>
      </div>

      <form @submit.prevent="login" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <div class="mt-1 relative">
            <input v-model="email" type="email" id="email" class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-purple-500" />
            <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
              📧
            </span>
          </div>
            <span class="text-xs text-red-500" v-if="errorMsg.email">{{ errorMsg.email[0] }}</span>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <div class="mt-1 relative">
            <input :type="showPassword ? 'text' : 'password'" v-model="password" id="password" class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-purple-500" />
            <span @click="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-400 cursor-pointer">
              {{ showPassword ? '🙈' : '👁️' }}
            </span>
          </div>
            <span class="text-xs text-red-500" v-if="errorMsg.password">{{ errorMsg.password[0] }}</span>
        </div>

        <button type="submit" class="w-full bg-purple-600 text-white font-semibold py-2 rounded hover:bg-purple-700 shadow-md">
          Masuk Sekarang
        </button>
      </form>

      <p class="text-center text-sm text-gray-600">
        Belum punya akun ?
        <a href="/register" class="text-purple-600 font-medium hover:underline">Daftar disini</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/axios';
import { useRouter } from 'vue-router'
import { parse } from 'vue/compiler-sfc';
import Swal from 'sweetalert2';

const router = useRouter();
const email = ref('')
const password = ref('')
const showPassword = ref(false)
const errorMsg = ref('')

const togglePassword = () => {
  showPassword.value = !showPassword.value
}




const login = async () => {
    try {
        const response = await api.post('/v1/login', {
            email: email.value,
            password: password.value,
        });
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        localStorage.setItem('role', response.data.user.role);
       
        router.push('/')
        
   
    } catch (error) {
        console.log(error.response.data);
        errorMsg.value = error.response.data
        console.log(error)

        if (error.response.status === 401) {
           Swal.fire({
                title: 'Error!',
                text: 'Email atau password salah',
                icon: 'error',
                confirmButtonColor: '#9333EA'
            });
        }
    }
};
</script>
