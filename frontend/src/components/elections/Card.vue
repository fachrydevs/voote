<template>
    <div class="bg-white border rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
      <div class="p-4">
        <h2 class="font-semibold text-lg mb-2 line-clamp-1">{{ title  }}</h2>
        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
          {{ description}}
        </p>
        <div class="flex justify-between items-center">
          <RouterLink 
            :to="`/elections/${id}`" 
            class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors"
          >
           Details
          </RouterLink>
          <span class="text-xs text-gray-500">{{ endDate }}</span>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    id: {
      type: [String, Number],
      default: 1
    },
    title: {
      type: String,
      default: ''
    },
    description: {
      type: String,
      default: ''
    },
    imageUrl: {
      type: String,
      default: 'https://placehold.co/600x400'
    },
    endDate: {
      type: String,
      default: ''
    }
  })
  </script>
  
  <style>
  .line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>