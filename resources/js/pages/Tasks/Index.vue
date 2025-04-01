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
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, PaginatedTaskResponse } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { DateFormatter } from '@internationalized/date';
import { onMounted } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tasks',
        href: '/tasks',
    },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

interface Props {
    tasks: PaginatedTaskResponse;
}

const props = defineProps<Props>();

onMounted(() => {
    console.log('props:', props.tasks);
});

const deleteTask = (id: number, name: string) => {
    if (confirm(`Are you sure you want to delete the task: ${name}?`)) {
        router.delete(`/tasks/${id}`, {
            onSuccess: () => {
                toast.success(`Task "${name}" deleted successfully.`);
            },
            onError: (errors) => {
                toast.error(`Failed to delete task "${name}". Please try again.`);
                console.error(`Failed to delete task "${name}":`, errors);
            },
        });
    }
};
</script>

<template>
    <Head title="Task List" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 pt-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Task List</h1>
                <Link href="/tasks/create" :class="buttonVariants({ variant: 'outline' })"> Create New Task </Link>
            </div>

            <Table class="min-w-full border-collapse">
                <TableHeader class="sticky top-0 z-10 bg-white shadow-md">
                    <TableRow class="bg-gray-100">
                        <TableHead class="w-[100px] font-bold">ID</TableHead>
                        <TableHead class="font-bold">Name</TableHead>
                        <TableHead class="w-[100px] font-bold">Completed</TableHead>
                        <TableHead class="w-[200px]">Due Date</TableHead>
                        <TableHead class="w-[100px] font-bold">Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="task in props.tasks.data" :key="task.id">
                        <TableCell>{{ task.id }}</TableCell>
                        <TableCell>{{ task.name }}</TableCell>
                        <TableCell>{{ task.is_completed ? 'Yes' : 'No' }}</TableCell>
                        <TableCell>{{ task.due_date ? df.format(new Date(task.due_date)) : '' }}</TableCell>
                        <TableCell>
                            <div class="flex items-center gap-2">
                                <Link :href="`/tasks/${task.id}/edit`" :class="buttonVariants()">Edit</Link>
                                <Button @click="deleteTask(task.id, task.name)" variant="destructive">Delete</Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <div class="flex items-center justify-center">
                <Pagination
                    v-slot="{ page }"
                    :items-per-page="props.tasks.per_page"
                    :total="props.tasks.total"
                    :sibling-count="1"
                    show-edges
                    :default-page="props.tasks.current_page"
                    @update:page="(newPage) => router.get(`/tasks?page=${newPage}`)"
                >
                    <PaginationList v-slot="{ items }" class="mt-3 flex items-center gap-1">
                        <PaginationFirst />
                        <PaginationPrev />

                        <template v-for="(item, index) in items">
                            <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                <Button class="h-10 w-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationList>
                </Pagination>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* removed scoped style */
</style>
