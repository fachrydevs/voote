<template>
  <Layout @show-election-modal="showElectionModal = true">
    <div class="hidden md:flex items-center  justify-end py-2 space-x-4">
      <button
        @click="showElectionModal = true"
        class="flex items-center space-x-2 bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        <span>Add Election</span>
      </button>
    </div>
    <div>
      <select name="elections" id="elections" v-model="selectedElection" @change="fetchDashboardData">
        <option value="">Semua Election</option>
        <option v-for="election in elections" :key="election.id" :value="election.id">{{ election.title }}</option>
      </select>
    </div>
    <div
      v-if="showElectionModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] flex flex-col"
      >
        <div class="p-6">
          <h2 class="text-2xl font-bold mb-4">Add New Election</h2>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6 pt-0">
          <!-- Election Form -->
          <form @submit.prevent="submitElection" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1">Title</label>
              <input
                v-model="electionForm.title"
                type="text"
                class="w-full border rounded-md p-2"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Description</label>
              <textarea
                v-model="electionForm.description"
                class="w-full border rounded-md p-2"
                rows="3"
                required
              ></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Start Date</label>
                <input
                  v-model="electionForm.start_date"
                  type="datetime-local"
                  step="1"
                  class="w-full border rounded-md p-2"
                  required
                  @input="formatDateTime($event, 'start_date')"
                />
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">End Date</label>
                <input
                  v-model="electionForm.end_date"
                  type="datetime-local"
                  step="1"
                  class="w-full border rounded-md p-2"
                  required
                  @input="formatDateTime($event, 'end_date')"
                />
              </div>

            <!-- Candidates Section -->
            <div class="mt-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Candidates</h3>
                <button
                  @click.prevent="openCandidateModal"
                  class="bg-purple-600 text-white p-2 rounded-full hover:bg-purple-700"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
              </div>

              <div
                v-for="(candidate, index) in electionForm.candidates"
                :key="index"
                class="border p-4 rounded-md mb-4"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-medium">{{ candidate.name }}</h4>
                    <p class="text-sm text-gray-600">
                      {{ candidate.description }}
                    </p>
                  </div>
                  <button
                    @click.prevent="removeCandidate(index)"
                    class="text-red-500 hover:text-red-700"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-5 w-5"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <div class="p-6 border-t">
              <div class="flex justify-end gap-2">
                <button
                  @click="showElectionModal = false"
                  type="button"
                  class="px-4 py-2 border rounded-md hover:bg-gray-100"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
                >
                  Create Election
                </button>
              </div>
            </div>
            <div v-if="showCandidateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60]">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Add New Candidate</h2>
                
                <form @submit.prevent="submitCandidate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input v-model="candidateForm.name" type="text" class="w-full border rounded-md p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Description</label>
                        <textarea v-model="candidateForm.description" class="w-full border rounded-md p-2" rows="3" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Image</label>
                        <input 
                            type="file" 
                            @change="handleImageUpload"
                            accept="image/*"
                            class="w-full border rounded-md p-2"
                        >
                        <p v-if="candidateForm.image" class="text-sm text-gray-600 mt-1">Selected file: {{ candidateForm.image.name }}</p>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button @click="showCandidateModal = false" type="button" class="px-4 py-2 border rounded-md hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Add Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
          </form>
        </div>

      </div>
    </div>

    

    <!-- Dashboard Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Users</h3>
        <p v-if="isLoading" class="text-2xl font-bold animate-pulse bg-gray-200 h-8 w-20 rounded"></p>
        <p v-else class="text-2xl font-bold">{{ statistics.total_users }}</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Votes</h3>
        <p v-if="isLoading" class="text-2xl font-bold animate-pulse bg-gray-200 h-8 w-20 rounded"></p>
        <p v-else class="text-2xl font-bold">{{ statistics.total_votes }}</p>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <!-- Votes Per Day Chart -->
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Votes Last 7 Days</h3>
        <canvas ref="votesChart"></canvas>
      </div>

      <!-- Candidates Votes Chart (tampil hanya jika ada election yang dipilih) -->
      <div v-if="selectedElection" class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Perbandingan Votes Kandidat</h3>
        <canvas ref="candidatesChart"></canvas>
      </div>

      <div v-else class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Top Elections</h3>
        <canvas ref="topElectionsChart"></canvas>
      </div>
    </div>

    <!-- Top Elections Table (tampil hanya jika tidak ada election yang dipilih) -->
    <div v-if="!selectedElection" class="bg-white p-4 rounded-lg shadow mb-6">
      <h3 class="text-lg font-semibold mb-4">Top Elections</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Votes</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="election in statistics.top_elections" :key="election.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ election.title }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ election.votes_count }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Candidates Votes Table (tampil hanya jika ada election yang dipilih) -->
    <div v-else class="bg-white p-4 rounded-lg shadow mb-6">
      <h3 class="text-lg font-semibold mb-4">Votes per Candidate</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidate</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Votes</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="candidate in statistics.candidates" :key="candidate.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ candidate.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ candidate.votes_count }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ candidate.percentage }}%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Existing election selection code -->
  </Layout>
