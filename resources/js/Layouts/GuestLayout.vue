<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { ConfigProvider } from "ant-design-vue";
import { Link } from "@inertiajs/vue3";

ConfigProvider.config({
    theme: {
        primaryColor: "#ff914d",
    },
});
</script>

<template>
    <div
        class="min-h-screen flex gap-2 sm:justify-center items-center pt-6 sm:pt-0 bg-[#FDF7F2]"
    >
        <div>
            <Link href="/">
                <img src="/MS_LOGO.png" width="450" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
