import { createRouter, createWebHistory } from "vue-router";

import Login from "../components/UserLogin.vue";
import Register from "../components/UserRegister.vue";
import Users from "../components/Users.vue";
import Products from "../components/Products.vue";
import ProductDetails from "../components/ProductDetials.vue";
import Cart from "../components/Cart.vue";

const routes = [
  { path: "/", component: Login },
  { path: "/register", component: Register },
  { path: "/users", component: Users },
  { path: "/products", component: Products },
  { path: "/products/:id", component: ProductDetails },
  { path: "/cart", component: Cart},
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
