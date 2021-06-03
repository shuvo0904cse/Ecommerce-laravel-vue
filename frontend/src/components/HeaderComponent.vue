<template>
  <div>
    <router-link to="/">Dashboard</router-link> |
    <router-link to="/user">Users</router-link> |
    <router-link to="/product">Products</router-link> |
    <a href="#" @click="logout">Hello {{ user.name }}, Log Out</a>
  </div>
</template>

<script>
import {AUTH_LOGOUT} from "../store/actions/auth";
import {PROFILE_REQUEST} from "../store/actions/profile";

export default {
  name: 'Header',
  mounted() {
   this.profile();
  },
  computed:{
    user(){
      return this.$store.getters.getProfile;
    }
  },
  methods: {
    logout: function() {
      this.$store.dispatch(AUTH_LOGOUT).then(() => this.$router.push({name: "Login"}));
    },
    profile: function() {
      return this.$store.dispatch(PROFILE_REQUEST)
    },
  },

}
</script>
