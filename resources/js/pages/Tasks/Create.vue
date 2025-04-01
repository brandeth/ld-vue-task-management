<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const breadcrumbs = [
    {
        title: 'Tasks',
        href: '/tasks',
    },
    {
        title: 'Create Task',
        href: '/tasks/create',
    },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const form = useForm({
    name: '',
    is_completed: false,
    due_date: null,
});

const createTask = () => {
    form.transform((data) => ({
        ...data,
        due_date: data.due_date ? data.due_date.toString() : null,
    })).post(route('tasks.store'), {
        onSuccess: () => {
            toast.success('Task created successfully.');
            router.visit('/tasks');
        },
        onError: (errors) => {
            toast.error('Failed to create task. Please try again.');
            console.error('Failed to create task:', errors);
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="mb-4 text-2xl font-bold">Create New Task</h1>
            <form @submit.prevent="createTask" class="space-y-4">
                <div class="space-y-2">
                    <Label for="taskName">Task Name <span class="text-red-400">*</span></Label>
                    <Input v-model="form.name" id="taskName" :errors="form.errors.name" />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label htmlFor="name">Due Date</Label>

                    <Popover>
                        <PopoverTrigger as-child>
                            <Button
                                variant="outline"
                                :class="cn('w-[280px] justify-start text-left font-normal', !form.due_date && 'text-muted-foreground')"
                            >
                                <CalendarIcon class="mr-2 h-4 w-4" />
                                {{ form.due_date ? df.format(form.due_date.toDate(getLocalTimeZone())) : 'Pick a date' }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto p-0">
                            <Calendar v-model="form.due_date" initial-focus />
                        </PopoverContent>
                    </Popover>

                    <InputError :message="form.errors.due_date" />
                </div>
                <!-- <div class="flex items-center">
          <Input v-model="form.is_completed" id="isCompleted" type="checkbox" />
          <Label for="isCompleted" class="ml-2">Mark as Completed</Label>
        </div> -->
                <div class="flex justify-end gap-2">
                    <Link href="/tasks" :class="buttonVariants({ variant: 'outline' })">Cancel</Link>
                    <Button type="submit" :disabled="form.processing">Create</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style>
/* Add any scoped styles here if needed */
</style>
