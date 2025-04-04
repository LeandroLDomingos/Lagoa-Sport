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
    zip_code: '',
    address: '',
    neighborhood: '',
    number: '',
    complement: '',
    city: '',
    state: '',
    country: '',
    status: 'pending'
});

const isLoading = ref(false); // Para controlar se a consulta está carregando

// Função para buscar o endereço via CEP
const getAddressFromZipCode = async () => {
    if (form.zip_code.length === 8) { // Verifica se o CEP tem 8 caracteres
        isLoading.value = true;
        try {
            const response = await axios.get(`https://viacep.com.br/ws/${form.zip_code}/json/`);
            if (response.data && !response.data.erro) {
                form.address = response.data.logradouro || '';
                form.neighborhood = response.data.bairro || '';
                form.city = response.data.localidade || '';
                form.state = response.data.uf || '';
                form.country = 'Brasil'; // Se você souber que o país será sempre o Brasil, já define aqui
            } else {
                // Lida com erro de CEP inválido
                form.address = '';
                form.neighborhood = '';
                form.city = '';
                form.state = '';
                form.country = '';
            }
        } catch (error) {
            console.error('Erro ao buscar CEP', error);
            // Lida com erro de requisição
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

// Adiciona um watcher para o campo zip_code
watch(() => form.zip_code, getAddressFromZipCode);

// Função de envio do formulário
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
                <div class="grid gap-2">
                    <Label for="name">Nome</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name"
                        v-model="form.name" placeholder="Nome completo" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Endereço de e-mail</Label>
                    <Input id="email" type="email" required :tabindex="2" autofocus autocomplete="email" v-model="form.email"
                        placeholder="email@exemplo.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Senha</Label>
                    <Input id="password" type="password" required :tabindex="3" autofocus autocomplete="new-password"
                        v-model="form.password" placeholder="Senha" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar senha</Label>
                    <Input id="password_confirmation" type="password" required :tabindex="4" autocomplete="new-password"
                        v-model="form.password_confirmation" placeholder="Confirmar senha" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <Label for="cpf">CPF</Label>
                    <Input id="cpf" type="text" required autofocus :tabindex="5" autocomplete="cpf" v-model="form.cpf"
                        placeholder="CPF" maxlength="11" />
                    <InputError :message="form.errors.cpf" />
                </div>

                <div class="grid gap-2">
                    <div>
                        <Label for="zip_code">CEP</Label>
                        <div class="relative">
                            <Input id="zip_code" type="text" required autofocus :tabindex="6" autocomplete="zip_code"
                                v-model="form.zip_code" placeholder="CEP" maxlength="8" @input="getAddressFromZipCode" />
                            <InputError :message="form.errors.zip_code" />
                            <LoaderCircle v-if="isLoading" class="absolute right-2 top-1/2 transform -translate-y-1/2 h-4 w-4 animate-spin" />
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="address">Endereço</Label>
                    <Input id="address" type="text" required autofocus :tabindex="7" autocomplete="address"
                        v-model="form.address" placeholder="Endereço" maxlength="11" />
                    <InputError :message="form.errors.address" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="number">Número</Label>
                        <Input id="number" type="text" required autofocus :tabindex="8" autocomplete="number"
                            v-model="form.number" placeholder="Número" />
                        <InputError :message="form.errors.number" />
                    </div>
                    <div>
                        <Label for="neighborhood">Bairro</Label>
                        <Input id="neighborhood" type="text" required autofocus :tabindex="9"
                            autocomplete="neighborhood" v-model="form.neighborhood" placeholder="Bairro" />
                        <InputError :message="form.errors.neighborhood" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="complement">Complemento</Label>
                        <Input id="complement" type="text" autofocus :tabindex="10" autocomplete="complement"
                            v-model="form.complement" placeholder="Complemento" maxlength="8" />
                        <InputError :message="form.errors.complement" />
                    </div>
                    <div>
                        <Label for="city">Cidade</Label>
                        <Input id="city" type="text" required autofocus :tabindex="11" autocomplete="city"
                            v-model="form.city" placeholder="Cidade" />
                        <InputError :message="form.errors.city" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <Label for="state">Estado</Label>
                        <Input id="state" type="text" required autofocus :tabindex="12" autocomplete="state"
                            v-model="form.state" placeholder="Estado" maxlength="8" />
                        <InputError :message="form.errors.state" />
                    </div>
                    <div>
                        <Label for="country">País</Label>
                        <Input id="country" type="text" required autofocus :tabindex="13" autocomplete="country"
                            v-model="form.country" placeholder="País" maxlength="8" />
                        <InputError :message="form.errors.country" />
                    </div>
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="14" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Criar conta
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Já tem conta?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="15">Faça login
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
