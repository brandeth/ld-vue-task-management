<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button, buttonVariants } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { type Task, type TaskCategory } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { DateFormatter, fromDate, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    task: Task;
    categories: TaskCategory[];
}

const breadcrumbs = [
    { title: 'Tasks', href: '/tasks' },
    { title: 'Edit Task', href: '' },
];

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});

const props = defineProps<Props>();

const fileInputRef = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const form = useForm({
    name: props.task.name ?? '', // ensure string
    is_completed: props.task.is_completed,
    due_date: props.task.due_date ? fromDate(new Date(props.task.due_date)) : null,
    media: '',
    categories: props.task.task_categories?.map((category) => category.id),
});

const clearSelectedFile = () => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
        form.media = '';
    }
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const fileSelected = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) {
        previewUrl.value = null;
        form.media = '';
        return;
    }

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }

    previewUrl.value = URL.createObjectURL(file);
    form.media = file;
};

const updateTask = () => {
    form.transform((data) => {
        const dueDate = data.due_date ? new Date(Date.UTC(data.due_date.year, data.due_date.month - 1, data.due_date.day)) : null;

        return {
            ...data,
            _method: 'PUT',
            name: data.name?.toString().trim(),
            due_date: dueDate ? dueDate.toISOString() : null,
        };
    }).post(`/tasks/${props.task.id}`, {
        forceFormData: true,
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

watch(
    () => previewUrl.value,
    (url, oldUrl) => {
        if (oldUrl && url !== oldUrl) {
            URL.revokeObjectURL(oldUrl);
        }
    },
);
</script>

<template>
    <Head title="Edit Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="mb-4 text-2xl font-bold">Edit Task</h1>
            <form @submit.prevent="updateTask" class="space-y-6">
                <!-- Task Name -->
                <div>
                    <Label for="taskName">Task Name</Label>
                    <Input v-model="form.name" id="taskName" type="text" class="max-w-sm" required />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Due Date -->
                <div class="grid gap-2">
                    <Label htmlFor="name">Due Date</Label>
                    <Popover>
                        <PopoverTrigger as-child>
                            <Button
                                variant="outline"
                                :class="cn('max-w-sm justify-start text-left font-normal', !form.due_date && 'text-muted-foreground')"
                            >
                                <CalendarIcon class="mr-2 h-4 w-4" />
                                {{ form.due_date ? df.format(new Date(form.due_date.toDate(getLocalTimeZone()))) : 'Pick a date' }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="max-w-sm p-0">
                            <Calendar v-model="form.due_date" initial-focus />
                        </PopoverContent>
                    </Popover>
                    <InputError :message="form.errors.due_date" />
                </div>

                <!-- Completion Checkbox -->
                <div class="flex items-center">
                    <Checkbox v-model:checked="form.is_completed" id="isCompleted" />
                    <Label for="isCompleted" class="ml-2">Mark as Completed</Label>
                    <InputError :message="form.errors.is_completed" />
                </div>

                <!-- File Upload -->
                <div class="space-y-2">
                    <Label for="taskMedia" class="text-sm font-medium text-gray-700 dark:text-gray-300">Upload New Media</Label>
                    <div class="flex items-center gap-3">
                        <Input type="file" id="taskMedia" @change="fileSelected($event)" class="w-full max-w-sm" ref="fileInputRef" />
                        <Button
                            v-if="previewUrl"
                            type="button"
                            variant="ghost"
                            class="px-2 text-red-600 hover:text-red-800"
                            @click="clearSelectedFile"
                        >
                            Clear
                        </Button>
                    </div>
                    <InputError :message="form.errors.media" class="mt-1" />
                </div>

                <!-- Unified Preview -->
                <div
                    class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <Label class="mb-2 text-sm font-semibold text-gray-600 dark:text-gray-200">
                        {{ previewUrl ? 'Selected Preview' : 'Current Media' }}
                    </Label>
                    <img :src="previewUrl || props.task.mediaFile?.original_url" alt="Task Media" class="h-40 w-40 rounded-md object-cover shadow" />
                </div>

                <div class="grid gap-2">
                    <Label htmlFor="categories">Categories</Label>

                    <ToggleGroup type="multiple" variant="outline" size="lg" v-model="form.categories">
                        <ToggleGroupItem v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </ToggleGroupItem>
                    </ToggleGroup>

                    <InputError :message="form.errors.categories" />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-2">
                    <Link href="/tasks" :class="buttonVariants({ variant: 'outline' })">Cancel</Link>
                    <Button type="submit" :disabled="form.processing">Update</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
img {
    transition: transform 0.2s ease-in-out;
}
img:hover {
    transform: scale(1.05);
}
</style>
