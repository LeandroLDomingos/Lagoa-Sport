<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    cpf: '',
    rg: '',
    birthdate: '',
    zip_code: '',
    address: '',
    neighborhood: '',
    number: '',
    complement: '',
    city: '',
    state: '',
    country: '',
    status: 'pending',
    terms: false, // campo de aceitação dos termos
});

const isLoading = ref(false);

// CEP
const getAddressFromZipCode = async () => {
    if (form.zip_code.length === 8) {
        isLoading.value = true;
        try {
            const response = await axios.get(`https://viacep.com.br/ws/${form.zip_code}/json/`);
            if (response.data && !response.data.erro) {
                form.address = response.data.logradouro || '';
                form.neighborhood = response.data.bairro || '';
                form.city = response.data.localidade || '';
                form.state = response.data.uf || '';
                form.country = 'Brasil';
            } else {
                form.address = '';
                form.neighborhood = '';
                form.city = '';
                form.state = '';
                form.country = '';
            }
        } catch (error) {
            console.error('Erro ao buscar CEP', error);
            form.address = '';
            form.neighborhood = '';
            form.city = '';
            form.state = '';
            form.country = '';
        } finally {
            isLoading.value = false;
        }
    }
};

watch(() => form.zip_code, getAddressFromZipCode);

// Modal
const termsDialog = ref<HTMLDialogElement | null>(null);
const openTerms = () => termsDialog.value?.showModal();
const closeTerms = () => termsDialog.value?.close();

// Envio
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Criar conta" description="Insira seus detalhes abaixo para criar sua conta">

        <Head title="Criar conta" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <!-- Dados Pessoais -->
                <div class="grid gap-2">
                    <Label for="name">Nome</Label>
                    <Input id="name" v-model="form.name" type="text" required placeholder="Nome completo" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" required placeholder="email@exemplo.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Senha</Label>
                    <Input id="password" v-model="form.password" type="password" required placeholder="Senha" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar senha</Label>
                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required
                        placeholder="Confirmar senha" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>
                <div class="grid gap-2">
                    <Label for="password_confirmation">Data de nascimento</Label>
                    <Input id="birthdate" v-model="form.birthdate" type="date" required
                        placeholder="Data de nascimento" />
                    <InputError :message="form.errors.birthdate" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="rg">RG</Label>
                        <Input id="rg" v-model="form.rg" type="text" required placeholder="UF00000000"
                            maxlength="10" />
                        <InputError :message="form.errors.rg" />
                    </div>
                    <div>
                        <Label for="cpf">CPF</Label>
                        <Input id="cpf" v-model="form.cpf" type="text" required placeholder="00000000000"
                            maxlength="11" />
                        <InputError :message="form.errors.cpf" />
                    </div>
                </div>

                <!-- Endereço -->
                <div class="grid gap-2">
                    <Label for="zip_code">CEP</Label>
                    <div class="relative">
                        <Input id="zip_code" v-model="form.zip_code" type="text" required placeholder="CEP"
                            maxlength="8" @input="getAddressFromZipCode" />
                        <LoaderCircle v-if="isLoading"
                            class="absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 animate-spin" />
                    </div>
                    <InputError :message="form.errors.zip_code" />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Endereço</Label>
                    <Input id="address" v-model="form.address" type="text" required placeholder="Rua, Avenida..." />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="number">Número</Label>
                        <Input id="number" v-model="form.number" type="text" required placeholder="Número" />
                        <InputError :message="form.errors.number" />
                    </div>
                    <div>
                        <Label for="neighborhood">Bairro</Label>
                        <Input id="neighborhood" v-model="form.neighborhood" type="text" required
                            placeholder="Bairro" />
                        <InputError :message="form.errors.neighborhood" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="complement">Complemento</Label>
                        <Input id="complement" v-model="form.complement" type="text" placeholder="Complemento" />
                        <InputError :message="form.errors.complement" />
                    </div>
                    <div>
                        <Label for="city">Cidade</Label>
                        <Input id="city" v-model="form.city" type="text" required placeholder="Cidade" />
                        <InputError :message="form.errors.city" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="state">Estado</Label>
                        <Input id="state" v-model="form.state" type="text" required placeholder="Estado" />
                        <InputError :message="form.errors.state" />
                    </div>
                    <div>
                        <Label for="country">País</Label>
                        <Input id="country" v-model="form.country" type="text" required placeholder="Brasil" />
                        <InputError :message="form.errors.country" />
                    </div>
                </div>

                <!-- Termos de uso -->
                <div class="flex items-start gap-2">
                    <input id="terms" type="checkbox" v-model="form.terms" />
                    <label for="terms" class="text-sm">
                        Eu li e aceito os
                        <button type="button" class="text-blue-600 underline" @click="openTerms">
                            Termos de Uso
                        </button>
                    </label>
                </div>
                <InputError :message="form.errors.terms" />

                <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Criar conta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Já tem conta?
                <TextLink :href="route('login')" class="underline underline-offset-4">Faça login</TextLink>
            </div>
        </form>

        <!-- Modal de Termos de Uso -->
        <dialog ref="termsDialog"
            class="rounded-2xl max-w-2xl w-full p-6 bg-white shadow-2xl backdrop:bg-black/50 backdrop:backdrop-blur">
            <h2 class="text-lg font-semibold mb-4">Termos de Uso</h2>
            <div class="text-sm max-h-96 overflow-y-auto space-y-2">
                <p>Bem-vindo aos nossos Termos de Uso.</p>
                <p>Ao se registrar, você concorda com os seguintes termos:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Você é responsável por manter seus dados atualizados.</li>
                    <li>Não compartilhe sua conta com terceiros.</li>
                    <!-- Adicione mais cláusulas conforme necessário -->
                </ul>
            </div>
            <div class="flex justify-end mt-6">
                <Button @click="closeTerms">Fechar</Button>
            </div>
        </dialog>
    </AuthBase>
</template>
