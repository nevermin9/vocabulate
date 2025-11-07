<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import logoImg from "../../images/logo.png";

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        :src="logoImg"
        title="Vocabulate logo"
    />
</template>
