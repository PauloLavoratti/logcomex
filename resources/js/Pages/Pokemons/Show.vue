<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pokemon: {
        type: Object,
        required: true,
    },
});

const deleteForm = useForm({});

const destroyPokemon = () => {
    if (
        !confirm(
            `Tem certeza que deseja excluir ${props.pokemon.nome}? Essa ação não poderá ser desfeita.`,
        )
    ) {
        return;
    }

    deleteForm.delete(route('pokemons.destroy', props.pokemon.id), {
        preserveScroll: true,
    });
};

const formatHeight = (value) => {
    if (value === null || value === undefined) return '-';
    return `${Number(value).toLocaleString('pt-BR')} cm`;
};

const formatWeight = (value) => {
    if (value === null || value === undefined) return '-';

    return `${Number(value).toLocaleString('pt-BR', {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    })} kg`;
};

const formatDateTime = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleString('pt-BR');
};
</script>

<template>
    <Head :title="`Pokémon: ${props.pokemon.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ props.pokemon.nome }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Detalhes do pokémon sincronizado pela PokeAPI.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('pokemons.index')"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                    >
                        &larr; Voltar para a lista
                    </Link>
                    <Link
                        :href="route('pokemons.edit', props.pokemon.id)"
                        class="inline-flex items-center rounded-md border border-indigo-600 bg-white px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                    >
                        Editar
                    </Link>
                    <DangerButton
                        type="button"
                        :disabled="deleteForm.processing"
                        @click="destroyPokemon"
                    >
                        Excluir
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <dl class="divide-y divide-gray-100">
                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Foto
                            </dt>
                            <dd class="sm:col-span-2">
                                <div
                                    v-if="props.pokemon.foto"
                                    class="flex items-center gap-4"
                                >
                                    <img
                                        :src="props.pokemon.foto"
                                        :alt="`Foto do pokémon ${props.pokemon.nome}`"
                                        class="h-24 w-24 rounded-full border border-gray-200 bg-white object-contain p-2"
                                    />
                                    <Link
                                        :href="props.pokemon.foto"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                                    >
                                        Abrir em nova guia
                                    </Link>
                                </div>
                                <span v-else class="text-sm text-gray-400">
                                    Nenhuma imagem disponível.
                                </span>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                ID interno
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ props.pokemon.id }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                ID externo
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ props.pokemon.external_id ?? '-' }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Tipos
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ props.pokemon.tipo || 'Não informado' }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Altura
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ formatHeight(props.pokemon.altura) }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Peso
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ formatWeight(props.pokemon.peso) }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Status
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                                        props.pokemon.ativo
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : 'bg-gray-100 text-gray-500',
                                    ]"
                                >
                                    {{ props.pokemon.ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Criado em
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ formatDateTime(props.pokemon.created_at) }}
                            </dd>
                        </div>

                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 px-6 py-5 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">
                                Atualizado em
                            </dt>
                            <dd class="text-sm text-gray-900 sm:col-span-2">
                                {{ formatDateTime(props.pokemon.updated_at) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
