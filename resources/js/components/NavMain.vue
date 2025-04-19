<script setup lang="ts">
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

// const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <Collapsible v-if="item.items && item.items.length > 0" as-child :default-open="item.isActive" class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title" class="flex items-center gap-2" :class="{ 'bg-gray-200': item.isActive }">
                                <Link :href="item.url" class="flex items-center gap-2">
                                    <component :is="item.icon" v-if="item.icon" class="h-5 w-5" />
                                    <span>{{ item.title }}</span>
                                </Link>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubButton as-child>
                                        <Link :href="subItem.url" class="flex items-center gap-2" :class="{ 'bg-gray-200': subItem.isActive }">
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>
                <SidebarMenuItem v-else>
                    <SidebarMenuButton :tooltip="item.title" class="flex items-center gap-2" :class="{ 'bg-gray-200': item.isActive }">
                        <Link :href="item.url" class="flex items-center gap-2">
                            <component :is="item.icon" v-if="item.icon" class="h-5 w-5" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
