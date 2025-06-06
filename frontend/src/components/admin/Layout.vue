<template>
    <div class="flex h-screen bg-gray-50">
        <Sidebar 
            :isOpen="isSidebarOpen" 
            @toggle-sidebar="toggleSidebar"
            @show-election-modal="$emit('show-election-modal')"
        />
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            <Navbar @toggle-sidebar="toggleSidebar" :isOpen="isSidebarOpen" />
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 px-6">
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';
import Navbar from '@/components/admin/Navbar.vue';

const isSidebarOpen = ref(true);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

defineEmits(['show-election-modal']);
</script>