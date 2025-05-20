<template>
  <div class="min-h-screen bg-white px-6 py-4">
    <!-- Navbar -->
    <nav class="flex items-center justify-between border-b-2 border-purple-400 pb-4 mb-6">
      <h1 class="text-xl font-semibold text-purple-700">voote</h1>
      <div class="flex items-center gap-4">
        <a href="#" class="text-sm text-gray-700 hover:text-purple-600">How to vote</a>
        <button class="bg-purple-600 text-white px-4 py-1.5 rounded-md hover:bg-purple-700 transition text-sm font-semibold">
          Login
        </button>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <!-- Card -->
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
            Vote Sekarang <span class="ml-1">➡️</span>
          </a>
        </div>
      </div>
      <!-- Empty Card Placeholder -->
      <div class="bg-white border rounded-lg shadow-md h-[280px] opacity-10"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "HomePage",
};
</script>

<style scoped>
</style>
