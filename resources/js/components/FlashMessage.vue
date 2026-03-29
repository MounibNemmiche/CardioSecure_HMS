<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const page = usePage()
const flash = computed(() => page.props.flash as { success?: string; error?: string } | null)

const visible = ref(false)
const message = ref('')
const type = ref<'success' | 'error'>('success')

watch(flash, (val) => {
    if (val?.success) {
        message.value = val.success
        type.value = 'success'
        visible.value = true
        setTimeout(() => { visible.value = false }, 4000)
    } else if (val?.error) {
        message.value = val.error
        type.value = 'error'
        visible.value = true
        setTimeout(() => { visible.value = false }, 5000)
    }
}, { immediate: true })
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 -translate-y-2"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform opacity-100 translate-y-0"
        leave-to-class="transform opacity-0 -translate-y-2"
    >
        <div
            v-if="visible"
            class="fixed top-4 right-4 z-50 max-w-sm w-full px-4 py-3 rounded-lg shadow-lg border text-sm font-medium flex items-center justify-between gap-3"
            :class="type === 'success'
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800'"
        >
            <span>{{ message }}</span>
            <button class="text-current opacity-60 hover:opacity-100" @click="visible = false">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </Transition>
</template>
