<script setup lang="ts">
import { Button, buttonVariants } from '@/components/ui/button';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, PaginatedResponse, TaskCategory } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Tasks',
        href: '/tasks',
    },
    {
        title: 'Task Categories',
        href: '/task-categories',
    },
];

interface Props {
    taskCategories: PaginatedResponse<TaskCategory>;
}

defineProps<Props>();

const deleteCategory = (taskCategory: TaskCategory) => {
    if (taskCategory.tasks_count === 0) {
        if (confirm('Are you sure you want to delete this task category?')) {
            router.delete(route('task-categories.destroy', taskCategory.id));
            toast.success('Task Category deleted successfully');
        }
    } else {
        if (
            confirm(
                'This category has tasks assigned to it. Are you sure you want to delete it? This will also delete all the tasks assigned to this category.',
            )
        ) {
            router.delete(route('task-categories.destroy', taskCategory.id));
            toast.success('Task Category deleted successfully');
        }
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Task Categories List" />

        <div class="px-4 pt-4">
            <div class="mb-4 flex justify-between">
                <h1 class="text-2xl font-bold">Task Categories</h1>
                <div class="flex items-center gap-x-2">
                    <Link :class="buttonVariants({ variant: 'outline' })" href="/task-categories/create"> Create Task Category </Link>
                </div>
            </div>

            <Table class="mt-4">
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead class="w-[200px] text-right">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="taskCategories.data.length > 0">
                        <TableRow v-for="category in taskCategories.data" :key="category.id">
                            <TableCell>{{ category.name }}</TableCell>
                            <TableCell class="flex gap-x-2 text-right">
                                <Link :class="buttonVariants({ variant: 'default' })" :href="`/task-categories/${category.id}/edit`">Edit</Link>
                                <Button variant="destructive" @click="deleteCategory(category)" class="mr-2">Delete</Button>
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell colspan="2" class="py-6 text-center text-muted-foreground"> No results found. </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>

            <div class="flex items-center justify-center">
                <Pagination
                    v-slot="{ page }"
                    :items-per-page="taskCategories.per_page"
                    :total="taskCategories.total"
                    :sibling-count="1"
                    show-edges
                    :default-page="taskCategories.current_page"
                    @update:page="(newPage) => router.get(`/task-categories?page=${newPage}`)"
                >
                    <PaginationList v-slot="{ items }" class="mt-3 flex items-center gap-1">
                        <PaginationFirst />
                        <PaginationPrev />

                        <template v-for="(item, index) in items" :key="index">
                            <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child>
                                <Button class="h-10 w-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :index="index" />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationList>
                </Pagination>
            </div>
        </div>
    </AppLayout>
</template>
