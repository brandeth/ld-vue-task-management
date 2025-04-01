<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { DateFormatter, fromDate, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const breadcrumbs = [
    {
        title: 'Tasks',
        href: '/tasks',
    },
    {
        title: 'Edit Task',
        href: '',
    },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.task.name,
    is_completed: props.task.is_completed,
    due_date: props.task.due_date ? fromDate(new Date(props.task.due_date)) : null,
});

const updateTask = () => {
    form.transform((data) => ({
        ...data,
        due_date: data.due_date ? data.due_date.toDate(getLocalTimeZone()) : null,
    })).put(`/tasks/${props.task.id}`, {
        onSuccess: () => {
            toast.success('Task updated successfully.');
            router.visit('/tasks');
        },
        onError: (errors) => {
            toast.error('Failed to update task. Please try again.');
            console.error('Failed to update task:', errors);
        },
    });
};
</script>

<template>
    <Head title="Edit Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="mb-4 text-2xl font-bold">Edit Task</h1>
            <form @submit.prevent="updateTask" class="space-y-4">
                <div>
                    <Label for="taskName">Task Name</Label>
                    <Input v-model="form.name" id="taskName" type="text" required />
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
                                {{ form.due_date ? df.format(new Date(form.due_date.toDate(getLocalTimeZone()))) : 'Pick a date' }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto p-0">
                            <Calendar v-model="form.due_date" initial-focus />
                        </PopoverContent>
                    </Popover>

                    <InputError :message="form.errors.due_date" />
                </div>
                <div class="flex items-center">
                    <Checkbox v-model:checked="form.is_completed" id="isCompleted" />
                    <Label for="isCompleted" class="ml-2">Mark as Completed</Label>
                    <InputError :message="form.errors.is_completed" />
                </div>
                <div class="flex justify-end gap-2">
                    <Link href="/tasks" :class="buttonVariants({ variant: 'outline' })">Cancel</Link>
                    <Button type="submit" :disabled="form.processing">Update</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style>
/* Add any scoped styles here if needed */
</style>
