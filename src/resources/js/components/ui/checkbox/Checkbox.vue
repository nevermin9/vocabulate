<script setup lang="ts">
import type { CheckboxRootEmits, CheckboxRootProps } from 'reka-ui'
import { cn } from '@/lib/utils'
import { Check } from 'lucide-vue-next'
import { CheckboxIndicator, CheckboxRoot, useForwardPropsEmits } from 'reka-ui'
import { computed, type HTMLAttributes } from 'vue'

const props = defineProps<CheckboxRootProps & { class?: HTMLAttributes['class'] }>()
const emits = defineEmits<CheckboxRootEmits>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props

    return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
    <CheckboxRoot
        data-slot="checkbox"
        v-bind="forwarded"
        :class="
        cn('peer data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground',
            'border-2 border-solid border-transparent focus-visible:not-user-invalid:not-aria-invalid:border-border',
            'size-4 shrink-0 rounded-[4px] shadow-xs transition-all transition-200',
            'outline outline-solid outline-ring hover:not-user-invalid:not-aria-invalid:outline-ring-active focus-visible:not-user-invalid:not-aria-invalid:outline-transparent',
            'data-[state=checked]:border-primary data-[state=checked]:outline-primary',
            'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
            'disabled:cursor-not-allowed disabled:opacity-50',
            props.class)"
    >
        <CheckboxIndicator
            data-slot="checkbox-indicator"
            class="flex items-center justify-center text-current transition-none"
            force-mount
        >
            <slot>
                <svg
                    class="size-[14px] stroke-2 stroke-primary-foreground [stroke-linecap:round] [stroke-linejoin:round] fill-none [stroke-dasharray:50] [stroke-dashoffset:50] [transition-property:stroke-dashoffset] duration-[600ms] ease-[cubic-bezier(0.68,-0.55,0.27,1.55)] [button[data-state='checked']_&]:[stroke-dashoffset:10]"
                    viewBox="0 0 24 24"
                >
                    <path d="M5 12l5 5l10-10"></path>
                </svg>
            </slot>
        </CheckboxIndicator>
    </CheckboxRoot>
</template>
