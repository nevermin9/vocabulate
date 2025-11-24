<script setup lang="ts">
import FlashcardStack from "@/components/FlashcardStack.vue";
import StacksGrid from '@/components/StacksGrid.vue';

import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const stacks = [
    {
        name: 'foo',
        id: 42
    },

    {
        name: 'bar',
        id: 10
    },

    {
        name: 'baz',
        id: 56
    }
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <StacksGrid>
            <li>
                <FlashcardStack />
            </li>

            <li v-for="stack of stacks" :key="stack.id">
                <FlashcardStack :stack-id="stack.id" :stack-name="stack.name" />
            </li>
        </StacksGrid>
    </AppLayout>
</template>
