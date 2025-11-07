<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center gap-6 bg-background p-6 md:p-10 h-full"
    >
        <div class="flex flex-col w-full flex-1">
            <Link
                :href="home()"
                class="flex flex-col items-center self-center"
            >
                <div
                    class="mb-1 flex size-32 items-center justify-center rounded-md"
                >
                    <AppLogoIcon class="object-contain" />
                </div>
            </Link>

            <div class="flex flex-col gap-8 w-full max-w-sm m-auto">
                <div class="flex flex-col items-center gap-4">
                    <span class="sr-only">
                        {{ title }}
                    </span>

                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">
                            {{ title }}
                        </h1>

                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
