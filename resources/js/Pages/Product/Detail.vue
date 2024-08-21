<script setup>
import { Head } from "@inertiajs/vue3";
const props = defineProps({
    product: Object,
});
import QrcodeVue from "qrcode.vue";
import QRCode from "qrcode";
</script>
<template>
    <Head :title="props.product.nama" />
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center sm:pt-0 bg-gray-100 dark:bg-gray-900"
    >
        <div
            class="w-full sm:max-w-md mt-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <figure class="px-5 pt-10">
                <QrcodeVue
                    :value="route('product.link', props.product.id)"
                    :size="100"
                    class="mx-auto"
                />
            </figure>
            <div class="card-body items-center text-center">
                <h2 class="card-title">{{ props.product.nama }}</h2>
                <p>
                    {{ props.product.product_code }} |
                    {{ props.product.category_product.nama }}
                </p>
                <p>
                    {{
                        new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            currency: "IDR",
                        }).format(props.product.selling_price)
                    }}
                </p>
            </div>
        </div>
    </div>
</template>
