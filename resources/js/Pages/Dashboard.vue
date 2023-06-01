<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue'
import axios from 'axios'

import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/en'
dayjs.extend(relativeTime)



const props = defineProps({
    todaySales: '',
    weekSales: '',
    monthSales: '',
    transactions: '',
    searchProps: ''
})

const current = ref(props.transactions.current_page)
const pageSize = ref(props.transactions.per_page)
const search = ref('')

const pagination = computed(() => ({
  total: props.transactions.total,
  current: current.value,
  pageSize: pageSize.value,
  showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
  showSizeChanger: false
}))


const columns = [
    {
        title: 'Id',
        dataIndex: 'id',
        key: 'id',
    },
    {
        title: 'Transaction Number',
        dataIndex: 'transaction_number',
        key: 'name',
    },
    {
        title: 'Cash',
        dataIndex: 'cash',
        key: 'name',
    },
    {
        title: 'Change',
        dataIndex: 'change',
        key: 'name',
    },
    {
        title: 'Total Amount',
        dataIndex: 'total_amount',
        key: 'total_amount',
    },
    {
        title: 'Date',
        dataIndex: 'created_at',
        key: 'created_at',
    },
]

const handleTableChange = (event) => {
  current.value = event.current
  pageSize.value = event.pageSize

  router.get(
    route('dashboard'),
    {
    from_date: search.value[0].toISOString() || undefined,
    to_date: search.value[1].toISOString() || undefined,
      page: event.current
    },
    {
      replace: true,
      preserveState: true
    }
  )
}

const handleSearch = () => {
    console.log(search.value[0].toISOString())
    router.get(route('dashboard'), {
        from_date: search.value[0].toISOString(),
        to_date: search.value[1].toISOString(),
    })
}

const handleClear = () => {
    router.get(route('dashboard'))
}

const generateReport = () => {
    // router.post('generate-report', {
    //     from_date: search.value ? search.value[0].toISOString() : undefined,
    //     to_date: search.value ? search.value[1].toISOString() : undefined,
    // })
    window.open(`/generate-report?from_date=${props.searchProps.from_date}&to_date=${props.searchProps.to_date}`, '_blank')
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-2">
            <div class="grid grid-cols-3 gap-3">
            <div class="w-full mx-auto">
                <div class="bg-blue-200 py-2 rounded-md ">
                    <div class="flex">
                      <div>
                      <h2 class="text-base md:text-2xl lg:text-5xl px-2 whitespace-no-wrap text-gray-600">Today</h2>
                      <h2 class="ltext-base md:text-2xl lg:text-5xl px-4 py-2 lg:px-8 text-gray-600">₱ {{ todaySales.toLocaleString("en-US") }}</h2>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="w-full mx-auto">
                <div class="bg-green-200 py-2 rounded-md ">
                    <div class="flex">
                      <div>
                      <h2 class="text-base md:text-2xl lg:text-5xl px-2 whitespace-no-wrap text-gray-600">Week</h2>
                      <h2 class="ltext-base md:text-2xl lg:text-5xl px-4 py-2 lg:px-8 text-gray-600">₱ {{ weekSales.toLocaleString("en-US") }}</h2>
                      </div>
                     
                    </div>
                  </div>
            </div>
            <div class="w-full mx-auto">
                <div class="bg-red-200 py-2 rounded-md ">
                    <div class="flex">
                      <div>
                      <h2 class="text-base md:text-2xl lg:text-5xl px-2 whitespace-no-wrap text-gray-600">Month</h2>
                      <h2 class="ltext-base md:text-2xl lg:text-5xl px-4 py-2 lg:px-8 text-gray-600">₱ {{ monthSales.toLocaleString("en-US") }}</h2>
                      </div>
                    
                    </div>
                  </div>
            </div>
        </div>
        <div class="mt-4">
            <hr>
            <div class="flex justify-between mt-2 mb-2">
                <a-button type="primary" @click="generateReport">Generate Report</a-button>
            <div class="flex justify-end gap-4 ">
                <a-range-picker v-model:value="search" />
                <a-button v-if="searchProps" type="primary" @click="handleClear">Clear</a-button>
                <a-button type="primary" @click="handleSearch">Search</a-button>
            </div>
        </div>
            <a-table :dataSource="transactions.data" :columns="columns" @change="handleTableChange" :pagination="{ ...pagination }">
                <template #bodyCell="{column, record}">
                    <template v-if="column.dataIndex === 'created_at'">
                        {{ dayjs(record.created_at).format('MMM DD YYYY h:mm A') }}
                    </template>
                </template>
            </a-table>
        </div>
    </div>
    </AuthenticatedLayout>
</template>
