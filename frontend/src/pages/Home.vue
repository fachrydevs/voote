<template>
  <div class="min-h-screen bg-white px-6 py-4">
    <!-- Navbar -->
    <Navbar/>
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-500">{{ error }}</p>
    </div>

    <!-- Main Content -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <!-- Card -->
      <div v-for="item in card" :key="item.id">
        <Card :title="item.title" :id="item.id" :imageUrl="item.imageUrl" :description="item.description" :endDate="formatDate(item.end_date)"/>
      </div>
      <!-- Empty Card Placeholder -->
    </div>
  </div>
</template>

<script setup>
import Card from '@/components/elections/Card.vue'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue';
import api from '@/services/axios';
import Navbar from '@/components/Navbar.vue'

const card = ref([''])
const loading = ref(true)
const error = ref(null)

// Format date function
const formatDate = (dateString) => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  
  // Check if date is valid
  if (isNaN(date.getTime())) return dateString;
  
  // Format options
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
  };
  
  return date.toLocaleDateString('id-ID', options);
}

const getElection = async () => {
    try {
        const response = await api.get('/elections/active');

        console.log(response.data.data);
        card.value = response.data.data;
    } catch (error) {
        console.log(error); 
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    getElection();
})
</script>

<style scoped>
</style>