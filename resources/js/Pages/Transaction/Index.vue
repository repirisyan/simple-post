<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, inject } from "vue";
import { QrcodeStream } from "vue-qrcode-reader";
import { PlusIcon, CreditCardIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    products: Object,
});

const form_search_product = useForm({
    product_code: "",
});

const swal = inject("$swal");

const form = useForm({});

const bayar = ref(0);
const grandtotal = ref(0);
const detail_products = ref([]);
const error = ref("");

const loading = ref(false);

let index = null;

function paintBoundingBox(detectedCodes, ctx) {
    for (const detectedCode of detectedCodes) {
        const {
            boundingBox: { x, y, width, height },
        } = detectedCode;

        ctx.lineWidth = 2;
        ctx.strokeStyle = "#007bff";
        ctx.strokeRect(x, y, width, height);
    }
}

function onError(err) {
    error.value = `[${err.name}]: `;

    if (err.name === "NotAllowedError") {
        error.value += "you need to grant camera access permission";
    } else if (err.name === "NotFoundError") {
        error.value += "no camera on this device";
    } else if (err.name === "NotSupportedError") {
        error.value += "secure context required (HTTPS, localhost)";
    } else if (err.name === "NotReadableError") {
        error.value += "is the camera already in use?";
    } else if (err.name === "OverconstrainedError") {
        error.value += "installed cameras are not suitable";
    } else if (err.name === "StreamApiNotSupportedError") {
        error.value += "Stream API is not supported in this browser";
    } else if (err.name === "InsecureContextError") {
        error.value +=
            "Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.";
    } else {
        error.value += err.message;
    }
}

function onDetect(detectedCodes) {
    axios.get(detectedCodes[0].rawValue).then((response) => {
        index = detail_products.value.findIndex(
            (obj) => obj["id"] === response.data.id,
        );
        if (index !== -1) {
            detail_products.value[index].qty += 1;
        } else {
            detail_products.value.push({
                id: response.data.id,
                nama: response.data.nama,
                price: response.data.selling_price,
                buying_price: response.data.buying_price,
                qty: 1,
            });
        }
    });
}

const searchProduct = async () => {
    loading.value = true;
    await axios
        .post(route("transaction.search_product"), form_search_product)
        .then((response) => {
            index = detail_products.value.findIndex(
                (obj) => obj["id"] === response.data.id,
            );
            if (index !== -1) {
                detail_products.value[index].qty += 1;
            } else {
                detail_products.value.push({
                    id: response.data.id,
                    nama: response.data.nama,
                    price: response.data.selling_price,
                    buying_price: response.data.buying_price,
                    qty: 1,
                });
            }
            form_search_product.errors.product_code = null;
            loading.value = false;
            index = null;
        })
        .catch((response) => {
            form_search_product.errors.product_code = "Kode salah";
            loading.value = false;
        });
};

const pay = async () => {
    if (confirm("Selesaikan Pembayaran ?")) {
        loading.value = true;
        grandtotal.value = detail_products.value.reduce(
            (accumulator, currentValue) => {
                return (
                    accumulator + currentValue["qty"] * currentValue["price"]
                );
            },
            0,
        );
        await axios
            .post(route("transaction.pay"), [
                detail_products.value,
                bayar.value,
                grandtotal.value,
            ])
            .then((response) => {
                swal.fire({
                    icon: response.data[0],
                    title: response.data[1],
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: "top-right",
                });
                router.get(route("transaction.history"));
            })
            .catch((response) => {
                swal.fire({
                    icon: response.data[0],
                    title: response.data[1],
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: "top-right",
                });
            });
    }
};
</script>

