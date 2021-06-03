<template>
  <div>
    <b-container>
      <Header/>
      <b-row>
        <h1>Dashboard</h1>
        <h3>
          Full Name: {{ user.name }} <br>
          Email: {{ user.email }} <br>
          Role: {{ user.role }} <br>
        </h3>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import Header from "../components/HeaderComponent";
export default {
  name: "Dashboard",
  components:{
    Header
  },
  computed:{
    user(){
      return this.$store.getters.getProfile;
    }
  }
};
</script>