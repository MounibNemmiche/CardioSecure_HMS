<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

const deferredPrompt = ref<any>(null)
const showPrompt = ref(false)
const dismissed = ref(false)

function handleBeforeInstallPrompt(e: Event) {
    e.preventDefault()
    deferredPrompt.value = e
    // Don't show if user previously dismissed this session
    if (!dismissed.value) {
        showPrompt.value = true
    }
}

async function install() {
    if (!deferredPrompt.value) return
    deferredPrompt.value.prompt()
    const { outcome } = await deferredPrompt.value.userChoice
    if (outcome === 'accepted') {
        showPrompt.value = false
    }
    deferredPrompt.value = null
}

function dismiss() {
    showPrompt.value = false
    dismissed.value = true
}

onMounted(() => {
    // Pick up the event if it already fired before this component mounted
    if ((window as any)._pwaInstallPrompt) {
        deferredPrompt.value = (window as any)._pwaInstallPrompt
        if (!dismissed.value) showPrompt.value = true
    }
    // Also listen for future fires and for the global relay event
    window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
    window.addEventListener('pwa-install-ready', () => {
        if ((window as any)._pwaInstallPrompt && !dismissed.value) {
            deferredPrompt.value = (window as any)._pwaInstallPrompt
            showPrompt.value = true
        }
    })
    window.addEventListener('appinstalled', () => {
        showPrompt.value = false
    })
})

onBeforeUnmount(() => {
    window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
})
</script>

<template>
    <Transition
        enter-from-class="translate-y-full opacity-0"
        enter-active-class="transition duration-300 ease-out"
        enter-to-class="translate-y-0 opacity-100"
        leave-from-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-to-class="translate-y-full opacity-0"
    >
        <div v-if="showPrompt" class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:w-96 z-50 bg-white rounded-xl border border-gray-200 shadow-lg p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-800 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">Install CardioSecure</p>
                    <p class="text-xs text-gray-500 mt-0.5">Get medication reminders and quick access from your home screen.</p>
                    <div class="flex items-center gap-2 mt-3">
                        <button
                            @click="install"
                            class="px-3 py-1.5 bg-blue-800 hover:bg-blue-900 text-white text-xs font-semibold rounded-lg transition-colors duration-200 cursor-pointer"
                        >
                            Install
                        </button>
                        <button
                            @click="dismiss"
                            class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors duration-200 cursor-pointer"
                        >
                            Not now
                        </button>
                    </div>
                </div>
                <button @click="dismiss" class="text-gray-400 hover:text-gray-600 cursor-pointer transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
