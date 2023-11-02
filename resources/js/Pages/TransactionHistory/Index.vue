<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import moment from "moment";

const props = defineProps({
    histories: Object,
});

const formFilter = useForm({
    date: ""
})

const filter = () => {
    formFilter.get(route("transaction.history"), {
        replace: true,
        preserveState: true,
    });
};

</script>

<template>
    <Head title="Riwayat Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Riwayat Transaksi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="filter">
                            <div class="join">
                                <input
                                    type="date"
                                    v-model.lazy="formFilter.date"
                                    class="input input-bordered join-item"
                                />
                                <button type="submit" class="btn join-item rounded-r-full">
                                    Filter
                                </button>
                            </div>
                        </form>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <!-- head -->
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Tanggal</th>
                                        <th>Total Pembelian</th>
                                        <th>Bayar</th>
                                        <th>Kembalian</th>
                                    </tr>
                                </thead>
                                <tbody
                                    v-if="
                                        !(
                                            Object.keys(props.histories.data)
                                                .length === 0
                                        )
                                    "
                                >
                                    <!-- row 1 -->
                                    <tr
                                        v-for="history in props.histories.data"
                                        :key="history.id"
                                    >
                                        <td>{{ history.invoice }}</td>
                                        <td>
                                            {{
                                                moment(history.created_at)
                                                    .locale("id")
                                                    .format("LLLL")
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                new Intl.NumberFormat("id-ID", {
                                                    style: "currency",
                                                    currency: "IDR",
                                                }).format(history.grand_total)
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                new Intl.NumberFormat("id-ID", {
                                                    style: "currency",
                                                    currency: "IDR",
                                                }).format(history.grand_pay)
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                new Intl.NumberFormat("id-ID", {
                                                    style: "currency",
                                                    currency: "IDR",
                                                }).format(
                                                    history.grand_pay -
                                                        history.grand_total,
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr class="text-center">
                                        <td colspan="5">Tidak ada data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="join mt-2 flex justify-center">
                            <Link
                                :href="props.histories.prev_page_url"
                                class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                v-if="props.histories.current_page - 1 > 0"
                                >«</Link
                            >
                            <button
                                type="button"
                                class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                            >
                                Page {{ props.histories.current_page ?? "" }}
                            </button>
                            <Link
                                :href="props.histories.next_page_url"
                                class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                                v-if="props.histories.next_page_url != null"
                                >»</Link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
