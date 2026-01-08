<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    pokemons: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
});

const page = usePage();
const deleteForm = useForm({});
const syncing = ref(false);
const syncedCount = ref(0);
const localSuccessMessage = ref(null);
const localSyncInfo = ref(null);
const localErrorMessage = ref(null);

const successMessage = computed(
    () => localSuccessMessage.value ?? page.props.flash?.success ?? null,
);
const errorMessage = computed(
    () => localErrorMessage.value ?? page.props.flash?.error ?? null,
);
const syncInfo = computed(
    () => localSyncInfo.value ?? page.props.flash?.sync ?? null,
);

const syncPokemons = async () => {
    if (syncing.value) return;

    syncing.value = true;
    syncedCount.value = 0;
    localSuccessMessage.value = null;
    localErrorMessage.value = null;
    localSyncInfo.value = null;

    let nextUrl = null;

    try {
        do {
            const { data } = await window.axios.post(route('pokemons.sync'), {
                next_url: nextUrl,
            });

            syncedCount.value += data.synced ?? 0;
            localSyncInfo.value = {
                count: data.count ?? localSyncInfo.value?.count ?? 0,
                next: data.next_url ?? null,
                results: data.results ?? [],
            };

            nextUrl = data.next_url ?? null;
        } while (nextUrl);

        localSuccessMessage.value = `Sincronização concluída (${syncedCount.value} pokémons).`;

        await router.reload({
            only: ['pokemons'],
            preserveScroll: true,
            preserveState: true,
        });
    } catch (error) {
        localErrorMessage.value =
            error.response?.data?.message ??
            'Falha ao sincronizar os pokémons. Tente novamente.';
    } finally {
        syncing.value = false;
    }
};

