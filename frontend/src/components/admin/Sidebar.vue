<template>
    <aside :class="[
        'h-screen',
        'transition-all duration-300 ease-in-out',
        isOpen ? 'w-64' : 'w-20',
        'sm:relative absolute z-30',
        isOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0',
        'bg-white'
    ]">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16">
                <h1 class="font-semibold text-2xl text-purple-600">
                    <RouterLink to="/" class="flex items-center">
                        voote
                        <span v-if="!isOpen" class="hidden lg:inline">...</span>
                    </RouterLink>
                </h1>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-4">
                <RouterLink 
                    :to="{ name: 'Dashboard'}"
                    class="flex items-center space-x-3 font-semibold p-2 rounded transition-colors"
                    active-class="text-purple-600 bg-purple-50"
                    exact-active-class="text-purple-700 bg-purple-100 font-bold"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span :class="isOpen ? 'inline' : 'hidden lg:inline'">Dashboard</span>
                </RouterLink>

                <!-- Add Election Button (Mobile Only) -->
               

                <RouterLink 
                    :to="{ name: 'AuditLog'}"
                    class="flex items-center space-x-3 font-semibold p-2 rounded transition-colors"
                    active-class="text-purple-600 bg-purple-50"
                    exact-active-class="text-purple-700 bg-purple-100 font-bold"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span :class="isOpen ? 'inline' : 'hidden lg:inline'">Audit Log</span>
                </RouterLink>
                <button     
                    @click="$emit('show-election-modal')"
                    class="md:hidden flex items-center space-x-3 font-semibold p-2 rounded transition-colors text-purple-600 hover:bg-purple-50 w-full"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span :class="isOpen ? 'inline' : 'hidden'">Add Election</span>
                </button>
                <button @click="$emit('toggle-sidebar')" class="cursor-pointer sm:hidden mt-4">
                    Close Menu
                </button>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

defineEmits(['toggle-sidebar', 'show-election-modal']);
defineProps({
    isOpen: {
        type: Boolean,
        default: true
    }
});
</script>