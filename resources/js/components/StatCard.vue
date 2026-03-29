<script setup lang="ts">
defineProps<{
    label: string
    value: string | number
    subtext?: string
    color?: 'blue' | 'emerald' | 'amber' | 'violet' | 'rose'
}>()
</script>

<template>
    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:border-gray-300 transition-colors duration-200">
        <div class="flex items-start justify-between">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">{{ label }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ value }}</p>
                <p v-if="subtext" class="text-sm text-gray-400 mt-1 truncate">{{ subtext }}</p>
            </div>
            <div v-if="$slots.icon" class="ml-3 shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                :class="{
                    'bg-blue-50 text-blue-600': !color || color === 'blue',
                    'bg-emerald-50 text-emerald-600': color === 'emerald',
                    'bg-amber-50 text-amber-600': color === 'amber',
                    'bg-violet-50 text-violet-600': color === 'violet',
                    'bg-rose-50 text-rose-600': color === 'rose',
                }"
            >
                <slot name="icon" />
            </div>
        </div>
    </div>
</template>
