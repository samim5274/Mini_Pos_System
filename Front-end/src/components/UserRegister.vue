<script>
import api from "@/services/api";

export default{
  data(){
    return {
      form:{
        email: "",
        username: "",
        password: "",
        password_confirmation: "",
        role: "user", // default
      },
      loading: false,
      errorMsg: "",
      successMsg: "",
      fieldErrors: {}, // backend validation errors
    }
  },

  methods: {
    async registerSubmit(){
      
      this.loading = true;
      this.errorMsg = "";
      this.successMsg = "";
      this.fieldErrors = {};

      try{
        // backend send data items
        const payload = {
          name: this.form.name,
          email: this.form.email,
          password: this.form.password,
          password_confirmation: this.form.password_confirmation,
          role: this.form.role,
        };

        const res = await api.post('/register', payload);

        // if token save
        const token = res.data?.token;
        if(token) localStorage.setItem("token", token);

        this.successMsg = "Registration successful!";
        this.form.name = "";
        this.form.email = "";
        this.form.password = "";
        this.form.password_confirmation = "";
        console.log("Register response:", res.data);
        this.$router.push("/users");
      } catch(err){
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
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 pt-7 pb-5 border-b border-slate-200 text-center">
          <h1 class="text-2xl font-bold text-slate-900">Create account</h1>
          <p class="text-sm text-slate-600 mt-1">Register with your details</p>
        </div>

        <!-- Body -->
        <form class="px-6 py-6 space-y-4" @submit.prevent="registerSubmit">
          <!-- Global messages -->
          <div
            v-if="errorMsg"
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
          >
            {{ errorMsg }}
          </div>

          <div
            v-if="successMsg"
            class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
          >
            {{ successMsg }}
          </div>

          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Full name</label>
            <input
              v-model.trim="form.name"
              type="text"
              placeholder="Your name"
              :class="[
                'w-full rounded-xl border px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2',
                fieldErrors.name
                  ? 'border-red-300 focus:ring-red-300'
                  : 'border-slate-300 focus:ring-blue-500'
              ]"
            />
            <p v-if="fieldErrors.name" class="mt-1 text-xs text-red-600">
              {{ fieldErrors.name[0] }}
            </p>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input
              v-model.trim="form.email"
              type="email"
              placeholder="you@example.com"
              :class="[
                'w-full rounded-xl border px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2',
                fieldErrors.email
                  ? 'border-red-300 focus:ring-red-300'
                  : 'border-slate-300 focus:ring-blue-500'
              ]"
            />
            <p v-if="fieldErrors.email" class="mt-1 text-xs text-red-600">
              {{ fieldErrors.email[0] }}
            </p>
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
            <select
              v-model="form.role"
              :class="[
                'w-full rounded-xl border bg-white px-4 py-3 text-slate-900 focus:outline-none focus:ring-2',
                fieldErrors.role
                  ? 'border-red-300 focus:ring-red-300'
                  : 'border-slate-300 focus:ring-blue-500'
              ]"
            >
              <option value="user">User</option>
              <option value="staff">Staff</option>
              <option value="owner">Owner</option>
              <option value="admin">Admin</option>
              <option value="teacher">Teacher</option>
              <option value="student">Student</option>
            </select>
            <p v-if="fieldErrors.role" class="mt-1 text-xs text-red-600">
              {{ fieldErrors.role[0] }}
            </p>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              placeholder="Minimum 6 characters"
              :class="[
                'w-full rounded-xl border px-4 py-3 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2',
                fieldErrors.password
                  ? 'border-red-300 focus:ring-red-300'
                  : 'border-slate-300 focus:ring-blue-500'
              ]"
            />
            <p v-if="fieldErrors.password" class="mt-1 text-xs text-red-600">
              {{ fieldErrors.password[0] }}
            </p>
          </div>

          <!-- Confirm password -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              placeholder="Re-type password"
              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 placeholder-slate-400
                     focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full rounded-xl bg-blue-600 py-3 font-semibold text-white shadow-sm
                   hover:bg-blue-700 active:bg-blue-800 transition disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {{ loading ? "Creating..." : "Create account" }}
          </button>

          <!-- Footer -->
          <p class="text-center text-sm text-slate-600 pt-2">
            Already have an account?
            <router-link to="/" class="text-blue-600 hover:text-blue-700 hover:underline font-medium">Sign in</router-link>
          </p>
          <p class="text-center text-sm text-slate-600 pt-3">
            View all users?
            <router-link
              to="/users"
              class="text-blue-600 font-semibold hover:underline ml-1"
            >
              Users List →
            </router-link>
          </p>

        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
