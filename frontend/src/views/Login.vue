<template>
  <div>
    <b-container>
      <b-row>
        <b-col md="4" offset-md="4">
          <b-form @submit.stop.prevent="onSubmit">
            <b-alert
                :show="dismissCountDown"
                dismissible
                variant="warning"
                @dismissed="dismissCountDown=0"
                @dismiss-count-down="countDownChanged"
            >
             {{ auth_error }}
            </b-alert>

            <b-form-group label="Email Address" label-for="email-input">
              <b-form-input
                  id="email-input"
                  name="email-input"
                  v-model="email"
                  v-validate="{ required: true, email: true }"
                  :state="validateState('email-input')"
                  aria-describedby="email-live-feedback"
                  data-vv-as="Email Address"
                  type="email"
              ></b-form-input>
              <b-form-invalid-feedback id="email-live-feedback">{{veeErrors.first('email-input') }}</b-form-invalid-feedback>
            </b-form-group>

            <b-form-group label="Password" label-for="password-input">
              <b-form-input
                  id="password-input"
                  name="password-input"
                  v-model="password"
                  v-validate="{ required: true }"
                  :state="validateState('password-input')"
                  aria-describedby="password-live-feedback"
                  data-vv-as="Password"
                  type="password"
              ></b-form-input>
              <b-form-invalid-feedback id="password-live-feedback">{{veeErrors.first('password-input') }}</b-form-invalid-feedback>
            </b-form-group>

            <b-button type="submit" variant="primary">Login</b-button>
            <b-button class="ml-2" @click="resetForm()">Reset</b-button>
          </b-form>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import {AUTH_REQUEST} from "@/store/actions/auth";

export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
      dismissSecs: 5,
      dismissCountDown: 0
    };
  },
  computed:{
    auth_error(){
      return this.$store.getters.auth_error;
    }
  },
  methods: {
    validateState(ref) {
      if (this.veeFields[ref] && (this.veeFields[ref].dirty || this.veeFields[ref].validated)) {
        return !this.veeErrors.has(ref);
      }
      return null;
    },
    resetForm() {
      this.email = "";
      this.password = "";
      this.$nextTick(() => {
        this.$validator.reset();
      });
    },
    onSubmit() {
      this.$validator.validateAll().then(result => {
        if (result) {
          const {email, password} = this;
          this.$store.dispatch(AUTH_REQUEST, {email, password})
              .then((response) => {
                this.$router.push({name: 'Dashboard'});
              })
              .catch(error => {
                this.dismissCountDown = this.dismissSecs
              });
        }
      });
    },
    countDownChanged(dismissCountDown) {
      this.dismissCountDown = dismissCountDown
    },
  }
};
</script>