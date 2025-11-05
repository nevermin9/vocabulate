<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
    defaultValue?: string | number
    modelValue?: string | number
    class?: HTMLAttributes['class']
}>()

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
})
</script>

<template>
    <input
        v-model="modelValue"
        data-slot="input"
        :class="cn(
            'flex h-10 w-full min-w-0 px-3 py-1',
            'rounded-lg border-2 border-solid border-transparent focus-visible:not-user-invalid:not-aria-invalid:border-border',
            'outline outline-solid outline-ring hover:not-user-invalid:not-aria-invalid:outline-ring-active focus-visible:not-user-invalid:not-aria-invalid:outline-transparent',
            'bg-input',
            'text-base md:text-sm',
            'transition-all transition-200',
            'aria-invalid:not-focus-visible:outline-destructive aria-invalid:focus-visible:outline-transparent aria-invalid:focus-visible:border-destructive',
            'file:text-foreground file:inline-flex file:h-9 file:border-0 file:bg-transparent file:text-sm file:font-medium',
            'disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-30',
            'placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground',
            props.class,
        )"
    >
</template>
