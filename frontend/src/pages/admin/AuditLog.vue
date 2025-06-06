<template>
  <Layout>
    <div class="p-4">
      <h2 class="text-2xl font-bold mb-6">Audit Log</h2>
      
      <!-- Filter Section -->
      <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input 
              v-model="filters.email" 
              type="email" 
              class="w-full border rounded-md p-2"
              placeholder="Filter by email"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
            <input 
              v-model="filters.activity_type" 
              type="text" 
              class="w-full border rounded-md p-2"
              placeholder="Filter by activity type"
            >
          </div>
          <div class="flex items-end">
            <button 
              @click="fetchAuditLogs" 
              class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition"
            >
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Audit Logs Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  {{ log.user?.name || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  {{ log.email || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  {{ log.role || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span 
                    :class="{
                      'px-2 py-1 rounded-full text-xs font-medium': true,
                      'bg-green-100 text-green-800': log.activity_type === 'login',
                      'bg-blue-100 text-blue-800': log.activity_type === 'Vote Created',
                      'bg-yellow-100 text-yellow-800': log.activity_type === 'update',
                      'bg-red-100 text-red-800': log.activity_type === 'delete'
                    }"
                  >
                    {{ log.activity_type }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm">
                  {{ log.activity_description }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  {{ log.ip_address }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  {{ new Date(log.created_at).toLocaleString() }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/components/admin/Layout.vue'
import { ref, onMounted } from 'vue'
import api from '@/services/axios'

const auditLogs = ref([])
const filters = ref({
  email: '',
  activity_type: ''
})

const fetchAuditLogs = async () => {
  try {
   
    // Mengubah endpoint ke audit-logs dan mengambil data dengan benar
    const response = await api.get('/dashboard/recent-activity')
    // Response dari endpoint audit-logs sudah dalam format paginate
    console.log(response.data.data)
    auditLogs.value = response.data
  } catch (error) {
    console.error('Error fetching audit logs:', error)
  }
}

onMounted(() => {
  fetchAuditLogs()
})
</script>

<style>

</style>