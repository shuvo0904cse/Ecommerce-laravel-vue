<template>
  <div>
    <b-container>
      <Header/>

      <b-row>
        <h2>User</h2>
      </b-row>
      <b-row>
        <!-- Start user lists -->
        <b-col cols="8">
          <b-row>
            <b-col cols="6">
              <b-form @submit="userSearch">
                <b-input-group prepend="Search">
                  <b-form-input
                      v-model="search"
                      debounce="500"
                      placeholder="Enter your keywords"
                  ></b-form-input>
                </b-input-group>
              </b-form>
            </b-col>
            <b-col cols="2" offset-md="2">
              Select Rule
            </b-col>
            <b-col cols="2">
              <b-form-select
                  @change="getSelectedRole"
                  id="role-input"
                  name="role-input"
                  v-model="selected_role"
                  :options="roles"
                  required
              ></b-form-select>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12">
              <b-table hover head-variant="dark"
                       id="pages-table"
                       :items="users"
                       :fields="fields">
                <template #cell(actions)="data">
                  <button type="button" class="btn btn-dark btn-sm" @click="editUser(data.item)"
                          :ref="'btn' + data.index">Update
                  </button> &nbsp;
                  <button type="button" class="btn btn-danger btn-sm" @click="deleteUser(data.item)"
                          :ref="'btn' + data.index">Delete
                  </button>
                </template>
              </b-table>

              <b-pagination size="md" :total-rows="total" v-model="current_page" :per-page="per_page"
                            @input="getPaginationData()"></b-pagination>

              Total {{ total }}
            </b-col>
          </b-row>
        </b-col>
        <!-- End user lists -->

        <!-- Start Add or edit User form -->
        <b-col cols="4">
          <h3> {{ save_update_title }} User</h3>

          <div v-if="success != ''">
            <b-alert show variant="success"> {{ success }}</b-alert>
          </div>
          <div v-if="error != ''">
            <b-alert show variant="danger"> {{ error }}</b-alert>
          </div>
          <form ref="form" @submit.stop.prevent="handleSubmit">
            <b-form-group label="Name" label-for="name-input">
              <b-form-input id="name-input" name="name-input" v-model="form.name" type="text" required></b-form-input>
              <ErrorField :errors="errors" :column="'name'"/>
            </b-form-group>

            <b-form-group label="Email Address" label-for="email-input">
              <b-form-input id="email-input" name="email-input" v-model="form.email" type="email"
                            required></b-form-input>
              <ErrorField :errors="errors" :column="'email'"/>
            </b-form-group>

            <b-form-group label="Password" label-for="password-input">
              <b-form-input id="password-input" name="password-input" v-model="form.password"
                            type="password"></b-form-input>
              <ErrorField :errors="errors" :column="'password'"/>
            </b-form-group>

            <b-form-group label="Photo" label-for="photo-input">
              <b-form-file @change="onChangeFile" name="photo-input" plain></b-form-file>
              <ErrorField :errors="errors" :column="'photo'"/>
            </b-form-group>

            <b-form-group label="Role" label-for="role-input">
              <b-form-select
                  id="role-input"
                  name="role-input"
                  v-model="form.role"
                  :options="roles"
                  required
              ></b-form-select>
              <ErrorField :errors="errors" :column="'role'"/>
            </b-form-group>
            <br>
            <b-button type="submit" variant="primary">{{ action }}</b-button> &nbsp;
            <b-button class="ml-2" @click="resetForm()">Reset</b-button>
          </form>
        </b-col>
        <!-- End Add or edit User form -->

      </b-row>
    </b-container>
  </div>
</template>

<script>
import ErrorField from "../components/ErrorFieldComponent";
import Header from "../components/HeaderComponent";
import {
  USER_SEARCH,
  UPDATE_USER_SEARCH,
  USER_CURRENT_PAGE,
  UPDATE_USER_CURRENT_PAGE,
  USER_SELECTED_ROLE,
  UPDATE_USER_SELECTED_ROLE,
  USER_STORE,
  USER_EDIT,
  USER_ADD,
  USER_UPDATE,
  USER_DELETE
} from "../store/actions/user";

export default {
  name: "UserList",
  data(){
    return {
      form: {
        id: "",
        name: "",
        email: "",
        password: "",
        photo: "",
        role: ""
      },
    }
  },
  components: {
    Header, ErrorField
  },
  computed: {
    search: {
      get() {
        return this.$store.state.search;
      },
      set(search) {
        this.$store.commit(UPDATE_USER_SEARCH, {search});
      },
    },
    current_page: {
      get() {
        return this.$store.state.current_page;
      },
      set(current_page) {
        this.$store.commit(UPDATE_USER_CURRENT_PAGE, {current_page});
      },
    },
    selected_role: {
      get() {
        return this.$store.state.selected_role;
      },
      set(selected_role) {
        this.$store.commit(UPDATE_USER_SELECTED_ROLE, {selected_role});
      },
    },
    save_update_title() {
      return this.$store.getters.user_save_update_title
    },
    users() {
      return this.$store.getters.users
    },
    fields() {
      return this.$store.getters.fields
    },
    roles() {
      return this.$store.getters.roles
    },
    action() {
      return this.$store.getters.action
    },
    errors() {
      return this.$store.getters.errors
    },
    success() {
      return this.$store.getters.success
    },
    error() {
      return this.$store.getters.error
    },
    total() {
      return this.$store.getters.pagination.total;
    },
    per_page() {
      return this.$store.getters.pagination.per_page;
    },
  },
  methods: {
    onChangeFile(event) {
      var reader = new FileReader()
      reader.readAsDataURL(event.target.files[0])
      reader.onload = ()=> {
        this.form.photo = reader.result;
      };
    },

    userSearch(e) {
      e.preventDefault();
      this.$store.dispatch(USER_SEARCH);
    },

    getPaginationData() {
      this.$store.dispatch(USER_CURRENT_PAGE)
    },

    getSelectedRole() {
      this.$store.dispatch(USER_SELECTED_ROLE)
    },

    editUser(user) {
      this.form.id = user.id;
      this.form.name = user.name;
      this.form.email = user.email;
      this.form.role = user.role;
      this.$store.dispatch(USER_EDIT, {user});
    },

    resetForm() {
      this.form.id = "";
      this.form.name = "";
      this.form.email = "";
      this.form.password = "";
      this.form.role = "";
      this.$store.dispatch(USER_ADD);
    },

    handleSubmit(e) {
      e.preventDefault();
      if (this.$store.getters.action == "SAVE") {
        this.storeData(this.form)
      } else {
        this.updateData(this.form)
      }
    },

    storeData(data){
      this.$store.dispatch(USER_STORE, { data })
          .then((response) => {
            this.resetForm();
            this.$store.dispatch(USER_ADD);
            console.log("response", response);
          });
    },

    updateData(data){
      console.log("dddddddddd", data);
      this.$store.dispatch(USER_UPDATE, { data })
          .then((response) => {
            console.log("response", response);
          //  this.resetForm();
          //  this.$store.dispatch(USER_ADD);
          })
    },

    deleteUser(data) {
      this.$store.dispatch(USER_DELETE, {data})
    },
  }
}
</script>

<style>
.custom-select {
  display: inline-block;
  width: 100%;
  height: calc(1.5em + .75rem + 2px);
  padding: .375rem 1.75rem .375rem .75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  vertical-align: middle;
  background: white;
  border: 1px solid #ced4da;
  border-radius: .25rem;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

.d-block {
  text-align: left;
}

.form-group {
  margin-bottom: 10px;
}
</style>