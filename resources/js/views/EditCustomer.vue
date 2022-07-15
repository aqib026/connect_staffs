<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import { required } from "vuelidate/lib/validators";

/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader },
  data() {
    return {
      title: "Update Customer",
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "Update Customer",
          active: true
        }
      ],
      showModal: false,
      submitted: false,
      user: {
        name: "",
        business_name: "",
        phone_number: "",
        address:"",
        email: "",
        type: "",
      },
    };
  },
  validations: {
    user: {
      name: { required },
      business_name: { required },
      phone_number: { required },
      email: { required },
      address:{ required }
    },
  },
  methods: {
    getResults() {
      axios
        .get(`/api/user/${this.$route.params.id}`)
        .then(({ data }) => {
          this.user = data.data;
          console.log(this.user);
    });
    },
    /**
     * Modal form submit
     */
    // eslint-disable-next-line no-unused-vars
    handleSubmit(e) {
      this.submitted = true;

      // stop here if form is invalid
      this.$v.$touch();
      if (this.$v.$invalid) {
        console.log('first ');
        return;
      } else {
        this.showModal = false;
        axios
          .post("/api/update/user/" + this.$route.params.id, this.user)
          .then(response => {
            this.$swal("Customer Update Successfully");
            this.$router.push({ path : '/customers' });
          })
          .catch(err => {
              if (err.response.status === 422) {
                  this.errors = [];
                  _.each(err.response.data.errors, error => {
                      _.each(error, e => {
                          this.errors.push(e);
                      });
                  });
              }
          });        
      }
    },
  },
  mounted() {
    console.log(this.$route.params.id);
},
  created() {
    this.getResults();
  }

};
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Basic Information</h4>
                        <p class="card-title-desc">
                            Fill all information below
                        </p>

                        <!-- THIS SECTION IS FOR ERRORS THAT WOULD COME FROM API -->
  
                        <form @submit.prevent="handleSubmit">
                            <div class="row">

                                <div class="col-6">
                                  <div class="mb-3">
                                    <label for="name">Bsuiness Name</label>
                                    <input
                                      id="name"
                                      v-model="user.business_name"
                                      type="text"
                                      class="form-control"
                                      placeholder="Insert Business Name"
                                      :class="{
                                        'is-invalid':
                                          submitted && $v.user.business_name.$error,
                                      }"
                                    />
                                    <div
                                      v-if="
                                        submitted && !$v.user.business_name.required
                                      "
                                      class="invalid-feedback"
                                    >
                                      This value is required.
                                    </div>
                                  </div>
                                </div>

                                <div class="col-6">
                                  <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input
                                      id="name"
                                      v-model="user.name"
                                      type="text"
                                      class="form-control"
                                      placeholder="Name"
                                      :class="{
                                        'is-invalid':
                                          submitted && $v.user.name.$error,
                                      }"
                                    />
                                    <div
                                      v-if="
                                        submitted && !$v.user.name.required
                                      "
                                      class="invalid-feedback"
                                    >
                                      This value is required.
                                    </div>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="mb-3">
                                    <label for="phone_number">Phone Number</label>
                                    <input
                                      id="phone_number"
                                      v-model="user.phone_number"
                                      type="text"
                                      class="form-control"
                                      placeholder="Enter Phonenumber"
                                      :class="{
                                        'is-invalid':
                                          submitted && $v.user.phone_number.$error,
                                      }"
                                    />
                                    <div
                                      v-if="submitted && !$v.user.phone_number.required"
                                      class="invalid-feedback"
                                    >
                                      This value is required.
                                    </div>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input
                                      id="email"
                                      v-model="user.email"
                                      type="email"
                                      class="form-control"
                                      placeholder="Insert email"
                                      :class="{
                                        'is-invalid':
                                          submitted && $v.user.email.$error,
                                      }"
                                    />
                                    <div
                                      v-if="submitted && !$v.user.email.required"
                                      class="invalid-feedback"
                                    >
                                      This value is required.
                                    </div>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="mb-3">
                                    <label for="address">Address</label>
                                    <input
                                      id="address"
                                      v-model="user.address"
                                      type="text"
                                      class="form-control"
                                      placeholder="Insert Address"
                                      :class="{
                                        'is-invalid':
                                          submitted && $v.user.address.$error,
                                      }"
                                    />
                                    <div
                                      v-if="submitted && !$v.user.address.required"
                                      class="invalid-feedback"
                                    >
                                      This value is required.
                                    </div>
                                  </div>
                                </div>



                            </div>

                            <button type="submit"  class="btn btn-primary mr-1">
                                Update Customer Information
                            </button>
                            <router-link v-if="user.type == 'worker'" to="/users" type="reset" class="btn btn-secondary">
                                Cancel
                            </router-link>
                            <router-link v-if="user.type == 'admin'" to="/administrators" type="reset" class="btn btn-secondary">
                                Cancel
                            </router-link>
                            <router-link v-if="user.type == 'customers'" to="/customers" type="reset" class="btn btn-secondary">
                                Cancel
                            </router-link>
                        </form>
                    </div>
                </div>


            </div>
        </div>
  </Layout>
</template>
