<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    ArchiveBoxIcon,
    ArchiveBoxXMarkIcon,
    CurrencyDollarIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    product: Number,
    product_empty: Number,
    transaction: Number,
    monthly_transaction: Object,
});

const options = {
    chart: {
        id: "vuechart-example",
    },
    xaxis: {
        categories: props.monthly_transaction.map((item) => item.month),
    },
};
const series = [
    {
        name: "series-1",
        data: props.monthly_transaction.map((item) => item.count),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div
                        class="p-6 text-gray-900 dark:text-gray-100 text-center"
                    >
                        <div class="stats shadow">
                            <div class="stat">
                                <div class="stat-figure text-primary">
                                    <ArchiveBoxIcon class="h-8 w-8" />
                                </div>
                                <div class="stat-title">Jumlah Produk</div>
                                <div class="stat-value">
                                    {{ props.product }}
                                </div>
                            </div>

                            <div class="stat">
                                <div class="stat-figure text-error">
                                    <ArchiveBoxXMarkIcon class="h-8 w-8" />
                                </div>
                                <div class="stat-title">
                                    Jumlah Produk Kosong
                                </div>
                                <div class="stat-value">
                                    {{ props.product_empty }}
                                </div>
                            </div>

                            <div class="stat">
                                <div class="stat-figure text-success">
                                    <CurrencyDollarIcon class="w-8 h-8" />
                                </div>
                                <div class="stat-title">
                                    Total Transaksi Bulan Ini
                                </div>
                                <div class="stat-value">
                                    {{ props.transaction }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="card bg-base-100 shadow-xl mx-auto mt-5"
                            style="width: 600px"
                        >
                            <div class="card-body">
                                <apexchart
                                    width="500"
                                    class="mx-auto"
                                    type="bar"
                                    :options="options"
                                    :series="series"
                                ></apexchart>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
