<script setup lang="ts">
import PatientLayout from '@/layouts/PatientLayout.vue'
import ProfileForm from '@/components/ProfileForm.vue'
import type { Profile } from '@/types'

defineOptions({ layout: PatientLayout })

defineProps<{ userName: string; profile: Profile | null }>()
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">My Profile</h1>
        <p class="text-sm text-gray-500 mb-6">Update your personal information.</p>
        <ProfileForm :user-name="userName" :profile="profile" update-url="/patient/profile" />
    </div>
</template>
