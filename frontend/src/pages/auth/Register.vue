<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar voote</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar voote sekarang agar bisa melakukan voting</p>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <div class="mt-1 relative">
            <input v-model="username" type="text" id="username" class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-purple-500" />
            <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">👤</span>
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <div class="mt-1 relative">
            <input v-model="email" type="email" id="email" class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-purple-500" />
            <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">📧</span>
          </div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <div class="mt-1 relative">
            <input :type="showPassword ? 'text' : 'password'" v-model="password" id="password" class="w-full border border-gray-300 rounded px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-purple-500" />
            <span @click="togglePassword" class="absolute inset-y-0 right-3 flex items-center text-gray-400 cursor-pointer">
              {{ showPassword ? '🙈' : '👁️' }}
            </span>
          </div>
        </div>

        <button type="submit" class="w-full bg-purple-600 text-white font-semibold py-2 rounded hover:bg-purple-700 shadow-md">
          Daftar Sekarang
        </button>
      </form>

      <p class="text-center text-sm text-gray-600">
        Sudah punya akun ?
        <a href="/login" class="text-purple-600 font-medium hover:underline">Masuk disini</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const username = ref('')
const email = ref('')
const password = ref('')
const showPassword = ref(false)

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleRegister = () => {
  console.log('Daftar dengan:', username.value, email.value, password.value)
  // Tambahkan logika registrasi di sini (API, validasi, dll)
}
</script>
