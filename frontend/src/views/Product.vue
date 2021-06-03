<template>
  <div>
    <b-container>
      <Header/>

      <b-row>
        <h2>Product</h2>
      </b-row>
      <b-row>
        <!-- Start product lists -->
        <b-col cols="8">
          <b-row>
            <b-col cols="6">
              <b-form @submit="productSearch">
                <b-input-group prepend="Search">
                  <b-form-input
                      v-model="search"
                      debounce="500"
                      placeholder="Enter your keywords"
                  ></b-form-input>
                </b-input-group>
              </b-form>
            </b-col>
          </b-row>

          <b-row>
            <b-col cols="12">
              <b-table hover head-variant="dark"
                       id="pages-table"
                       :items="products"
                       :fields="product_fields">
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
        <!-- End product lists -->

        <!-- Start Add or edit User form -->
        <b-col cols="4">
          <h3> {{ product_save_update_title }} Product</h3>

          <div v-if="product_success != ''">
            <b-alert show variant="product_success"> {{ product_success }}</b-alert>
          </div>
          <div v-if="product_error != ''">
            <b-alert show variant="danger"> {{ product_error }}</b-alert>
          </div>
          <form ref="form" @submit.stop.prevent="handleSubmit">
            <b-form-group label="Title" label-for="title-input">
              <b-form-input id="title-input" name="title-input" v-model="form.title" type="text" required></b-form-input>
              <ErrorField :errors="product_errors" :column="'title'"/>
            </b-form-group>

            <b-form-group label="Code" label-for="code-input">
              <b-form-input id="code-input" name="code-input" v-model="form.code" type="text"
                            required></b-form-input>
              <ErrorField :errors="product_errors" :column="'code'"/>
            </b-form-group>

            <b-form-group label="Description" label-for="description-input">
              <b-form-input id="description-input" name="description-input" v-model="form.description"
                            type="text"></b-form-input>
              <ErrorField :errors="product_errors" :column="'description'"/>
            </b-form-group>

            <b-form-group label="Images" label-for="images-input">
              <b-form-file @change="onChangeFiles" multiple name="images-input" ref="myFileInput" plain></b-form-file>
              <ErrorField :errors="product_errors" :column="'images'"/>
            </b-form-group>

            <div v-show="previewImages != ''" v-for="image in previewImages" :key="image.id">
              <img :src="image.image" class="img img-thumbnail"/>
            </div>

            <br>
            <b-button type="submit" variant="primary">{{ product_action }}</b-button> &nbsp;
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
  PRODUCT_SEARCH,
  UPDATE_PRODUCT_SEARCH,
  PRODUCT_CURRENT_PAGE,
  UPDATE_PRODUCT_CURRENT_PAGE,
  PRODUCT_STORE,
  PRODUCT_EDIT,
  PRODUCT_ADD,
  PRODUCT_UPDATE,
  PRODUCT_DELETE
} from "../store/actions/product";

export default {
  name: "UserList",
  data(){
    return {
      form: {
        id: "",
        title: "",
        code: "",
        description: "",
        images: []
      },
      previewImages: ""
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
        this.$store.commit(UPDATE_PRODUCT_SEARCH, {search});
      },
    },
    current_page: {
      get() {
        return this.$store.state.current_page;
      },
      set(current_page) {
        this.$store.commit(UPDATE_PRODUCT_CURRENT_PAGE, {current_page});
      },
    },
    product_save_update_title() {
      return this.$store.getters.product_save_update_title
    },
    products() {
      return this.$store.getters.products
    },
    product_fields() {
      return this.$store.getters.product_fields
    },
    roles() {
      return this.$store.getters.roles
    },
    product_action() {
      return this.$store.getters.product_action
    },
    product_errors() {
      return this.$store.getters.product_errors
    },
    product_success() {
      return this.$store.getters.product_success
    },
    product_error() {
      return this.$store.getters.product_error
    },
    total() {
      return this.$store.getters.product_pagination.total;
    },
    per_page() {
      return this.$store.getters.product_pagination.per_page;
    },
  },
  methods: {
    onChangeFiles(event) {
      var results = [];
      if(event.target.files.length > 0){
        Array.from(event.target.files).forEach((file) => {
          var reader = new FileReader()
          reader.readAsDataURL(file)
          reader.onload = ()=> {
            results.push(reader.result);
          };
        })
      }
      this.form.images =  results;
    },

    productSearch(e) {
      e.preventDefault();
      this.$store.dispatch(PRODUCT_SEARCH);
    },

    getPaginationData() {
      this.$store.dispatch(PRODUCT_CURRENT_PAGE)
    },

    editUser(product) {
      this.previewImages = "";
      this.form.id = product.id;
      this.form.title = product.title;
      this.form.code = product.code;
      this.form.description = product.description;
      if(product.images.length > 0) this.previewImages = product.images;
      this.$store.dispatch(PRODUCT_EDIT, {product});
    },

    resetForm() {
      this.form.id = "";
      this.form.title = "";
      this.form.code = "";
      this.form.description = "";
      this.form.images = "";
      this.previewImages = "";
      this.$store.dispatch(PRODUCT_ADD);
      this.$refs.myFileInput.value = '';
    },

    handleSubmit(e) {
      e.preventDefault();
      if (this.$store.getters.product_action == "SAVE") {
        this.storeData(this.form)
      } else {
        this.updateData(this.form)
      }
    },

    storeData(data){
      this.$store.dispatch(PRODUCT_STORE, { data })
          .then((response) => {
            this.resetForm();
          });
    },

    updateData(data){
      this.$store.dispatch(PRODUCT_UPDATE, { data })
          .then((response) => {
              this.resetForm();
          })
    },

    deleteUser(data) {
      this.$store.dispatch(PRODUCT_DELETE, {data})
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