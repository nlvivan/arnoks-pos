<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { notification } from 'ant-design-vue'
import { watchDebounced } from '@vueuse/core'
import {
    EditOutlined,
    DeleteOutlined,
    FilterOutlined
} from '@ant-design/icons-vue'

const fileList = ref([])
const props = defineProps({
    products: Object,
    categories: '',
    filters: ''
})

const form = useForm({
    name: '',
    description: '',
    image: '',
    hasImage: '',
    category_id: '',
    quantity: '',
    price: ''
})

const visible = ref(false)
const isEdit = ref(false)
const loading = ref(false)
const search = ref(props.filters.search)

const openModal = () => {
    isEdit.value = false
    visible.value = true
}

const selectedId = ref(null)
const EditProduct = (data) => {
    selectedId.value = data.id
    isEdit.value = true
    visible.value = true

    form.name = data.name
    form.description = data.description
    form.category_id = data.category_id
    form.quantity = data.quantity
    form.price = data.price
    imageUrl.value = data.image ? `/storage/${data.image}` : null
    form.hasImage = data.image ? true : false
}

const handleCancel = () => {
    form.reset()
    visible.value = false
}

const imageUrl = ref('')
const handleChange = (info) => {
    getBase64(info.file.originFileObj, (base64Url) => {
        form.image = info.file.originFileObj
        form.hasImage = true
        imageUrl.value = base64Url
        loading.value = false
    })
}

function getBase64(img, callback) {
    const reader = new FileReader()
    reader.addEventListener('load', () => callback(reader.result))
    reader.readAsDataURL(img)
}

function removeLogo() {
    imageUrl.value = ''
    form.logo = ''
    form.hasLogo = false
    fileList.value = []
}




const submitForm = () => {
    form.post(route('products.store'),
        {
            preserveScroll: false,
            onSuccess: () => {
                visible.value = false
                imageUrl.value = ''
                form.reset()
                notification.success({
                    message: 'Product Created Successfully',
                    placement: 'bottomRight',
                    duration: 1.5
                })
            }
        }
    )
}

const updateLoading = ref(false)
const updateForm = () => {
    router.post(
        route('products.update', selectedId.value),
        {
            _method: 'put',
            id: selectedId.value,
            name: form.name,
            description: form.description,
            category_id: form.category_id,
            image: form.image,
            quantity: form.quantity,
            price: form.price,
            hasImage: form.hasImage
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                fileList.value = []
                visible.value = false
                notification.success({
                    message: 'Product Updated Successfully',
                    placement: 'bottomRight',
                    duration: 1
                })
                updateLoading.value = false
            },
            onError: (error) => {
                // formError.value = error
                updateLoading.value = false
            }
        }
    )
}

const deleteProduct = (id) => {
    selectedId.value = id
    loading.value = true
    form.delete(
        route('products.destroy', id),
        {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false
                notification.success({
                    message: 'Product Deleted Successfully',
                    placement: 'bottomRight',
                    duration: 1.5
                })
            }
        }
    )
}

const columns = [
    {
        title: 'Id',
        dataIndex: 'id',
        key: 'id',
    },
    {
        title: 'Name',
        dataIndex: 'name',
        key: 'name',
    },
    {
        title: 'Description',
        dataIndex: 'description',
        key: 'name',
    },
    {
        title: 'Category',
        dataIndex: ['category', 'name'],
        key: 'name',
    },
    {
        title: 'Stock',
        dataIndex: 'quantity',
        key: 'name',
    },
    {
        title: 'Price',
        dataIndex: 'price',
        key: 'name',
    },
    {
        title: 'Action',
        dataIndex: 'action',
        key: 'action',
        class: 'w-1'
    },
]

watchDebounced(
    search,
    () => {
        router.get(
            route('products.index'),
            { search: search.value },
            {
                preserveState: true,
                replace: true
            }
        )
    },
    { debounce: 300 }
)

