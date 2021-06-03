import Vue from 'vue'
import VueRouter from 'vue-router'
import {ifAuthenticated, ifNotAuthenticated} from "../utils/guard";
import Dashboard from "../views/Dashboard";

Vue.use(VueRouter)


const routes = [
  { path: '/', name: 'Dashboard', component: Dashboard, beforeEnter: ifAuthenticated},
  { path: '/product',  name: 'Product',  component: () => import(/* webpackChunkName: "about" */ '../views/Product'), beforeEnter: ifAuthenticated},
  { path: '/user',  name: 'User',  component: () => import(/* webpackChunkName: "about" */ '../views/User'), beforeEnter: ifAuthenticated},
  { path: '/login',  name: '/Login',  component: () => import(/* webpackChunkName: "about" */ '../views/Login'), beforeEnter: ifNotAuthenticated}
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router