</template>

<script setup>
import Layout from '@/components/admin/Layout.vue'

import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import api from "@/services/axios"
import Chart from "chart.js/auto";
import Swal from 'sweetalert2'

const token = localStorage.getItem("token");
const route = useRoute();
const selectedElection = ref('');
const showElectionModal = ref(false);
const showCandidateModal = ref(false);
const elections = ref([])
const isLoading = ref(true);
const candidateForm = ref({
  name: "",
  description: "",
  image: "",
});

const electionForm = ref({
  title: "",
  description: "",
  start_date: "",
  end_date: "",
  is_active: true,
  candidates: [],
});

const candidatesChart = ref(null);
let candidatesChartInstance = null;
const topElectionsChart = ref(null);
let topElectionsChartInstance = null;



const getElections = async () => {
  try {
    const response = await api.get("/elections");
    elections.value = response.data;
    console.log(response.data);
  } catch (error) {
    console.error("Error fetching elections:", error);
    return [];
  }
};

const formatDateTime = (event, field) => {
  const date = new Date(event.target.value);
  const isoString = date.toISOString();
  electionForm.value[field] = isoString.slice(0, 19).replace('Z', '');
};

const openCandidateModal = () => {
  showCandidateModal.value = true;
};

// Add this in the script setup section
const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        candidateForm.value.image = file;
    }
};

// Modify the submitCandidate function
const submitCandidate = () => {
    const formData = new FormData();
    formData.append('name', candidateForm.value.name);
    formData.append('description', candidateForm.value.description);
    if (candidateForm.value.image) {
        formData.append('image', candidateForm.value.image);
    }
    formData.append('order_number', electionForm.value.candidates.length + 1);

    // Create URL for preview if needed
    const imageUrl = candidateForm.value.image ? URL.createObjectURL(candidateForm.value.image) : null;

    electionForm.value.candidates.push({
        name: candidateForm.value.name,
        description: candidateForm.value.description,
        image: imageUrl,
        imageFile: candidateForm.value.image,
        order_number: electionForm.value.candidates.length + 1,
    });

    // Reset form and close modal
    candidateForm.value = {
        name: "",
        description: "",
        image: null,
    };
    showCandidateModal.value = false;
};

