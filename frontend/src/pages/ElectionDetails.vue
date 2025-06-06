<template>
  <div class="min-h-screen bg-white px-6 py-4">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-500">{{ error }}</p>
    </div>

    <!-- Content -->
    <div v-else class="max-w-7xl mx-auto">
      <!-- Header Section -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ election.title }}</h1>
        <p class="text-gray-600">{{ election.description }}</p>
        <div class="mt-4 flex items-center gap-4">
          <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">Active</span>
          <span class="text-gray-500 text-sm">Berakhir pada: {{ formatDate(election.end_date) }}</span>
        </div>
      </div>

      <!-- Candidates Section -->
      <div class="grid grid-cols-1 place-content-center md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="candidate in election.candidates" :key="candidate.id" class="bg-white rounded-lg border shadow-sm hover:shadow-md transition-shadow p-4">
          <div class="aspect-square rounded-lg bg-gray-100 mb-4 overflow-hidden">
              <img 
                :src="candidate.image_url" 
                :alt="candidate.name"
                class="w-full h-full object-cover"
              >
          </div>
          <h3 class="text-xl font-semibold mb-2">{{ candidate.name }}</h3>
          <p class="text-gray-600 text-sm mb-4">{{ candidate.description }}</p>
          <button 
            @click="handleVote(candidate.id)" 
            class="w-full bg-purple-600 disabled:cursor-not-allowed text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors"
            :disabled="hasVoted"
          >
            {{ hasVoted ? 'Sudah Memilih' : 'Pilih Kandidat' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/axios'
import Swal from 'sweetalert2'
const token = localStorage.getItem('token')
console.log(token)

const route = useRoute()
const router = useRouter()
const election = ref({})
const loading = ref(true)
const error = ref(null)
const hasVoted = ref(false)

// Format date function
const formatDate = (dateString) => {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  
  if (isNaN(date.getTime())) return dateString
  
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
  }
  
  return date.toLocaleDateString('id-ID', options)
}

// Fetch election details
const fetchElectionDetails = async () => {
  try {
    loading.value = true
    const response = await api.get(`/elections/${route.params.id}`, {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': token ? `Bearer ${token}` : ''
      }
    })
    election.value = response.data
    console.log(response.data)
    
    // Check if user has already voted for this election
    if (token) {
      try {
        const voteStatusResponse = await api.get(`/elections/${route.params.id}/vote-status`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        hasVoted.value = voteStatusResponse.data.has_voted || false
      } catch (voteErr) {
        console.error('Error checking vote status:', voteErr)
      }
    }
    
    console.log(response.data)
  } catch (err) {
    error.value = 'Gagal memuat data pemilihan'
    console.error(err)
  } finally {
    loading.value = false
  }
}

// Handle vote submission
const handleVote = async (candidateId) => {
  try {
    // Check if user is authenticated
    if (!token) {
      alert('Anda harus login terlebih dahulu untuk melakukan pemilihan')
      return
    }
    
    const response = await api.post(`/elections/${route.params.id}/vote`, {
      candidate_id: candidateId
    })
    
    console.log('Vote response:', response.data)
    hasVoted.value = true
    await Swal.fire({
            title: 'Berhasil!',
            text: 'Pemilihan berhasil ditambahkan',
            icon: 'success',
            confirmButtonColor: '#9333EA'
        });
     
  } catch (err) {
    console.error('Vote error:', err.response?.data || err.message)
    
    if (err.response?.status === 400) {
      await Swal.fire({
            title: 'Error!',
            text: err.response.data.message || 'Gagal melakukan pemilihan: Format data tidak valid',
            icon: 'error',
            confirmButtonColor: '#9333EA'
        });
    } else if (err.response?.status === 401) {
      await Swal.fire({
            title: 'Error!',
            text: 'Sesi login Anda telah berakhir. Silakan login kembali.',
            icon: 'error',
            confirmButtonColor: '#9333EA'
        });
    } else if (err.response?.status === 403) {
      await Swal.fire({
            title: 'Error!',
            text: 'Anda sudah melakukan pemilihan',
            icon: 'error',
            confirmButtonColor: '#9333EA'
        });
      hasVoted.value = true
    } else {
      
      await Swal.fire({
            title: 'Error!',
            text: 'Gagal melakukan pemilihan, coba lagi nanti',
            icon: 'error',
            confirmButtonColor: '#9333EA'
        });
    }
  } finally {
    router.push('/')
  }
}

onMounted(() => {
  fetchElectionDetails()
})
</script>