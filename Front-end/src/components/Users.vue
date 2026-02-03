<template>
    <div class="min-h-screen bg-slate-100 p-6">
        <div class="max-w-6xl mx-auto">

            <!-- header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">All Users</h1>

                <div>
                    <router-link
                    to="/"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 me-2"
                    >
                    Login
                    </router-link>
                    <router-link
                    to="/products"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                    >
                    Products
                    </router-link>
                </div>
            </div>

            <!-- table -->
            <div class="bg-white rounded-xl shadow border overflow-hidden">

                <div v-if="loading" class="p-6 text-center">Loading...</div>

                <table v-else class="w-full text-sm">
                <thead class="bg-slate-50 border-b">
                    <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                    v-for="(u, i) in users"
                    :key="u.id"
                    class="border-b hover:bg-slate-50"
                    >
                    <td class="p-3">{{ i + 1 }}</td>
                    <td class="p-3 font-medium">{{ u.name }}</td>
                    <td class="p-3">{{ u.email }}</td>
                    <td class="p-3">{{ u.role }}</td>
                    </tr>
                </tbody>
                </table>

            </div>
        </div>
    </div>
</template>

<script>
import api from "@/services/api";

export default {
    data() {
        return {
            users: [],
            loading: false,
        };
    },

    async mounted(){
        this.fetchUsers();
    },

    methods: {
        async fetchUsers() {
            this.loading = true;

            try {
                const res = await api.get("/user");
                console.log("RAW:", res.data);

                const data = res.data;

                if (Array.isArray(data)) this.users = data;
                else if (Array.isArray(data?.data)) this.users = data.data;
                else if (data) this.users = [data];
                else this.users = [];
            } catch (err) {
                console.log("STATUS:", err?.response?.status);
                console.log("DATA:", err?.response?.data);
                console.log("URL:", err?.config?.baseURL + err?.config?.url);
            } finally {
                this.loading = false;
            }
        }

    }
}
</script>

<style>

</style>