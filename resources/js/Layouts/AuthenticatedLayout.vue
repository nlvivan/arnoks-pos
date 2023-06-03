<script setup>
import { ref, computed, watch } from "vue";
import {
    UserOutlined,
    VideoCameraOutlined,
    UploadOutlined,
    DashboardOutlined,
} from "@ant-design/icons-vue";
import { Head, useForm, usePage, router } from "@inertiajs/vue3";
import { ConfigProvider } from "ant-design-vue";
const page = computed(() => usePage());
const user = computed(() => page.value.props.auth?.user);

const userRole = computed(() => page.value.props.auth?.user?.roles[0].name);

console.log(userRole.value);

ConfigProvider.config({
    theme: {
        primaryColor: "#ff914d",
    },
});

// get the current url without query string
const selectedKeys = ref([window.location.href.split("?")[0]]);
watch(
    () => page.value.url,
    () => {
        const current = window.location.href.split("?")[0];
        if (selectedKeys.value[0] !== current) {
            selectedKeys.value = [current];
        }
    }
);

const onCollapse = (collapsed, type) => {
    console.log(collapsed, type);
};

const links = computed(() => {
    if (userRole.value === "admin") {
        return [
            {
                label: "Dashboard",
                path: route("dashboard"),
                // icon: DashboardOutlined,
            },
            {
                label: "Cashier",
                path: route("point-of-sale.index"),
                // icon: DashboardOutlined,
            },
            {
                label: "Categories",
                path: route("categories.index"),
                //  icon: DashboardOutlined,
            },
            {
                label: "Locations",
                path: route("locations.index"),
                //  icon: DashboardOutlined,
            },
            {
                label: "Products",
                path: route("products.index"),
                // icon: DashboardOutlined,
            },

            {
                label: "Stocks",
                path: route("productStock.index"),
                // icon: DashboardOutlined,
            },
        ];
    } else {
        return [
            {
                label: "Cashier",
                path: route("point-of-sale.index"),
                // icon: DashboardOutlined,
            },
        ];
    }
});

const onBreakpoint = (broken) => {
    console.log(broken);
};
</script>

<style>
#components-layout-demo-responsive .logo {
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    margin: 16px;
}

.site-layout-sub-header-background {
    background: #fff;
}

.site-layout-background {
    background: #fff;
}

[data-theme="dark"] .site-layout-sub-header-background {
    background: #141414;
}
</style>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <a-layout>
                <a-layout-sider
                    :style="{
                        overflow: 'auto',
                        height: '100vh',
                        position: 'sticky',
                        left: 0,
                        top: 0,
                        bottom: 0,
                    }"
                    breakpoint="lg"
                    collapsed-width="0"
                    @collapse="onCollapse"
                    @breakpoint="onBreakpoint"
                >
                    <div class="logo p-2">
                        <img src="/MS_LOGO.png" />
                    </div>

                    <a-menu
                        class="bg-gray-900"
                        v-model:selectedKeys="selectedKeys"
                        theme="dark"
                        mode="inline"
                    >
                        <a-menu-item v-for="link in links" :key="link.path">
                            <Link :href="link.path">
                                <template v-if="link.icon">
                                    <component :is="link.icon"></component>
                                </template>
                                <span class="font-medium">{{
                                    link.label
                                }}</span>
                            </Link>
                        </a-menu-item>
                    </a-menu>
                </a-layout-sider>
                <a-layout>
                    <a-layout-header
                        :style="{ background: '#fff', padding: '4px' }"
                    >
                        <a-dropdown
                            :trigger="['click']"
                            class="flex items-center float-right"
                            placement="bottomRight"
                        >
                            <div>
                                <button
                                    type="button"
                                    class="flex items-center rounded-full bg-white text-sm focus:outline-none"
                                >
                                    <span class="mr-3 font-medium">{{
                                        user.name
                                    }}</span>
                                    <img
                                        class="h-10 w-10 rounded-full"
                                        :src="`https://ui-avatars.com/api/?name=${user?.name}`"
                                        alt=""
                                    />
                                </button>
                            </div>
                            <template #overlay>
                                <a-menu>
                                    <a-menu-item key="3">
                                        <div class="font-semibold">
                                            {{ user.name }}
                                        </div>
                                        <div class="text-gray-500 text-sm">
                                            {{ user.email }}
                                        </div>
                                    </a-menu-item>
                                    <a-menu-item key="4"> </a-menu-item>
                                    <a-menu-item key="1">
                                        <Link
                                            :href="route('profile.edit')"
                                            as="button"
                                            type="button"
                                        >
                                            Profile
                                        </Link>
                                    </a-menu-item>
                                    <a-menu-item key="2">
                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            type="button"
                                        >
                                            Logout
                                        </Link>
                                    </a-menu-item>
                                </a-menu>
                            </template>
                        </a-dropdown>
                    </a-layout-header>
                    <a-layout-content :style="{ margin: '24px 16px 0' }">
                        <div
                            :style="{ background: '#fff', minHeight: '360px' }"
                        >
                            <slot />
                        </div>
                    </a-layout-content>
                    <a-layout-footer style="text-align: center">
                        MS Pharmacy and General Merchandise ©2023 Created Team
                        MS
                    </a-layout-footer>
                </a-layout>
            </a-layout>
        </div>
    </div>
</template>