<template>
    <Head title="Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Transaksi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10"
                >
                    <div class="flex flex-col w-full border-opacity-50">
                        <div
                            class="grid grid-cols-2 card rounded-box place-items-center"
                        >
                            <form @submit.prevent="searchProduct">
                                <div class="join">
                                    <input
                                        required
                                        type="text"
                                        placeholder="Kode Produk"
                                        :readonly="loading"
                                        v-model.lazy="
                                            form_search_product.product_code
                                        "
                                        class="input input-bordered w-full max-w-xs"
                                    />
                                    <button
                                        :disabled="loading"
                                        type="submit"
                                        class="btn join-item"
                                    >
                                        Tambah <PlusIcon class="w-4 h-4" />
                                    </button>
                                </div>
                                <label class="label">
                                    <span
                                        class="label-text-alt text-error"
                                        v-if="
                                            form_search_product.errors
                                                .product_code
                                        "
                                        >{{
                                            form_search_product.errors
                                                .product_code
                                        }}</span
                                    >
                                </label>
                            </form>
                            <div class="w-96">
                                <qrcode-stream
                                    :track="paintBoundingBox"
                                    @detect="onDetect"
                                    @error="onError"
                                ></qrcode-stream>
                            </div>
                        </div>
                        <div class="divider">Detail Transaksi</div>
                        <div
                            class="grid grid-cols-2 card rounded-box place-items-center"
                        >
                            <div class="grid gap-1">
                                <div
                                    v-for="(
                                        detail_product, index
                                    ) in detail_products"
                                    :key="detail_product.id"
                                    class="flex items-center gap-1"
                                >
                                    <p>
                                        {{ detail_product.nama }}&nbsp;{{
                                            new Intl.NumberFormat("id-ID", {
                                                style: "currency",
                                                currency: "IDR",
                                            }).format(detail_product.price)
                                        }}
                                    </p>
                                    <input
                                        required
                                        type="number"
                                        placeholder="Qty"
                                        :readonly="loading"
                                        v-model="detail_products[index].qty"
                                        class="input input-bordered w-full max-w-xs"
                                    />
                                    <button
                                        class="btn btn-sm btn-error"
                                        @click="
                                            detail_products.splice(index, 1)
                                        "
                                    >
                                        X
                                    </button>
                                    {{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(
                                            detail_product.price *
                                                detail_products[index].qty,
                                        )
                                    }}
                                </div>
                            </div>
                            <div class="grid gap-3">
                                <div>
                                    <div class="form-control w-full max-w-xs">
                                        <label class="label">
                                            <span class="label-text"
                                                >Bayar</span
                                            >
                                        </label>
                                        <input
                                            type="number"
                                            v-model="bayar"
                                            placeholder="Bayar"
                                            min="0"
                                            class="input input-bordered w-full max-w-xs"
                                        />
                                    </div>
                                </div>
                                <div class="text-3xl">
                                    Total :
                                    <b class="font-extrabold">{{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(
                                            detail_products.reduce(
                                                (accumulator, currentValue) => {
                                                    return (
                                                        accumulator +
                                                        currentValue["qty"] *
                                                            currentValue[
                                                                "price"
                                                            ]
                                                    );
                                                },
                                                0,
                                            ),
                                        )
                                    }}</b>
                                </div>
                                <div class="text-2xl">
                                    Kembalian :
                                    <b class="font-extrabold">{{
                                        new Intl.NumberFormat("id-ID", {
                                            style: "currency",
                                            currency: "IDR",
                                        }).format(
                                            bayar -
                                                detail_products.reduce(
                                                    (
                                                        accumulator,
                                                        currentValue,
                                                    ) => {
                                                        return (
                                                            accumulator +
                                                            currentValue[
                                                                "qty"
                                                            ] *
                                                                currentValue[
                                                                    "price"
                                                                ]
                                                        );
                                                    },
                                                    0,
                                                ),
                                        )
                                    }}</b>
                                </div>
                                <button
                                    :disabled="
                                        bayar -
                                            detail_products.reduce(
                                                (accumulator, currentValue) => {
                                                    return (
                                                        accumulator +
                                                        currentValue['qty'] *
                                                            currentValue[
                                                                'price'
                                                            ]
                                                    );
                                                },
                                                0,
                                            ) <
                                            0 || loading
                                    "
                                    class="btn btn-success max-w-xs mt-5"
                                    @click="pay"
                                >
                                    Bayar <CreditCardIcon class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
