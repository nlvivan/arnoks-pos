<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
const props = defineProps({
    products: Array,
    filters: '',
    change: ''
})
import { notification } from 'ant-design-vue'

const visible = ref(false)
const selectedProductId = ref(null)
const selectedProductQuantity = ref(null)
const selectedProduct = ref(null)
const quantityError = ref(false)
const cashError = ref(false)
const Ordervisible = ref(false)
const totalAmount = ref(0)

const openPosModal = (product) => {
    selectedProduct.value = product
    visible.value = true
    selectedProductId.value = product.id
}

const form = useForm({
    items: [],
    cash: 0,
    totalAmount: totalAmount.value
})

const handleCancel = () => {
    Ordervisible.value = false
}

const addOrder = () => {
    quantityError.value = false
    if (selectedProduct.value.quantity < selectedProductQuantity.value || !selectedProductQuantity.value) {
        quantityError.value = true
        return
    }
    const total = selectedProduct.value.price * selectedProductQuantity.value
    let obj = {
        product_id: selectedProductId.value,
        product_name: selectedProduct.value.name,
        quantity: selectedProductQuantity.value,
        total: total
    }
    form.items.push(obj)
    visible.value = false

    selectedProductId.value = ''
    selectedProductQuantity.value = ''
    selectedProduct.value = ''
}

const submitOrder = () => {
    cashError.value = false
    if (form.cash < totalAmount.value) {
        cashError.value = true
        return;
    }
    form.post(route('point-of-sale.store'),
        {
            preserveScroll: false,
            onSuccess: () => {
                notification.success({
                    message: 'Order Checkout',
                    placement: 'bottomRight',
                    duration: 1.5
                })
            }
        }
    )
}

const nextCustomer = () => {
    Ordervisible.value = false
    // form.reset()
    // totalAmount.value = 0
    // props.change = ''
    form.items = []
    form.cash = 0
    form.totalAmount = 0
    totalAmount.value = 0
    selectedProductId.value = null
    selectedProductQuantity.value = null
    selectedProduct.value = null
    quantityError.value = false
    cashError.value = false
    router.reload()
}

const viewOrderModal = () => {
    totalAmount.value = form.items.reduce((acc, obj) => acc + obj.total, 0)
    form.totalAmount = form.items.reduce((acc, obj) => acc + obj.total, 0)
    Ordervisible.value = true
}

</script>

<template>
    <Head title="POS" />

    <AuthenticatedLayout>
        <div class="p-12">
            <div class=" w-full flex justify-end">
                <a-button type="primary" size="large" class="mb-2" @click="viewOrderModal">View Orders</a-button>
            </div>
            <hr />
            <a-row type="flex mt-2">
                <a-col :span="6" :order="4" v-for="product in products" :key="product.id">
                    <a-card hoverable style="width: 240px" @click="openPosModal(product)">
                        <template #cover>
                            <img alt="example" :src="`storage/${product.image}`" />
                        </template>
                        <a-card-meta :title="product.name">
                            <template #description>
                                <div>
                                    {{ product.description }}
                                    <div class="mt-4">
                                        <p>stock: {{ product?.quantity }}</p>
                                        <p>price: {{ product?.price }}</p>
                                    </div>

                                </div>
                            </template>
                        </a-card-meta>
                    </a-card>
                </a-col>
            </a-row>
            <a-modal v-model:visible="visible" title="Order" @ok="addOrder">
                <p>Available Quantity: {{ selectedProduct?.quantity }}</p>
                <a-form-item label="Quantity" :validate-status="quantityError ? 'error' : null"
                    :help="quantityError ? 'Invalid Quantity' : null">
                    <a-input v-model:value="selectedProductQuantity" placeholder="Quantity" allow-clear />
                </a-form-item>
            </a-modal>

            <a-modal v-model:visible="Ordervisible" title="Order" width="1000px" :footer="null">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Product name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Quantity
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white border-b" v-for="orderItem in form.items" :key="orderItem.product_id">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ orderItem.product_name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ orderItem?.quantity }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ orderItem.total }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <div class=" w-full flex justify-end">
                        <p class="font-bold text-lg mt-4">Total Amount: {{ totalAmount }}</p>
                    </div>
                    <hr />
                    <div class="w-full mt-12">
                        <a-form-item :wrapperCol="{ span: 12 }" label="Cash" :validate-status="cashError ? 'error' : null"
                            :help="cashError ? 'Invalid Amount' : null">
                            <a-input v-model:value="form.cash" />
                        </a-form-item>
                        <p>Change: {{ change }}</p>
                    </div>
                    <div class="w-full">
                        <a-form-item :wrapper-col="{ offset: 4, span: 20 }" class="mb-0">
                            <div class="flex justify-end gap-2">
                                <a-button key="back" @click="handleCancel">Cancel</a-button>
                                <a-button v-if="!isEdit" type="primary" htmlType="submit" :loading="form.processing"
                                    @click="submitOrder">Submit Order</a-button>
                                <a-button v-if="change" type="primary" htmlType="submit" :loading="form.processing"
                                    @click="nextCustomer">Next</a-button>
                            </div>
                        </a-form-item>
                    </div>
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
