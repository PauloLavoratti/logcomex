<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    price: '',
    stock: '',
});

const submit = () => {
    form.post(route('products.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Novo produto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Novo produto
                </h2>
                <Link
                    :href="route('products.index')"
                    class="text-sm font-semibold text-indigo-600 underline-offset-2 hover:underline"
                >
                    Voltar para listagem
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <form class="space-y-6 px-8 py-6" @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Nome" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Descrição" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="3"
                                placeholder="Detalhes curtos sobre o produto"
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <InputLabel for="price" value="Preço (R$)" />
                                <TextInput
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.price" />
                            </div>

                            <div>
                                <InputLabel for="stock" value="Estoque" />
                                <TextInput
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.stock" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <Link
                                :href="route('products.index')"
                                class="text-sm font-semibold text-gray-500 underline-offset-2 hover:text-gray-700 hover:underline"
                            >
                                Cancelar
                            </Link>

                            <PrimaryButton :disabled="form.processing">
                                Salvar produto
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
