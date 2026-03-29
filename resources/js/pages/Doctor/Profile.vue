<script setup lang="ts">
import DoctorLayout from '@/layouts/DoctorLayout.vue'
import ProfileForm from '@/components/ProfileForm.vue'
import type { Profile } from '@/types'

defineOptions({ layout: DoctorLayout })

defineProps<{ userName: string; profile: Profile | null }>()
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">My Profile</h1>
        <p class="text-sm text-gray-500 mb-6">Update your personal information.</p>
        <ProfileForm :user-name="userName" :profile="profile" update-url="/doctor/profile" />
    </div>
</template>
