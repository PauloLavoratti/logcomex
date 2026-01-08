<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
});

const destroyForm = useForm({});

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

const formatCurrency = (value) => currencyFormatter.format(Number(value));

const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('pt-BR');
};

const confirmDeletion = (product) => {
    if (!confirm(`Deseja realmente excluir ${product.name}?`)) {
        return;
    }

    destroyForm.delete(route('products.destroy', product.id));
};
</script>

<template>
    <Head title="Produtos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Produtos
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                        <div>
                            <p class="text-sm text-gray-600">
                                Listagem simples de produtos cadastrados. Edite a
                                seed ou substitua pela sua fonte de dados real.
                            </p>
                            <p
                                v-if="$page.props.flash?.success"
                                class="mt-2 text-sm font-semibold text-emerald-600"
                            >
                                {{ $page.props.flash.success }}
                            </p>
                        </div>

                        <Link
                            :href="route('products.create')"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                        >
                            Novo produto
                        </Link>
                    </div>

                    <div class="overflow-x-auto border-t border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th scope="col" class="px-6 py-3">
                                        Produto
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Descrição
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Estoque
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Preço
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Criado em
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr
                                    v-for="product in props.products"
                                    :key="product.id"
                                    class="text-sm text-gray-700"
                                >
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ product.name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ product.description }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ product.stock }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600">
                                        {{ formatCurrency(product.price) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ formatDate(product.created_at) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <Link
                                                :href="route('products.edit', product.id)"
                                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Editar
                                            </Link>

                                            <DangerButton
                                                @click="confirmDeletion(product)"
                                                :disabled="destroyForm.processing"
                                                class="!px-3 !py-1.5"
                                            >
                                                Excluir
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!props.products.length"
                                    class="text-center text-sm text-gray-500"
                                >
                                    <td colspan="6" class="px-6 py-8">
                                        Nenhum produto cadastrado no momento.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
