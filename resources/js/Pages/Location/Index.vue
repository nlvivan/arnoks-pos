<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { notification } from "ant-design-vue";
import { watchDebounced } from "@vueuse/core";
import {
    EditOutlined,
    DeleteOutlined,
    FilterOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    locations: Object,
    filters: "",
});

const visible = ref(false);
const isEdit = ref(false);
const loading = ref(false);
const search = ref(props.filters.search);

const current = ref(props.locations.current_page);
const pageSize = ref(props.locations.per_page);

const pagination = computed(() => ({
    total: props.locations.total,
    current: current.value,
    pageSize: pageSize.value,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: false,
}));

const handleTableChange = (event) => {
    current.value = event.current;
    pageSize.value = event.pageSize;

    router.get(
        route("locations.index"),
        {
            search: search.value,
            page: event.current,
        },
        {
            replace: true,
            preserveState: true,
        }
    );
};

const openModal = () => {
    isEdit.value = false;
    visible.value = true;
};

const selectedId = ref(null);
const editCategory = (data) => {
    selectedId.value = data.id;
    isEdit.value = true;
    visible.value = true;
    form.name = data.name;
};

const handleCancel = () => {
    form.reset();
    visible.value = false;
};

const form = useForm({
    name: "",
});

const submitForm = () => {
    form.post(route("locations.store"), {
        preserveScroll: false,
        onSuccess: () => {
            visible.value = false;
            form.reset();
            notification.success({
                message: "Location Created Successfully",
                placement: "bottomRight",
                duration: 1.5,
            });
        },
    });
};

const updateForm = () => {
    form.put(route("locations.update", selectedId.value), {
        preserveScroll: false,
        onSuccess: () => {
            visible.value = false;
            form.reset();
            notification.success({
                message: "Location Updated Successfully",
                placement: "bottomRight",
                duration: 1.5,
            });
        },
    });
};

const deleteCategory = (id) => {
    selectedId.value = id;
    loading.value = true;
    form.delete(route("locations.destroy", id), {
        preserveScroll: true,
        onSuccess: () => {
            loading.value = false;
            notification.success({
                message: "Location Deleted Successfully",
                placement: "bottomRight",
                duration: 1.5,
            });
        },
    });
};

const columns = [
    {
        title: "Id",
        dataIndex: "id",
        key: "id",
    },
    {
        title: "Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Action",
        dataIndex: "action",
        key: "action",
        class: "w-1",
    },
];

watchDebounced(
    search,
    () => {
        router.get(
            route("locations.index"),
            { search: search.value },
            {
                preserveState: true,
                replace: true,
            }
        );
    },
    { debounce: 300 }
);
</script>

<template>
    <Head title="Locations" />

    <AuthenticatedLayout>
        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex justify-between">
                        <div class="text-gray-900 font-bold text-2xl">
                            Locations
                        </div>
                        <div>
                            <a-input-search
                                v-model:value="search"
                                :allowClear="true"
                                placeholder="Search"
                                style="width: 200px"
                            />
                            <a-button type="primary" class="" @click="openModal"
                                >Add Location</a-button
                            >
                        </div>
                    </div>

                    <a-table
                        :dataSource="locations.data"
                        :columns="columns"
                        @change="handleTableChange"
                        :pagination="{ ...pagination }"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'action'">
                                <div class="flex gap-4">
                                    <a-tooltip title="Edit">
                                        <a-button
                                            @click="editCategory(record)"
                                            shape="circle"
                                        >
                                            <template #icon>
                                                <EditOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <a-popconfirm
                                        title="Are you sure to delete this location?"
                                        ok-text="Yes"
                                        cancel-text="No"
                                        @confirm="deleteCategory(record.id)"
                                    >
                                        <a-tooltip title="Delete">
                                            <a-button
                                                shape="circle"
                                                :loading="
                                                    loading &&
                                                    selectedId === record.id
                                                "
                                            >
                                                <template #icon>
                                                    <DeleteOutlined />
                                                </template>
                                            </a-button>
                                        </a-tooltip>
                                    </a-popconfirm>
                                </div>
                            </template>
                        </template>
                    </a-table>
                </div>
            </div>
            <a-modal
                :maskClosable="true"
                v-model:visible="visible"
                title="Create Location"
                :footer="false"
                size="md"
            >
                <div class="flex flex-col gap-2">
                    <a-form @submit.prevent="isEdit ? updateForm : submitForm">
                        <div>
                            <a-form-item
                                label="Name"
                                :validate-status="
                                    form.errors.name ? 'error' : null
                                "
                                :help="form.errors.name"
                            >
                                <a-input
                                    v-model:value="form.name"
                                    placeholder="Location name"
                                    allow-clear
                                />
                            </a-form-item>
                            <div class="w-full">
                                <a-form-item
                                    :wrapper-col="{ offset: 4, span: 20 }"
                                    class="mb-0"
                                >
                                    <div class="flex justify-end gap-2">
                                        <a-button
                                            key="back"
                                            @click="handleCancel"
                                            >Cancel</a-button
                                        >
                                        <a-button
                                            v-if="!isEdit"
                                            type="primary"
                                            htmlType="submit"
                                            :loading="form.processing"
                                            @click="submitForm"
                                            >Submit</a-button
                                        >
                                        <a-button
                                            v-else
                                            type="primary"
                                            htmlType="submit"
                                            :loading="form.processing"
                                            @click="updateForm"
                                            >Update</a-button
                                        >
                                    </div>
                                </a-form-item>
                            </div>
                        </div>
                    </a-form>
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
