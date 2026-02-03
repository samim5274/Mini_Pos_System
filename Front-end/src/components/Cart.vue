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

                <div v-if="errorMsg" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ errorMsg }}
                </div>

                <div v-if="successMsg" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ successMsg }}
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
                            class="border-b hover:bg-slate-50">

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
                                    :disabled="loading || item.quantity <= 1"
                                    @click="decreaseQty(item)"
                                >-</button>

                                <input type="number" class="w-14 h-8 text-center border rounded-lg outline-none focus:ring-2 focus:ring-blue-200"
                                v-model.number="item.quantity" min="1"/>

                                <button
                                    class="w-8 h-8 rounded-lg border hover:bg-slate-100"
                                    :disabled="loading"
                                    @click="increaseQty(item)"
                                >+</button>
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
                    :disabled="loading || cartItems.length === 0" @click="checkout"
                > Checkout </button>

                <p class="text-xs text-slate-500 mt-3">
                By placing your order, you agree to our terms & conditions.
                </p>
            </div>

        </div>
    </div>
</template>

<script>
import api from "@/services/api";

export default {
    data() {
        return {
        loading: false,
        cartItems: [],
        discount: 100,
        vatRate: 0.05,
        qtyTimers: {},
        errorMsg: "",
        successMsg: "",
        cartUpdatedHandler: null,
        };
    },

    computed: {
        subtotal() {
            return this.cartItems.reduce((sum, item) => {
                const price = Number(item.price || 0);
                const qty = Number(item.quantity || 0);
                return sum + price * qty;
            }, 0);
        },
        vat() {
            return this.subtotal * Number(this.vatRate || 0);
        },
        total() {
            return Math.max(0, this.subtotal - Number(this.discount || 0) + this.vat);
        },
    },

    mounted() {
        this.fetchCartItem();
        this.cartUpdatedHandler = () => this.fetchCartItem();
        window.addEventListener("cart-updated", this.cartUpdatedHandler);
    },

    beforeUnmount() {
        window.removeEventListener("cart-updated", this.cartUpdatedHandler);
        Object.values(this.qtyTimers).forEach((t) => clearTimeout(t));
    },

    methods: {
        format(n) {
            return Number(n || 0).toLocaleString("en-US");
        },

        async fetchCartItem() {
            this.loading = true;
            this.errorMsg = "";

            try {
                const reg = localStorage.getItem("cart_reg");

                const res = await api.post("/cart-view", { reg });

                const data = res.data;

                // ✅ backend might return reg (new or existing)
                if (data?.reg) {
                localStorage.setItem("cart_reg", data.reg);
                }

                this.cartItems = Array.isArray(data?.data) ? data.data : [];
            } catch (err) {
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);
                this.cartItems = [];
            } finally {
                this.loading = false;
            }
        },

        // ✅ Qty update (debounced)
        increaseQty(item) {
            item.quantity = Math.max(1, Number(item.quantity || 1) + 1);
            this.queueQtyUpdate(item);
        },

        decreaseQty(item) {
            item.quantity = Math.max(1, Number(item.quantity || 1) - 1);
            this.queueQtyUpdate(item);
        },

        queueQtyUpdate(item) {
            const key = `${item.reg}_${item.product_id}`;

            if (this.qtyTimers[key]) clearTimeout(this.qtyTimers[key]);

            this.qtyTimers[key] = setTimeout(() => {
                this.updateQty(item);
            }, 400);
        },

        async updateQty(item) {
            try {
                await api.post(`/cart-qty-update/${item.reg}/${item.product_id}`, {
                    quantity: Number(item.quantity),
                });
            } catch (err) {
                await this.fetchCartItem();
                const msg = err?.response?.data?.message || "Out of stock.";
                
                // rollback UI by refetch
                this.errorMsg = msg;
                setTimeout(() => (this.errorMsg = ""), 1000);
            }
        },

        // ✅ Remove item
        async remove(item) {
            this.loading = true;
            this.errorMsg = "";

            try {
                await api.post(`/remove-to-cart/${item.reg}/${item.product_id}`);

                await this.fetchCartItem();

                // ✅ optional: cart empty হলে reg clear
                if (this.cartItems.length === 0) {
                localStorage.removeItem("cart_reg");
                }
            } catch (err) {
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);
                this.errorMsg = err?.response?.data?.message || "Remove failed!";
            } finally {
                this.loading = false;
            }
        },

        // ✅ Checkout
        async checkout() {
            this.loading = true;
            this.errorMsg = "";
            this.successMsg = "";

            try {
                const reg = localStorage.getItem("cart_reg");

                if (!reg || this.cartItems.length === 0) {
                    this.errorMsg = "Cart is empty.";
                    return;
                }

                const res = await api.post("/confirm-order", {
                    reg,
                    discount: Number(this.discount || 0),
                    vat: Number(this.vat || 0),
                    total: Number(this.total || 0),
                });

                this.successMsg = res.data?.message || "Order confirmed";

                // ✅ empty UI + remove reg
                this.cartItems = [];
                localStorage.removeItem("cart_reg");

                // ✅ notify others (products page count etc. if you listen there)
                window.dispatchEvent(new Event("cart-updated"));

                setTimeout(() => (this.successMsg = ""), 1000);
            } catch (err) {
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);

                this.errorMsg = err?.response?.data?.message || err?.response?.data?.error || "Checkout failed!";

                setTimeout(() => (this.errorMsg = ""), 1500);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
