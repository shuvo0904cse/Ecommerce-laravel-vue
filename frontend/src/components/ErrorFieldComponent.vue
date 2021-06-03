<template>
  <div v-if="errors != ''">
    <div v-for="(error, key) in errors" :key="error.id">
      <div class="d-block invalid-feedback" v-if="key == column">{{ error[0] }}</div>
    </div>
  </div>
</template>

<script>
import {AUTH_LOGOUT} from "../store/actions/auth";
import {USER_REQUEST} from "../store/actions/user";

export default {
  name: 'ErrorField',
  props: ['errors', 'column']
}
</script>
