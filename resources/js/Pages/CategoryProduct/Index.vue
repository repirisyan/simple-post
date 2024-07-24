<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { ref, inject } from "vue";

const swal = inject("$swal");

const props = defineProps({
    data: Object,
});

const page = usePage();

const form = useForm({
    nama: "",
});

const modal = ref();
const modalUbah = ref();
const category_id = ref();

const loading = ref(false);

const store = () => {
    form.post(route("category-product.store"), {
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
        .get(route("category-product.edit", id))
        .then((res) => {
            form.nama = res.data.nama;
            category_id.value = id;
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
    form.patch(route("category-product.update", category_id.value), {
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
            category_id.value = null;
            form.reset();
        },
    });
};

const destroy = (id) => {
    if (confirm("Hapus data ?")) {
        form.delete(route("category-product.destroy", id), {
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
    <Head title="Kategori Produk" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Kategori Produk
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
                        Tambah <PlusIcon class="h-4 w-4" />
                    </button>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody
                                v-if="!(Object.keys(props.data).length === 0)"
                            >
                                <!-- row 1 -->
                                <tr v-for="item in props.data" :key="item.id">
                                    <td>{{ item.nama }}</td>
                                    <td class="flex gap-2">
                                        <button
                                            @click="edit(item.id)"
                                            onclick="modalUbah.showModal()"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Ubah <PencilSquareIcon class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="destroy(item.id)"
                                            class="btn btn-sm btn-error"
                                        >
                                            Hapus <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="text-center">
                                    <td colspan="2">Tidak ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <dialog id="modalTambah" class="modal" ref="modal">
            <div class="modal-box">
                <form @submit.prevent="store">
                    <h3 class="font-bold text-lg">Modal Tambah Kategori</h3>
                    <div class="divider"></div>
                    <div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Nama Kategori</span>
                            </label>
                            <input
                                type="text"
                                required
                                v-model.lazy="form.nama"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.nama ? 'input-error' : ''
                                } max-w-xs`"
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
                    </div>
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
                    <h3 class="font-bold text-lg">Modal Tambah Kategori</h3>
                    <div class="divider"></div>
                    <div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                                <span class="label-text">Nama Kategori</span>
                            </label>
                            <input
                                type="text"
                                required
                                v-model.lazy="form.nama"
                                placeholder="Type here"
                                :class="`input input-bordered w-full ${
                                    form.errors.nama ? 'input-error' : ''
                                } max-w-xs`"
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
                    </div>
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
                            onclick="modalUbah.close()"
                        >
                            Tutup
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
    </AuthenticatedLayout>
</template>
