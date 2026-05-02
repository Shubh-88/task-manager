<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Overview
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

              
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                    <div class="card">
                        <h3>Total Files</h3>
                        <p>{{ stats.files }}</p>
                    </div>

                    <div class="card">
                        <h3>Total Tasks</h3>
                        <p>{{ stats.tasks }}</p>
                    </div>

                    <div class="card warning">
                        <h3>Pending Tasks</h3>
                        <p>{{ stats.pending_tasks }}</p>
                    </div>

                    <div class="card success">
                        <h3>Completed Tasks</h3>
                        <p>{{ stats.completed_tasks }}</p>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    text-align: center;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    font-size: 14px;
    color: gray;
}

.card p {
    font-size: 28px;
    font-weight: bold;
    margin-top: 10px;
}

.warning {
    border-left: 5px solid #f59e0b;
}

.success {
    border-left: 5px solid #10b981;
}
</style>