const deactivatePokemon = (pokemon) => {
    if (
        !confirm(
            `Deseja realmente desativar ${pokemon.nome}? Você poderá reativá-lo editando o registro.`,
        )
    ) {
        return;
    }

    deleteForm.delete(route('pokemons.destroy', pokemon.id), {
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

const formatDate = (value) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('pt-BR');
};

const normalizeLabel = (label = '') =>
    label.replace('&laquo;', '«').replace('&raquo;', '»');

const formatSyncValue = (value, fallback = '-') =>
    value === null || value === undefined || value === ''
        ? fallback
        : value;
</script>

<template>
    <Head title="Pokémons" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pokémons
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                        <div>
                            <p class="text-sm text-gray-600">
                                Explore a lista de pokémons cadastrados. Use a navegação
                                abaixo para percorrer todas as páginas ou sincronize a base
                                diretamente com a PokeAPI.
                            </p>
                            <p
                                v-if="successMessage"
                                class="mt-2 text-sm font-semibold text-emerald-600"
                            >
                                {{ successMessage }}
                            </p>
                            <p
                                v-if="errorMessage"
                                class="mt-2 text-sm font-semibold text-rose-600"
                            >
                                {{ errorMessage }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="syncPokemons"
                            :disabled="syncing"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-indigo-300"
                        >
                            <svg
                                class="-ms-1 me-2 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M15.312 11.415A5.5 5.5 0 114.687 8.585 5.5 5.5 0 0010 15.5a5.5 5.5 0 005.312-4.085zm-4.353-7.447a3.5 3.5 0 00-4.46 4.46 1 1 0 01-1.897.632 5.5 5.5 0 016.998-6.998 1 1 0 01-.632 1.897z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            {{ syncing ? 'Sincronizando...' : 'Sincronizar pokémons' }}
                        </button>
                        <p
                            v-if="syncing"
                            class="text-xs font-semibold text-indigo-600"
                        >
                            Importando... {{ syncedCount }} pokémons sincronizados.
                        </p>
                    </div>

                    <div
                        v-if="syncInfo"
                        class="mx-6 mb-4 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900"
                    >
                        <p>
                            <span class="font-semibold">Total na PokeAPI:</span>
                            {{ formatSyncValue(syncInfo.count) }}
                        </p>
                        <p class="mt-1">
                            <span class="font-semibold">Próxima página:</span>
                            {{ formatSyncValue(syncInfo.next, 'início') }}
                        </p>
                        <div
                            v-if="syncInfo.results && syncInfo.results.length"
                            class="mt-2"
                        >
                            <p class="font-semibold">Últimos resultados:</p>
                            <ul class="mt-1 list-disc space-y-0.5 pl-4 text-xs text-indigo-800">
                                <li
                                    v-for="result in syncInfo.results"
                                    :key="result.name"
                                    class="flex items-center justify-between gap-2"
                                >
                                    <span class="font-medium capitalize">
                                        {{ result.name }}
                                    </span>
                                    <a
                                        v-if="result.url"
                                        :href="result.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-[11px] font-semibold uppercase tracking-wide text-indigo-600 hover:text-indigo-500"
                                    >
                                        detalhes
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="overflow-x-auto border-t border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr
                                    class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    <th scope="col" class="px-6 py-3">
                                        ID Externo
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Foto
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Nome
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Tipo
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Altura
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Peso
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
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
                                    v-for="pokemon in props.pokemons.data"
                                    :key="pokemon.id"
                                    class="text-sm text-gray-700"
                                >
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ pokemon.external_id ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <img
                                            v-if="pokemon.foto"
                                            :src="pokemon.foto"
                                            :alt="`Foto do pokémon ${pokemon.nome}`"
                                            class="h-12 w-12 rounded-full border border-gray-200 bg-white object-contain p-1"
                                        />
                                        <span
                                            v-else
                                            class="text-xs text-gray-400"
                                        >
                                            Sem imagem
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <Link
                                            :href="route('pokemons.show', pokemon.id)"
                                            class="text-indigo-600 hover:text-indigo-500"
                                        >
                                            {{ pokemon.nome }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ pokemon.tipo }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ formatHeight(pokemon.altura) }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ formatWeight(pokemon.peso) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="[
                                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                                pokemon.ativo
                                                    ? 'bg-emerald-100 text-emerald-800'
                                                    : 'bg-gray-100 text-gray-500',
                                            ]"
                                        >
                                            {{ pokemon.ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ formatDate(pokemon.created_at) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <Link
                                                :href="route('pokemons.edit', pokemon.id)"
                                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Editar
                                            </Link>
                                            <DangerButton
                                                type="button"
                                                class="!px-3 !py-1.5 text-xs uppercase tracking-widest"
                                                :disabled="deleteForm.processing"
                                                @click="deactivatePokemon(pokemon)"
                                            >
                                                Excluir
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!props.pokemons.data || !props.pokemons.data.length"
                                    class="text-center text-sm text-gray-500"
                                >
                                    <td colspan="9" class="px-6 py-8">
                                        Nenhum pokémon cadastrado no momento.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 px-6 py-4"
                    >
                        <p class="text-sm text-gray-600">
                            Mostrando
                            <span class="font-semibold text-gray-900">
                                {{ props.pokemons.from || 0 }} - {{ props.pokemons.to || 0 }}
                            </span>
                            de
                            <span class="font-semibold text-gray-900">
                                {{ props.pokemons.total || 0 }}
                            </span>
                            pokémons
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <template
                                v-for="link in props.pokemons.links"
                                :key="link.label + (link.url ?? '')"
                            >
                                <component
                                    :is="link.url ? Link : 'span'"
                                    v-bind="link.url ? { href: link.url, preserveScroll: true } : {}"
                                    class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm font-medium transition"
                                    :class="[
                                        link.active
                                            ? 'border-indigo-600 bg-indigo-600 text-white'
                                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
                                        !link.url && !link.active
                                            ? 'cursor-not-allowed text-gray-400 hover:bg-white'
                                            : '',
                                    ]"
                                    v-html="normalizeLabel(link.label)"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
