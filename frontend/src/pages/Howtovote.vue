<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-purple-50 to-purple-100 p-8 font-sans text-black">
    <!-- Navbar -->
    <nav class="flex items-center justify-between border-b border-purple-300 pb-4 mb-8">
      <h1 class="text-2xl font-bold text-purple-700">voote</h1>
      <div class="flex items-center gap-4">
        <a href="#" class="text-sm text-gray-700 hover:text-purple-600 transition">How to vote</a>
        <button class="bg-purple-600 text-white px-4 py-1.5 rounded-md hover:bg-purple-700 transition text-sm font-medium">
          Login
        </button>
      </div>
    </nav>

    <!-- Title -->
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">How to Vote</h2>

    <!-- Steps -->
    <div class="grid md:grid-cols-3 gap-6">

      <!-- Step 1 -->
      <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-3">➊ Select the election you want to vote</h3>
        <div class="bg-white border rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-200 h-40 flex items-center justify-center">
          <div class="flex space-x-4">
            <div class="w-16 h-24 bg-gray-300 rounded"></div>
            <div class="w-16 h-24 bg-gray-300 rounded"></div>
          </div>
        </div>
        <div class="p-4">
          <h2 class="font-semibold text-lg mb-2">Pemilihan Ketua Osis</h2>
          <p class="text-sm text-gray-600 mb-4">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
          </p>
          <a href="#" class="inline-flex items-center text-sm font-medium text-purple-600 hover:underline">
            Vote Sekarang <span class="ml-1"></span>
          </a>
        </div>
      </div>
      </div>

      <!-- Step 2 -->
      <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-3">➋ Click “Vote Sekarang”</h3>
        <p class="text-sm text-gray-600 mb-4">Date: 24 May 2025 - 27 May 2025</p>
        <button class="bg-purple-700 text-white w-full py-2 rounded-lg hover:bg-purple-800 transition">
          Vote Sekarang
        </button>
      </div>

      <!-- Step 3 -->
      <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
        <h3 class="text-lg font-semibold mb-3">➌ Choose your candidate</h3>
        <div class="bg-gray-200 w-full h-32 rounded-lg mb-4 flex items-center justify-center text-gray-500">
          Foto Kandidat
        </div>
        <p class="font-semibold">Name: Nikeisha Kamila</p>
        <p class="text-sm text-gray-700 mb-4">Visi & Misi: Idk just vote me guys, pls</p>
        <button class="bg-purple-700 text-white w-full py-2 rounded-lg hover:bg-purple-800 transition">
          Vote Kandidat 1
        </button>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'HowToVote'
}
</script>
