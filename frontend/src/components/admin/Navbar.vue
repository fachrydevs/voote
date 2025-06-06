<template>
   <nav class="px-4  py-3">
        <div class="flex items-center  ">
            
            
            <button @click="$emit('toggle-sidebar')" class="p-2 sm:hidden rounded-md  hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

    
  </nav>
</template>

<script setup>
import { useRoute } from "vue-router";
defineEmits(['toggle-sidebar']);

const route = useRoute();


// Add this function in your script setup



</script>

<style>
/* Add any custom styles here if needed */
</style>