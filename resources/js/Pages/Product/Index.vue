<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage, Link } from "@inertiajs/vue3";
import { ref, inject } from "vue";
import QrcodeVue from "qrcode.vue";

const swal = inject("$swal");

const props = defineProps({
    products: Object,
    category_products: Object,
});

const page = usePage();

const form = useForm({
    category_product_id: "",
    product_code: "",
    nama: "",
    qty: "",
    price: "",
});

const modal = ref();
const modalUbah = ref();
const product_id = ref();

const loading = ref(false);

const store = () => {
    form.post(route("product.store"), {
        preserveScroll: true,
        onFinish: () => {
            swal.fire({
                icon: page.props.flash.message[0],
                title: page.props.flash.message[1],
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                position: "top-right",
            });
            modal.value.close();
            form.reset();
        },
    });
};

const edit = async (id) => {
    await axios
        .get(route("product.edit", id))
        .then((res) => {
            form.nama = res.data.nama;
            form.category_product_id = res.data.category_product_id;
            form.qty = res.data.qty;
            form.price = res.data.price;
            product_id.value = id;
        })
        .catch((res) => {
            swal.fire({
                icon: "error",
                title: res.data,
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                position: "top-right",
            });
        });
};

const update = () => {
    form.patch(route("product.update", product_id.value), {
        preserveScroll: true,
        onFinish: () => {
            swal.fire({
                icon: page.props.flash.message[0],
                title: page.props.flash.message[1],
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                position: "top-right",
            });
            modalUbah.value.close();
            product_id.value = null;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm("Hapus data ?")) {
        form.delete(route("product.destroy", id), {
            preserveScroll: true,
            onFinish: () => {
                swal.fire({
                    icon: page.props.flash.message[0],
                    title: page.props.flash.message[1],
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: "top-right",
                });
            },
        });
    }
};
</script>

<template>
    <Head title="Produk" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Produk
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-10"
                >
                    <button
                        class="btn btn-sm"
                        onclick="modalTambah.showModal()"
                    >
                        Tambah
                    </button>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>QR</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                v-if="
                                    !(
                                        Object.keys(props.products.data)
                                            .length === 0
                                    )
                                "
                            >
                                <!-- row 1 -->
                                <tr
                                    v-for="product in props.products.data"
                                    :key="product.id"
                                >
                                    <td>{{ product.product_code }}</td>
                                    <td>{{ product.nama }}</td>
                                    <td>{{ product.category_product.nama }}</td>
                                    <td>{{ product.qty }}</td>
                                    <td>
                                        {{
                                            new Intl.NumberFormat("id-ID", {
                                                style: "currency",
                                                currency: "IDR",
                                            }).format(product.price)
                                        }}
                                    </td>
                                    <td>
                                        <QrcodeVue
                                            :value="
                                                route(
                                                    'product.edit',
                                                    product.id,
                                                )
                                            "
                                            :size="100"
                                        />
                                    </td>
                                    <td>
                                        <button
                                            @click="edit(product.id)"
                                            onclick="modalUbah.showModal()"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Ubah
                                        </button>
                                        &nbsp;
                                        <button
                                            @click="destroy(product.id)"
                                            class="btn btn-sm btn-error"
                                        >
                                            Hapus
                                        </button>
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
                            :href="props.products.prev_page_url"
                            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                            v-if="props.products.current_page - 1 > 0"
                            >«</Link
                        >
                        <button
                            type="button"
                            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                        >
                            Page {{ props.products.current_page ?? "" }}
                        </button>
                        <Link
                            :href="props.products.next_page_url"
                            class="bg-white text-black dark:text-white hover:text-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 join-item btn btn-sm"
                            v-if="props.products.next_page_url != null"
                            >»</Link
                        >
                    </div>
                </div>
            </div>
        </div>

        <dialog id="modalTambah" class="modal" ref="modal">
            <div class="modal-box">
                <form @submit.prevent="store">
                    <h3 class="font-bold text-lg">Modal Tambah Produk</h3>
                    <div class="divider"></div>
                    <div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Kode Produk</span>
                            </label>
                            <input
                                type="text"
                                required
                                v-model.lazy="form.product_code"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.product_code
                                        ? 'input-error'
                                        : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.product_code"
                                    >{{ form.errors.product_code }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Nama</span>
                            </label>
                            <input
                                type="text"
                                required
                                v-model.lazy="form.nama"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.nama ? 'input-error' : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.nama"
                                    >{{ form.errors.nama }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Kategori</span>
                            </label>
                            <select
                                required
                                :disabled="form.processing"
                                v-model.lazy="form.category_product_id"
                                :class="`select select-bordered w-full ${
                                    form.errors.categoru_id
                                        ? 'select-error'
                                        : ''
                                }`"
                            >
                                <option disabled value="" selected>
                                    -- Pilih Kategori --
                                </option>
                                <option
                                    v-for="category_product in props.category_products"
                                    :key="category_product.id"
                                    :value="category_product.id"
                                >
                                    {{ category_product.nama }}
                                </option>
                            </select>
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.nama"
                                    >{{ form.errors.nama }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Qty</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                required
                                v-model.lazy="form.qty"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.qty ? 'input-error' : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.qty"
                                    >{{ form.errors.qty }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Harga</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                required
                                v-model.lazy="form.price"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.price ? 'input-error' : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.price"
                                    >{{ form.errors.price }}</span
                                >
                            </label>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="modal-action gap-1">
                        <!-- if there is a button in form, it will close the modal -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="btn btn-sm btn-success"
                        >
                            Simpan
                            <div
                                v-if="form.processing"
                                class="spinner-border spinner-border-sm"
                                role="status"
                            >
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm"
                            @click="form.reset()"
                            onclick="modalTambah.close()"
                        >
                            Tutup
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <dialog id="modalUbah" class="modal" ref="modalUbah">
            <div class="modal-box">
                <form @submit.prevent="update">
                    <h3 class="font-bold text-lg">Modal Ubah Produk</h3>
                    <div class="divider"></div>
                    <div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Kode Produk</span>
                            </label>
                            <input
                                type="text"
                                required
                                v-model.lazy="form.product_code"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.product_code
                                        ? 'input-error'
                                        : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.product_code"
                                    >{{ form.errors.product_code }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Nama</span>
                            </label>
                            <input
                                type="text"
                                required
                                v-model.lazy="form.nama"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.nama ? 'input-error' : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.nama"
                                    >{{ form.errors.nama }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Kategori</span>
                            </label>
                            <select
                                required
                                :disabled="form.processing"
                                v-model.lazy="form.category_product_id"
                                :class="`select select-bordered w-full ${
                                    form.errors.categoru_id
                                        ? 'select-error'
                                        : ''
                                }`"
                            >
                                <option disabled value="" selected>
                                    -- Pilih Kategori --
                                </option>
                                <option
                                    v-for="category_product in props.category_products"
                                    :key="category_product.id"
                                    :value="category_product.id"
                                >
                                    {{ category_product.nama }}
                                </option>
                            </select>
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.nama"
                                    >{{ form.errors.nama }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Qty</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                required
                                v-model.lazy="form.qty"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.qty ? 'input-error' : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.qty"
                                    >{{ form.errors.qty }}</span
                                >
                            </label>
                        </div>
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Harga</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                required
                                v-model.lazy="form.price"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.price ? 'input-error' : ''
                                } `"
                                :readonly="form.processing"
                            />
                            <label class="label">
                                <span
                                    class="label-text-alt text-error"
                                    v-if="form.errors.price"
                                    >{{ form.errors.price }}</span
                                >
                            </label>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="modal-action gap-1">
                        <!-- if there is a button in form, it will close the modal -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="btn btn-sm btn-success"
                        >
                            Simpan
                            <div
                                v-if="form.processing"
                                class="spinner-border spinner-border-sm"
                                role="status"
                            >
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm"
                            onclick="modalUbah.close()"
                            @click="form.reset()"
                        >
                            Tutup
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
