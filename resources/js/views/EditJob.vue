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
      title: "Users",
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "Users",
          active: true
        }
      ],
      customers:"",
      showModal: false,
      submitted: false,
      job: {
        job_name: { required },
        customer_id: { required },
        start_date: { required }
      },
    };
  },
  validations: {
     job: {
      job_name: { required },
      customer_id: { required },
      start_date: { required }
    },
 },
  methods: {
    getResults() {
      axios
        .get(`/api/job/${this.$route.params.id}`)
        .then(({ data }) => {
          this.job = data.data;
          console.log(this.job);
    });
    },
    getCustomers(){
      if(this.customers.length < 1){
        axios
          .get("/api/get/customers")
          .then(response => {
              this.customers = response.data;
                        });      
      }
      this.showModal = true;
    },
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
          .post("/api/update/job/" + this.$route.params.id, this.job)
          .then(response => {
            this.$swal(response.data.message);
            this.$router.push({ path : '/jobs' });
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
    this.getCustomers();
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
                        <h4 class="card-title">Job Information</h4>
                        <p class="card-title-desc">
                            Update Job Information
                        </p>

                        <!-- THIS SECTION IS FOR ERRORS THAT WOULD COME FROM API -->
  
                  <form @submit.prevent="handleSubmit">
                      <div class="row">

                        <div class="col-6">
                          <div class="mb-3">
                            <label for="name">Job Name</label>
                            <input
                              id="job_name"
                              v-model="job.job_name"
                              type="text"
                              class="form-control"
                              placeholder="Enter Job Name"
                              :class="{
                                'is-invalid':
                                  submitted && $v.job.job_name.$error,
                              }"
                            />
                            <div
                              v-if="
                                submitted && !$v.job.job_name.required
                              "
                              class="invalid-feedback"
                            >
                              This value is required.
                            </div>
                          </div>
                        </div>

                        <div class="col-6">
                          <div class="mb-3">
                            <label for="name">Customer</label>
                            <select class="form-control" :class="{
                                'is-invalid':
                                  submitted && $v.job.customer_id.$error,
                              }" id="customer_id" v-model="job.customer_id">
                              <option value="">Select a Customer</option>
                              <template v-for="(customer , id) in customers" >
                                <option  :value="id" :key="id">{{customer}}</option>                              
                              </template>
                            </select>
                            <div
                              v-if="
                                submitted && !$v.job.customer_id.required
                              "
                              class="invalid-feedback"
                            >
                              This value is required.
                            </div>
                          </div>
                        </div>

                        <div class="col-6">
                          <div class="mb-3">
                            <label for="phone">Start Date</label>
                            <input
                              id="start_date"
                              v-model="job.start_date"
                              type="date"
                              class="form-control"
                              placeholder="Enter Start Date"
                              :class="{
                                'is-invalid':
                                  submitted && $v.job.start_date.$error,
                              }"
                            />
                            <div
                              v-if="submitted && !$v.job.start_date.required"
                              class="invalid-feedback"
                            >
                              This value is required.
                            </div>
                          </div>
                        </div>

                        <div class="col-6">
                          <div class="mb-3">
                            <label for="phone">End Date</label>
                            <input
                              id="end_date"
                              v-model="job.end_date"
                              type="date"
                              class="form-control"
                              placeholder="Enter End Date"                              
                            />
                            
                          </div>
                        </div>
                    


                      </div>

                      <button type="submit"  class="btn btn-primary mr-1">
                          Save Changes
                      </button>
                      <router-link to="/jobs" type="reset" class="btn btn-secondary">
                          Cancel
                      </router-link>
                   </form>
                    </div>
                </div>


            </div>
        </div>
  </Layout>
</template>
