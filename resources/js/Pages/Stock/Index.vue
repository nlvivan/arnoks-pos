<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { notification, Modal } from 'ant-design-vue'
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
    filters: '',
    productStocksDetails: Array
})

const form = useForm({
    quantity: null,
    critical_stock: null,
    stock_added: null
})
const showAlertNotification = ref(false)
onMounted(() => {
    if (props.productStocksDetails) {
        Modal.warning({
            title: () => 'There have products that has low stock please check the Product',
            content: () => {
                return props.productStocksDetails.map((prod) => {
                    return prod.name + ','
                })
            }
        })
    }
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

    form.quantity = data.quantity
    form.critical_stock = data.critical_stock
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

const updateForm = () => {
    form.post(route('productStock.update', selectedId.value), {
        preserveScroll: true,
        onSuccess: () => {
            visible.value = false
            form.reset()
            notification.success({
                message: 'Product Stock Updated Successfully',
                placement: 'bottomRight',
                duration: 1
            })
        },
        onError: (error) => {
            // formError.value = error

        }
    })
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
        title: 'Stock',
        dataIndex: 'quantity',
        key: 'name',
    },
    {
        title: 'Critical Stock',
        dataIndex: 'critical_stock',
        key: 'critical_stock',
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
            route('productStock.index'),
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
                        <div class="text-gray-900 font-bold text-2xl">Stocks</div>
                        <div>
                            <a-input-search v-model:value="search" :allowClear="true" placeholder="Search"
                                style="width: 200px" />
                        </div>

                    </div>

                    <a-table :dataSource="products.data" :columns="columns" class="mt-6">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'quantity'
                                ">
                                <span class="font-bold"
                                    :class="record.quantity < record.critical_stock ? 'text-red-800' : 'text-green-800'">
                                    {{ record.quantity }}</span>
                            </template>
                            <template v-if="column.dataIndex === 'action'
                                ">
                                <div class="flex gap-4">
                                    <a-tooltip title="Edit">
                                        <a-button @click="EditProduct(record)">
                                            Add Stock / Edit Critical Stock
                                        </a-button>
                                    </a-tooltip>
                                </div>
                            </template>
                        </template>

                    </a-table>
                </div>
            </div>
            <a-modal :maskClosable="true" v-model:visible="visible" title="Update Stock / Critical Stock" :footer="false"
                size="md">
                <div class="flex flex-col gap-2">
                    <a-form :label-col="{ span: 6 }" :wrapper-col="{ span: 18 }"
                        @submit.prevent="isEdit ? updateForm : submitForm">
                        <div>
                            <a-form-item label="Current Stock" :validate-status="form.errors.quantity ? 'error' : null"
                                :help="form.errors.quantity">
                                <a-input disabled type="number" v-model:value="form.quantity" placeholder="Stock"
                                    allow-clear />
                            </a-form-item>
                            <a-form-item label="Number of Stock" :validate-status="form.errors.stock_added ? 'error' : null"
                                :help="form.errors.stock_added">
                                <a-input type="number" v-model:value="form.stock_added" placeholder="Stock" allow-clear />
                            </a-form-item>
                            <a-form-item label="Critical Stock"
                                :validate-status="form.errors.critical_stock ? 'error' : null"
                                :help="form.errors.critical_stock">
                                <a-input type="number" v-model:value="form.critical_stock" placeholder="Stock"
                                    allow-clear />
                            </a-form-item>
                            <div class="w-full">
                                <a-form-item :wrapper-col="{ offset: 4, span: 20 }" class="mb-0">
                                    <div class="flex justify-end gap-2">
                                        <a-button key="back" @click="handleCancel">Cancel</a-button>
                                        <a-button v-if="!isEdit" type="primary" htmlType="submit" :loading="form.processing"
                                            @click="submitForm">Save</a-button>
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
