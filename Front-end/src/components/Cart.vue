<template>
    <div class="min-h-screen bg-slate-100 p-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: Cart Items -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
            <h1 class="text-xl font-bold">Your Cart</h1>

            <button
                class="text-sm text-red-600 hover:underline"
                :disabled="loading || cartItems.length === 0">
                Clear Cart
            </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="p-8 text-center text-slate-600">
            Loading cart...
            </div>

            <!-- Empty -->
            <div v-else-if="cartItems.length === 0" class="p-10 text-center">
                <div class="text-4xl mb-2">🛒</div>
                <p class="text-slate-700 font-medium">Your cart is empty</p>
                <p class="text-sm text-slate-500 mt-1">Add products to see them here.</p>

                <router-link
                    to="/products"
                    class="inline-block mt-5 px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Browse Products
                </router-link>
            </div>

            <!-- Cart Table -->
            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-center w-36">Qty</th>
                        <th class="p-3 text-right w-28">Price</th>
                        <th class="p-3 text-right w-32">Total</th>
                        <th class="p-3 text-right w-20">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr
                        v-for="item in cartItems"
                        :key="item.id"
                        class="border-b hover:bg-slate-50"
                    >
                        <!-- Product -->
                        <td class="p-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500">
                            <!-- image থাকলে এখানে <img :src="item.image" class="w-full h-full object-cover" /> -->
                            IMG
                            </div>

                            <div>
                            <div class="font-semibold text-slate-800 leading-5">
                                {{ item.product.name }}
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                SKU: {{ item.product.sku || '—' }}
                            </div>
                            </div>
                        </div>
                        </td>

                        <!-- Qty -->
                        <td class="p-3">
                        <div class="flex items-center justify-center gap-2">
                            <button
                                class="w-8 h-8 rounded-lg border hover:bg-slate-100 disabled:opacity-50"
                                :disabled="item.quantity <= 1">
                                -
                            </button>

                            <input
                            type="number"
                            class="w-14 h-8 text-center border rounded-lg outline-none focus:ring-2 focus:ring-blue-200"
                            v-model.number="item.quantity"
                            min="1"/>

                            <button
                                class="w-8 h-8 rounded-lg border hover:bg-slate-100">
                                +
                            </button>
                        </div>
                        </td>

                        <!-- Unit price -->
                        <td class="p-3 text-right">
                        ৳ {{ format(item.price) }}
                        </td>

                        <!-- Total -->
                        <td class="p-3 text-right font-semibold">
                        ৳ {{ format(item.price * item.quantity) }}
                        </td>

                        <!-- Remove -->
                        <td class="p-3 text-right">
                            <button
                                class="px-3 py-1.5 text-xs rounded-lg bg-red-50 text-red-700 hover:bg-red-100"
                                :disabled="loading"
                                @click="remove(item)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- Footer actions -->
                <div class="p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <router-link
                    to="/products"
                    class="text-sm text-blue-600 hover:underline">
                    ← Continue shopping
                    </router-link>
                </div>
            </div>
        </div>

            <!-- Right: Summary -->
            <div class="bg-white rounded-xl shadow p-5 h-fit">
                <h2 class="text-lg font-bold">Order Summary</h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">৳ {{ format(subtotal) }}/-</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Discount</span>
                        <span class="font-semibold">৳ {{ format(discount) }}/-</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">VAT</span>
                        <span class="font-semibold">৳ {{ format(vat) }}/-</span>
                    </div>

                    <div class="border-t pt-3 flex items-center justify-between">
                        <span class="text-slate-700 font-bold">Total</span>
                        <span class="text-slate-900 font-bold text-lg">৳ {{ format(total) }}/-</span>
                    </div>
                </div>

                <button
                class="w-full mt-5 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 disabled:opacity-50"
                :disabled="cartItems.length === 0"
                >
                Checkout
                </button>

                <p class="text-xs text-slate-500 mt-3">
                By placing your order, you agree to our terms & conditions.
                </p>
            </div>

        </div>
    </div>
</template>

<script>
import api from '@/services/api'

export default {
    data() {
        return {
            loading: false,
            cartItems: [],
            discount: 100,   // fixed discount (৳)
            vatRate: 0.05, // example: 0.05 মানে 5%
        };
    },

    computed: {
        subtotal() {
            return this.cartItems.reduce((sum, item) => {
                const price = Number(item.price || 0);
                const qty = Number(item.quantity || 0);
                return sum + (price * qty);
            }, 0);
        },
        vat(){
            return this.subtotal * Number(this.vatRate || 0);
        },
        total() {
            return Math.max(0, this.subtotal - Number(this.discount || 0) + this.vat);
        },
    },

    async mounted(){
        this.fetchCartItem();        
    },

    methods: {
        format(n) {
            return Number(n || 0).toLocaleString("en-US");
        },
            
        async fetchCartItem(){
            this.loading = true;
            try{
                const res = await api.post("/cart-view");
                const data = res.data;
                this.cartItems = Array.isArray(data) ? data : Array.isArray(data?.data) ? data.data : [];
            } catch (err){
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);
                console.log("URL:", err?.config?.baseURL + err?.config?.url);
            } finally {
                this.loading = false;
            }
        },

        async remove(item){
            this.loading = true;
            try{
                const res = await api.post(`/remove-to-cart/${item.reg}/${item.product_id}`);
                await this.fetchCartItem();
            } catch (err){
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);
                console.log("URL:", err?.config?.baseURL + err?.config?.url);
            } finally {
                this.loading = false;
            }
        }
    },

};
</script>
