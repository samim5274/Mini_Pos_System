<template>
    <div class="min-h-screen bg-slate-100 p-6">
        <div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">

        <h1 class="text-xl font-bold mb-4">Product Details</h1>

        <div v-if="loading" class="text-center py-6">
            Loading...
        </div>

        <div v-else-if="product">

            <div class="space-y-3 text-sm">

            <div><b>Name:</b> {{ product.name }}</div>
            <div><b>SKU:</b> {{ product.sku }}</div>
            <div><b>Price:</b> {{ product.price }}</div>
            <div><b>Stock:</b> {{ product.stock_quantity }}</div>
            <div><b>Low Stock Limit:</b> {{ product.low_stock_threshold }}</div>

            </div>

            <router-link
            to="/products"
            class="inline-block mt-6 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 me-2"
            >
            ← Back
            </router-link>

        </div>

        <div v-else class="text-center text-gray-500">
            Product not found
        </div>

        </div>
    </div>
</template>

<script>
import api from '@/services/api'

export default {
    data() {
        return {
            product: null,
            loading: false,
        };
    },

    mounted(){
        this.fetchProduct();
    },

    methods: {
        async fetchProduct(){
            this.loading = true;

            try{
                const id = this.$route.params.id;
                const res = await api.get(`/products/${id}`);
                this.product = res.data?.data ?? res.data;
            } catch (err) {
                console.log(err);
            } finally {
                this.loading = false;
            }
        }
    },
}
</script>

<style>

</style>