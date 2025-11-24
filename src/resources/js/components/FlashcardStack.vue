<script lang="ts" setup>
import { cva } from "class-variance-authority";
import { cn } from "@/lib/utils";
import { ref, onBeforeMount, defineProps, computed } from "vue";
import { Plus } from "lucide-vue-next"

interface Props {
    stackName?: string;
    stackId?: number;
}

const props = defineProps<Props>();
const isBlank = computed(() => !props.stackName && !props.stackId);


// background-color: oklch(27.8% 0.033 256.848);
const stackCardVariants = cva(
    cn(
        "flex justify-center items-center w-full h-full",
        "rounded-lg",
        "z-3"
    ),
    {
        variants: {
            variant: {
                bottom: "absolute inset-0 rotate-(--bottom-rotation) z-1",
                middle: "absolute inset-0 rotate-(--middle-rotation) z-2",
                top: "relative",
                blank: "relative",
            },
            border: {
                solid: "border-2 border-white border-solid",
                dashed: "border-1 border-dashed border-foreground",
            },
            bg: {
                default: "bg-[oklch(27.8%_0.033_256.848)]",
                transparent: "bg-transparent"
            }
        },
        defaultVariants: {
            variant: "top",
            border: "solid",
            bg: "default",
        }
    }
);

const generateRandomRotation = () => {
    return {
        bottom: (Math.random() * 5 - 6).toFixed(2),
        middle: (Math.random() * 5 + 1).toFixed(2)
    };
};
const bottom = ref<string>("0deg");
const middle = ref<string>("0deg");

onBeforeMount(() => {
    if (props.stackId && props.stackName) {
        const { bottom: b, middle: m } = generateRandomRotation();
        bottom.value = `${b}deg`;
        middle.value = `${m}deg`;
    }
});
/**
 * questions
 */

// aria-label="Open form to create a flashcard stack"
// aria-haspopup="dialog"
// SHOULD be updated
// aria-expanded="false"
</script>

<template>
    <div
        :style="`--bottom-rotation: ${bottom}; --middle-rotation: ${middle}`"
        class="relative w-full h-full"
    >

        <template v-if="isBlank">
            <div :class="stackCardVariants({ variant: 'blank', border: 'dashed', bg: 'transparent' })">
                <button
                    aria-label="Open form to create a flashcard stack"
                    aria-haspopup="dialog"
                    aria-expanded="false"
                    class="bg-transparent border-0 cursor-pointer text-foreground"
                    type="button"
                >
                    <Plus :size="36" />
                </button>
            </div>
        </template>

        <template v-else>
            <div :class="stackCardVariants({ variant: 'bottom' })"></div>
            <div :class="stackCardVariants({ variant: 'middle' })"></div>

            <div :class="stackCardVariants({ variant: 'top' })">
                <a :href="`/stack/${stackId}`">
                    {{ stackName }}
                </a>
            </div>
        </template>
    </div>
</template>
