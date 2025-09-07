<template>
    <div class="bg-gray-100 h-screen">
        <div class="flex flex-col md:flex-row h-full">
            <!-- Sidebar -->
            <aside
                :class="[
                    'fixed top-0 left-0 h-full transition-all duration-300 z-[1100] bg-white shadow-lg',
                    isSidebarOpen ? 'w-60' : 'w-0 overflow-hidden'
                ]"
            >
                <div class="card flex justify-center h-full">
                    <TieredMenu
                        :model="sideBarItems"
                        class="w-full md:w-60 h-full flex-shrink-0 !rounded-none"
                    >
                        <template #start>
                            <div class="flex items-center w-full pt-20 px-2">
                                <img
                                    :src="logoSrc"
                                    alt="MedPort Logo"
                                    class="h-10 w-10 mr-2"
                                />
                                <span class="text-xl font-semibold">MedPort</span>
                            </div>
                        </template>
                        <template #item="{ item, props, hasSubmenu }">
                            <Link
                                v-if="item.route"
                                :href="item.route"
                                class="no-underline"
                                v-bind="props.action"
                            >
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                            </Link>
                        </template>
                    </TieredMenu>
                </div>
            </aside>

            <!-- Main Content -->
            <main
                class="flex-1 transition-all duration-300 relative"
                :class="isSidebarOpen ? 'md:ml-60' : 'ml-0'"
            >

                <header class="fixed top-0 left-0 right-0 z-[1100]">
                    <Menubar :model="menuBarItems" class="w-full md:w-100 !border-l-0 !rounded-none">
                        <template #start>
                            <button
                                @click="toggleSidebar"
                                class="bg-transparent border-none p-2 mr-2 cursor-pointer hover:bg-gray-200 rounded"
                            >
                                <i class="pi pi-bars text-gray-700"></i>
                            </button>
                        </template>
                        <template #end>
                            <div class="flex items-center">
                                <button
                                    @click="toggleUserMenu"
                                    v-ripple
                                    class="relative overflow-hidden w-full border-0 bg-transparent flex items-start justify-center pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200"
                                >
                                    <Avatar
                                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                                        class="mr-2"
                                        shape="circle"
                                    />
                                    <span class="inline-flex flex-col items-start">
                                        <span class="font-bold text-xs">{{ user.full_name }}</span>
                                        <span class="text-xs">{{ user.role }}</span>
                                    </span>
                                </button>
                                <Menu ref="userMenu" :model="userMenuItems" popup />
                            </div>
                        </template>
                    </Menubar>
                </header>

                <div class="md:mt-16 p-4">
                    <slot></slot>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import TieredMenu from 'primevue/tieredmenu'
import Menubar from 'primevue/menubar'
import Avatar from 'primevue/avatar'
import Button from 'primevue/button'
import Menu from 'primevue/menu'

// Import the logo
import logoSrc from '@/assets/logo.png'

const page = usePage()

const user = computed(() => {
    const authUser = page.props.auth?.user
    return {
        full_name: authUser?.full_name || authUser?.name || 'Guest',
        role: authUser?.role || 'Not Logged In'
    }
})

const menuBarItems = ref([])

const isSidebarOpen = ref(true)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    console.log('Sidebar toggled:', isSidebarOpen.value)
}

const logout = () => {
    router.post('/logout', {}, {
        onSuccess: () => {
            console.log('Logged out successfully');
        },
        onError: (errors) => {
            console.error('Logout failed', errors);
        }
    });
};

const userMenuItems = ref([
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => {
            // Navigate to profile page if needed
            // router.visit('/profile');
        }
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        command: logout
    }
]);

const userMenu = ref(null);

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};

const sideBarItems = ref([
    {
        label: 'Dashboard',
        icon: 'pi pi-home',
        route: '/'
    },
    {
        label: 'Pupil Health',
        icon: 'pi pi-heart',
        route: '/pupil-health'
    },
    {
        label: 'Health Report',
        icon: 'pi pi-chart-bar',
        route: '/health-report'
    },
    {
        label: 'Oral Health Report',
        icon: 'pi pi-teeth',
        route: '/oral-health-report'
    }
])
</script>

<style scoped>
.sidebar-closed {
    width: 0;
    overflow: hidden;
}
</style>
