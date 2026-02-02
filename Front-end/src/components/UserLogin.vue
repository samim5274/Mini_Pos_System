<template>
  <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">

        <!-- Header -->
        <div class="px-6 pt-7 pb-5 border-b border-slate-200 text-center">
          <h1 class="text-2xl font-bold text-slate-900">User Login</h1>
          <p class="text-sm text-slate-600 mt-1">Sign in to your account</p>
          <div
            v-if="errorMsg"
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
          >
            {{ errorMsg }}
          </div>
        </div>

        <!-- Form -->
        <form class="px-6 py-6 space-y-4" @submit.prevent="loginSubmit">
          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input
            
              type="email"
              
              v-model.trim="form.email"
              placeholder="you@example.com"
              class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="fieldErrors.email" class="mt-1 text-xs text-red-600">
              {{ fieldErrors.email[0] }}
            </p>
          </div>

          <!-- Password -->
          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block text-sm font-medium text-slate-700">Password</label>
              <a href="#" class="text-sm text-blue-600 hover:text-blue-700 hover:underline">
                Forgot password?
              </a>
            </div>
            <input
              type="password"
              v-model.trim="form.password"
              placeholder="••••••••"
              class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="fieldErrors.password" class="mt-1 text-xs text-red-600">
              {{ fieldErrors.password[0] }}
            </p>
          </div>

          <!-- Remember -->
          <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input
                type="checkbox"
                v-model="form.remember"
                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
              />
              Remember me
            </label>

            <span class="text-xs text-slate-500">Secure login</span>
          </div>

          <!-- Button -->
          <button
            type="submit"
            class="w-full rounded-xl bg-blue-600 py-3 font-semibold text-white shadow-sm
                   hover:bg-blue-700 active:bg-blue-800 transition"
          >
            Sign in
          </button>

          <!-- Footer -->
          <p class="text-center text-sm text-slate-600 pt-2">
            Don’t have an account?
            <router-link to="/register" class="text-blue-600 hover:text-blue-700 hover:underline font-medium">
              Create one
            </router-link>
          </p>
        </form>
      </div>

      <p class="text-center text-xs text-slate-500 mt-4">
        SAMIM-Hossen by continuing, you agree to our Terms & Privacy Policy.
      </p>
    </div>
  </div>
</template>

<script>
import api from "@/services/api"
export default {
    data() {
        return {
            form: {
                email: "",
                password: "",
                remember: "",
            },
            loading: false,
            errorMsg: "",
            fieldErrors: {},
        }
    },

    methods: {
        async loginSubmit(){

            this.loading = true;
            this.errorMsg = "";
            this.fieldErrors = {};

            try{
                const payload = {
                    email: this.form.email,
                    password: this.form.password,
                    remember: this.form.remember,
                };

                const res = await api.post('/login', payload);
                const token = res.data?.token;
                if(token) localStorage.setItem("token", token);
                this.$router.push("/users");
            }catch(err){
                if (err.response?.status === 422) {
                    this.fieldErrors = err.response.data?.errors || {};
                    this.errorMsg = "Please fix the highlighted errors.";
                } else {
                    this.errorMsg = err.response?.data?.message || "Something went wrong!";
                }
            } finally {
                this.loading = false;
            }
        }
    },
};
</script>

<style scoped></style>