</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class=" flex justify-between">
                        <div class="text-gray-900 font-bold text-2xl">Products</div>
                        <div>
                            <a-input-search v-model:value="search" :allowClear="true" placeholder="Search"
                                style="width: 200px" />
                            <a-button type="primary" class="" @click="openModal">Add Products</a-button>
                        </div>

                    </div>

                    <a-table :dataSource="products.data" :columns="columns">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'action'
                                ">
                                <div class="flex gap-4">
                                    <a-tooltip title="Edit">
                                        <a-button @click="EditProduct(record)" shape="circle">
                                            <template #icon>
                                                <EditOutlined />
                                            </template>
                                        </a-button>
                                    </a-tooltip>
                                    <a-popconfirm title="Are you sure to delete product?" ok-text="Yes" cancel-text="No"
                                        @confirm="deleteProduct(record.id)">
                                        <a-tooltip title="Delete">
                                            <a-button shape="circle" :loading="loading && selectedId === record.id">
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
            <a-modal :maskClosable="true" v-model:visible="visible" :title="isEdit ? 'Update Product' : 'Create Product'"
                :footer="false" size="md">
                <div class="flex flex-col gap-2">
                    <a-form :label-col="{ span: 4 }" :wrapper-col="{ span: 20 }"
                        @submit.prevent="isEdit ? updateForm : submitForm">
                        <div>
                            <a-form-item label="Name" :validate-status="form.errors.name ? 'error' : null"
                                :help="form.errors.name">
                                <a-input v-model:value="form.name" placeholder="Product name" allow-clear />
                            </a-form-item>
                            <a-form-item label="Description" :validate-status="form.errors.name ? 'error' : null"
                                :help="form.errors.description">
                                <a-textarea v-model:value="form.description" placeholder="Description" allow-clear />
                            </a-form-item>
                            <a-form-item label="Category" :validate-status="form.errors.category_id ? 'error' : null"
                                :help="form.errors.category_id">
                                <a-select ref="select" v-model:value="form.category_id" placeholder="Select Category"
                                    style="width: 100%" :options="categories.map((category) => ({
                                        value: category.id,
                                        label: category.name
                                    }))
                                        ">
                                </a-select>
                            </a-form-item>
                            <a-form-item label="Stock" :validate-status="form.errors.quantity ? 'error' : null"
                                :help="form.errors.quantity">
                                <a-input v-model:value="form.quantity" placeholder="Stock" allow-clear />
                            </a-form-item>
                            <a-form-item label="Price" :validate-status="form.errors.price ? 'error' : null"
                                :help="form.errors.price">
                                <a-input v-model:value="form.price" placeholder="Price" allow-clear />
                            </a-form-item>
                            <a-form-item label="Image" :validate-status="form.errors.image ? 'error' : null"
                                :help="form.errors.image">
                                <a-upload v-model:file-list="fileList" name="image" list-type="picture-card"
                                    class="avatar-uploader" :accept="'image/jpeg,image/png,image/jpg'"
                                    :show-upload-list="false" @change="handleChange">
                                    <img v-if="imageUrl" :src="imageUrl" alt="avatar"
                                        class="h-16 w-16 object-contain rounded" />
                                    <div v-else>
                                        <LoadingOutlined v-if="loading"></LoadingOutlined>
                                        <PlusOutlined v-else></PlusOutlined>
                                        <div class="ant-upload-text">Upload</div>
                                    </div>
                                </a-upload>
                                <a v-if="imageUrl" @click="removeLogo" class="cursor-pointer text-sm">Remove Image</a>
                            </a-form-item>
                            <div class="w-full">
                                <a-form-item :wrapper-col="{ offset: 4, span: 20 }" class="mb-0">
                                    <div class="flex justify-end gap-2">
                                        <a-button key="back" @click="handleCancel">Cancel</a-button>
                                        <a-button v-if="!isEdit" type="primary" htmlType="submit" :loading="form.processing"
                                            @click="submitForm">Submit</a-button>
                                        <a-button v-else type="primary" htmlType="submit" :loading="updateLoading"
                                            @click="updateForm">Update</a-button>
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
