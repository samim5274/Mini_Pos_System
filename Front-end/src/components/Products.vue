<template>
    <div class="min-h-screen bg-slate-100 p-6">
        <div class="max-w-5xl mx-auto bg-white shadow rounded-xl overflow-hidden">

        <div class="flex justify-between items-center  mb-6 font-bold p-4 border-b">
            <h1 class="text-xl font-bold ">Products List</h1>

            <div>
                <router-link
                to="/users"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 me-2"
                >
                User
                </router-link>
                <router-link
                to="/cart"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                Cart 
                <span>{{ cartCount }}</span>
                </router-link>
            </div>
        </div>

        <div>
            <div v-if="successMsg" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ successMsg }}
            </div>

            <div v-if="loading" class="p-6 text-center">
                Loading...
            </div>

            <table v-else class="w-full text-sm">
                <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">SKU</th>
                    <th class="p-3 text-left">Price</th>
                    <th class="p-3 text-left">Stock</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="(p, i) in products"
                    :key="p.id"
                    class="border-b hover:bg-slate-50">

                    <td class="p-3">{{ i + 1 }}</td>
                    <td class="p-3">
                        <router-link :to="`/products/${p.id}`"
                        class="text-blue-600 hover:underline font-medium">
                        {{ p.name }}
                        </router-link>
                    </td>
                    <td class="p-3">{{ p.sku }}</td>
                    <td class="p-3">{{ p.price }}</td>
                    <td class="p-3">{{ p.stock_quantity }}</td>
                    <td class="p-3">
                        <button 
                        @click="addCart(p.id)" 
                        :disabled="loading || p.stock_quantity <= 0"
                        class="px-3 py-2 rounded-lg text-white"
                        :class="(loading || p.stock_quantity <= 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-800'">
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        </div>
    </div>
</template>

<script>
import api from '@/services/api'

export default {

    data() {
        return {
            products: [],
            loading: false,
            successMsg: "",
            cartCount: 0,
        };
    },

    async mounted(){
        this.fetechProducts(); 
        this.fetchCartCount();       
    },

    methods: {
        async fetechProducts(){
            this.loading = true;

            try{
                const res = await api.get('/products');
                // console.log("Raw: ", res.data);
                this.products = res.data;

                const data = res.data;
                if(Array.isArray(data)) this.products = data;
                else if (Array.isArray(data?.data)) this.products = data.data;
                else if (data) this.products = [data];
                else this.products = [];

            } catch (err){
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);
                console.log("URL:", err?.config?.baseURL + err?.config?.url);
            } finally {
                this.loading = false;
            }
        },

        async addCart(id){

            if (this.loading) return;
            this.loading = true;
            this.successMsg = "";

            try{
                const res = await api.post(`/add-cart/${id}`, { quantity: 1 });
                console.log(res.data);
                const msg = res.data?.message || "Added successfully";
                this.successMsg = msg;
                await this.fetechProducts();
                await this.fetchCartCount();
                setTimeout(() => this.successMsg = "", 800);
            } catch (err){
                const msg = err.response?.data?.message || "Failed to add cart ❌";

                alert(msg);
            } finally {
                this.loading = false;
            }
        },

        async fetchCartCount(){
            try{
                const res = await api.get("/cart-count");
                this.cartCount = res.data?.count || 0;
            } catch (e) {
                console.log(e);
            }
        }
    }
}
</script>

<style>

</style>