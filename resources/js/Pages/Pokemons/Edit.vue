<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pokemon: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    nome: props.pokemon.nome ?? '',
    tipo: props.pokemon.tipo ?? '',
    altura: props.pokemon.altura ?? '',
    peso: props.pokemon.peso ?? '',
    foto: props.pokemon.foto ?? '',
    ativo: props.pokemon.ativo ?? true,
});

const deleteForm = useForm({});

const submit = () => {
    form.put(route('pokemons.update', props.pokemon.id));
};

const destroy = () => {
    if (
        !confirm(
            `Deseja realmente excluir ${props.pokemon.nome}? Essa ação não pode ser desfeita.`,
        )
    ) {
        return;
    }

    deleteForm.delete(route('pokemons.destroy', props.pokemon.id));
};
</script>

<template>
    <Head :title="`Editar Pokémon: ${props.pokemon.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Editar {{ props.pokemon.nome }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        Atualize os dados sincronizados ou personalize manualmente.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('pokemons.show', props.pokemon.id)"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                    >
                        Cancelar
                    </Link>
                    <DangerButton
                        type="button"
                        :disabled="deleteForm.processing"
                        @click="destroy"
                    >
                        Excluir Pokémon
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <form class="space-y-6 px-8 py-6" @submit.prevent="submit">
                        <div>
                            <InputLabel for="nome" value="Nome" />
                            <TextInput
                                id="nome"
                                v-model="form.nome"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.nome" />
                        </div>

                        <div>
                            <InputLabel for="tipo" value="Tipos" />
                            <TextInput
                                id="tipo"
                                v-model="form.tipo"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ex.: Fogo, Voador"
                            />
                            <InputError class="mt-2" :message="form.errors.tipo" />
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <InputLabel for="altura" value="Altura (cm)" />
                                <TextInput
                                    id="altura"
                                    v-model="form.altura"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.altura" />
                            </div>

                            <div>
                            <InputLabel for="peso" value="Peso (kg)" />
                            <TextInput
                                id="peso"
                                v-model="form.peso"
                                type="number"
                                min="0"
                                step="0.1"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.peso" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="foto" value="URL da foto (front_default)" />
                        <TextInput
                            id="foto"
                            v-model="form.foto"
                            type="url"
                            class="mt-1 block w-full"
                            placeholder="https://..."
                        />
                        <InputError class="mt-2" :message="form.errors.foto" />
                    </div>

                        <div>
                            <InputLabel value="Status" class="mb-1" />
                            <label class="inline-flex items-center space-x-2 text-sm text-gray-700">
                                <Checkbox v-model:checked="form.ativo" />
                                <span>Ativo</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.ativo" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <PrimaryButton :disabled="form.processing">
                                Salvar alterações
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
