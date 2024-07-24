<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { FunnelIcon, PrinterIcon } from '@heroicons/vue/24/outline'
import moment from "moment";
import axios from "axios";
import { ref } from "vue";

const props = defineProps({
    histories: Object,
});

const formFilter = useForm({
    from_date: "",
    until_date: "",
});

const detail_transactions = ref([]);
const modalDetail = ref(false);
const loading = ref(false);

const printTransaction = () => {
    if(formFilter.from_date == "" || formFilter.until_date == ""){
        alert("Harap isi tanggal terlebih dahulu")
    }else{
        formFilter.get(route('transaction.print'),{
            preserveState: true, 
        })
    }
}

const calculateTotal = (transactions) => {
    return transactions.reduce((acc, item) => {
        return acc + (item.price - item.buying_price) * item.qty;
    }, 0);
};

const formatTotal = (transactions) => {
    const total = calculateTotal(transactions);
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
    }).format(total);
};

const getDetailTransaction = (id) => {
    modalDetail.value.showModal();
    loading.value = true;
    axios
        .get(route("transaction.detail", id))
        .then((response) => {
            detail_transactions.value = response.data;
        })
        .catch((response) => {
            console.log(response);
        })
        .finally(() => {
            loading.value = false;
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
                        <div class="flex justify-between">
                            <form @submit.prevent="filter">
                            <div class="join">
                                <input
                                    type="date"
                                    v-model.lazy="formFilter.from_date"
                                    class="input input-bordered join-item"
                                />

                                <input
                                    type="date"
                                    v-model.lazy="formFilter.until_date"
                                    class="input input-bordered join-item"
                                />
                                <button
                                    :disabled="formFilter.processing"
                                    type="submit"
                                    class="btn join-item rounded-r-full"
                                >
                                    Filter <FunnelIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </form>
                        <a target="_blank" :href="route('transaction.print')+`?from_date=${formFilter.from_date}&until_date=${formFilter.until_date}`"  class="btn btn-sm btn-success">Print <PrinterIcon class="w-4 h-4" /></a>
                        </div>
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
                                        <th>Laba</th>
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
                                        <td>
                                            <a
                                                href="javascript:;"
                                                class="hover:text-blue-500"
                                                @click="
                                                    getDetailTransaction(
                                                        history.id,
                                                    )
                                                "
                                                >{{ history.invoice }}</a
                                            >
                                        </td>
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
                                        <td>
                                            {{
                                                formatTotal(
                                                    history.detail_transaction,
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

        <dialog id="modalDetail" class="modal" ref="modalDetail">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Detail Transaksi</h3>
                <div class="py-4">
                    <table class="table" v-if="!loading">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>QTY</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in detail_transactions"
                                :key="item.id"
                            >
                                <td>
                                    {{ item.product.nama }}
                                </td>
                                <td>
                                    {{ item.qty }}
                                </td>
                                <td>
                                    {{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(item.buying_price)
                                    }}
                                </td>
                                <td>
                                    {{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(item.price)
                                    }}
                                </td>
                                <td>
                                    {{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(
                                            (item.price - item.buying_price) *
                                                item.qty,
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center" v-else>
                        <span class="loading loading-spinner loading-sm"></span>
                    </p>
                </div>
                <div class="modal-action">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn btn-sm">Tutup</button>
                    </form>
                </div>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
