<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, ClipboardList, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
        isActive: page.url === '/dashboard',
    },
    {
        title: 'Tasks',
        url: '/tasks',
        icon: ClipboardList,
        isActive: page.url.startsWith('/tasks'),
        items: [
            {
                title: 'Create Task',
                url: '/tasks/create',
                isActive: page.url === '/tasks/create',
            },
            {
                title: 'Manage Task Categories',
                url: '/task-categories',
                isActive: page.url.startsWith('/task-categories'),
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        url: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        url: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
