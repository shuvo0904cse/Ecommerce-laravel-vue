import Vue from 'vue'
import VueRouter from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
Vue.use(VueRouter)

const routes = [
  { path: '/', name: 'Dashboard', component: Dashboard },
  { path: '/user', name: 'User', component: () => import(/* webpackChunkName: "about" */ '../views/User.vue')},
  { path: '/product', name: 'Product', component: () => import(/* webpackChunkName: "about" */ '../views/Product.vue') },
  { path: '/login', name: 'Login', component: () => import(/* webpackChunkName: "about" */ '../views/Login.vue') },
  { path: '*', redirect: '/' }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

//page authentication
router.beforeEach((to, from, next) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = ['/login'];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user');
  if (authRequired && !loggedIn) {
    return next('/login');
  }
  next();
})

export default router