// Modify the submitElection function
const submitElection = async () => {
    try {
        const formData = new FormData();
        formData.append('title', electionForm.value.title);
        formData.append('description', electionForm.value.description);
        formData.append('start_date', electionForm.value.start_date);
        formData.append('end_date', electionForm.value.end_date);
        formData.append('is_active', electionForm.value.is_active);

        // Append candidates
        electionForm.value.candidates.forEach((candidate, index) => {
            formData.append(`candidates[${index}][name]`, candidate.name);
            formData.append(`candidates[${index}][description]`, candidate.description);
            formData.append(`candidates[${index}][order_number]`, candidate.order_number);
            if (candidate.imageFile) {
                formData.append(`candidates[${index}][image]`, candidate.imageFile);
            }
        });

        const response = await api.post("/elections", formData, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
            }
        });

        showElectionModal.value = false;
        // Reset form
        electionForm.value = {
            title: "",
            description: "",
            start_date: "",
            end_date: "",
            is_active: true,
            candidates: [],
        };
        
        // Show success message
        await Swal.fire({
            title: 'Berhasil!',
            text: 'Election berhasil ditambahkan',
            icon: 'success',
            confirmButtonColor: '#9333EA'
        });
        
        getElections(); // Refresh the elections list
    } catch (error) {
        console.error("Error creating election:", error);
        // Show error message
        await Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Gagal menambahkan election',
            icon: 'error',
            confirmButtonColor: '#9333EA'
        });
    }
};

const removeCandidate = (index) => {
  electionForm.value.candidates.splice(index, 1);
  // Update order numbers
  electionForm.value.candidates.forEach((candidate, idx) => {
    candidate.order_number = idx + 1;
  });
};

const statistics = ref({
  total_users: 0,
  total_elections: 0,
  total_votes: 0,
  active_elections: 0,
  votes_per_day: [],
  users_by_role: [],
  top_elections: [],
  candidates: []
});

const votesChart = ref(null);
const usersChart = ref(null);
let votesChartInstance = null;
let usersChartInstance = null;

const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    const params = {};
    if (selectedElection.value) {
      params.election_id = selectedElection.value;
    }
    const response = await api.get('/dashboard/statistics', { params });
    statistics.value = response.data;
    renderCharts();
  } catch (error) {
    console.error('Error fetching dashboard statistics:', error);
    await Swal.fire({
      title: 'Error!',
      text: 'Gagal memuat data statistik',
      icon: 'error',
      confirmButtonColor: '#9333EA'
    });
  } finally {
    isLoading.value = false;
  }
};

const renderCharts = () => {
  // Destroy existing charts if they exist
  if (votesChartInstance) votesChartInstance.destroy();
  if (usersChartInstance) usersChartInstance.destroy();
  if (candidatesChartInstance) candidatesChartInstance.destroy();
  // Votes per day chart
  const votesData = statistics.value.votes_per_day;
  votesChartInstance = new Chart(votesChart.value, {  
    type: 'line',
    data: {
      labels: votesData.map(item => new Date(item.date).toLocaleDateString()),
      datasets: [{
        label: 'Votes',
        data: votesData.map(item => item.count),
        borderColor: 'rgb(147, 51, 234)',
        tension: 0.1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
      }
    }
  });

  if (selectedElection.value && candidatesChart.value) {
    const candidatesData = statistics.value.candidates;
    candidatesChartInstance = new Chart(candidatesChart.value, {
      type: 'bar',
      data: {
        labels: candidatesData.map(item => item.name),
        datasets: [{
          label: 'Jumlah Votes',
          data: candidatesData.map(item => item.votes_count),
          backgroundColor: 'rgb(147, 51, 234)',
          borderColor: 'rgb(147, 51, 234)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  }

  if (!selectedElection.value) {
    if (topElectionsChartInstance) {
      topElectionsChartInstance.destroy();
    }

    const ctx = topElectionsChart.value.getContext('2d');
    topElectionsChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: statistics.value.top_elections.map(election => election.title),
        datasets: [{
          label: 'Total Votes',
          data: statistics.value.top_elections.map(election => election.votes_count),
          backgroundColor: '#7e22ce',
          borderColor: '#7e22ce',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  }
};

onMounted(() => {
  getElections();
  fetchDashboardData();
});
</script>

  <style>
</style>
