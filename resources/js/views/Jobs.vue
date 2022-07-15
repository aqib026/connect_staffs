<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import { required } from "vuelidate/lib/validators";
import pagination from 'laravel-vue-pagination'
/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader , pagination },
  data() {
    return {
      jobsData: {
                type:Object,
                default:null
            },
      title: "Published Jobs",
      search:"",
      customers:"",
      jobsArr: [],
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "Jobs",
          active: true
        }
      ],
      showModal: false,
      submitted: false,
      job: {
        job_name: "",
        customer_id: "",
        start_date: "",
        end_date: ""
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
    //
    edit_job_url(id){
      //router.push({ name: 'user', params: { id } });
      return '/job/'+id;
    },
    unPublishJobs(){
      if(this.jobsArr.length > 0){
        let n = confirm('are you sure you want to publish all these jobs ?');
        if(n){
        axios
          .post("/api/unpublish/multijobs", this.jobsArr)
          .then(response => {
              this.$swal('Jobs Unpublished Successfully');
              this.getResults();
              this.jobsArr = [];
          })

        }
      }else{
          this.$swal('Please select Job to Publish !');
      }
    },
    publishJobs(){
      if(this.jobsArr.length > 0){
        let n = confirm('are you sure you want to publish all these jobs ?');
        if(n){
        axios
          .post("/api/publish/multijobs", this.jobsArr)
          .then(response => {
              this.$swal('Jobs Published Successfully');              
              this.getResults();
              this.jobsArr = [];
          })

        }
      }else{
          this.$swal('Please select Job to Publish !');
      }
    },
    updateJobsArr(e){
       if (e.target.checked){
        this.jobsArr.push(e.target.value);
       }else{
        const index = this.jobsArr.indexOf(e.target.value);
        if (index > -1) {
          this.jobsArr.splice(index, 1);
        }         
       }
      console.log(this.jobsArr);
    },

    async getResults(page = 1) {
      if(this.search.length > 2 || this.search == ''){
        axios
          .get(`/api/jobs?page=${page}&search=${this.search}`)
          .then(({ data }) => {this.jobsData = data;});
      }
    },
    deleteJob(user_id){
      let n = confirm('are you sure you want to delete this job?')
      if(n){
          axios
          .delete("/api/delete/job/"+user_id)
            .then(response => {
              this.$swal(response.data.message);
                this.getResults();
                
            })        
      }
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
          .post("/api/save/job", this.job)
          .then(response => {
              this.showForm = false;
              this.getResults();
              this.job= {
                        job_name: "",
                        customer_id: "",
                        start_date: "",
                        end_date: ""
                      };              
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
      this.submitted = false;
    },
    addNewJob(){
      if(this.customers.length < 1){
        axios
          .get("/api/get/customers")
          .then(response => {
              this.customers = response.data;
                        });      
      }
      this.showModal = true;
    }
    
  },
  mounted() {
     this.getResults();
     
  },
  created() {
    //this.getResults();
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
            <div class="row mb-2">

              <div class="col-sm-3">
                <div class="search-box me-2 mb-2 d-inline-block">
                  <div class="position-relative">
                    <input type="text" class="form-control" v-on:keyup="getResults()" v-model="search" placeholder="Search..." />
                    <i class="bx bx-search-alt search-icon"></i>
                  </div>
                </div>
              </div>

              <div class="col-sm-3">
                  <select class="form-control d-inline-block" v-model="search" @change="getResults()">
                    <option value="">Filter Jobs</option>
                    <option value="Published">Published Jobs</option>
                    <option value="Unpublished">Unpublished Jobs</option>
                  </select> 
              </div>

              <div class="col-sm-2">
                <div class="text-sm-end">
                  <button
                    type="button"
                    class="btn btn-info btn-rounded mb-2 me-2"
                     @click="publishJobs"
                  >
                    <i class="far fa-chart-bar"></i> &nbsp;Mark as Published
                  </button>
                </div>
              </div>
              
              <div class="col-sm-2">
                <div class="text-sm-end">
                  <button
                    type="button"
                    class="btn btn-danger btn-rounded mb-2 me-2"
                     @click="unPublishJobs"
                  >
                    <i class="far fa-chart-bar"></i> &nbsp;Mark as Unpublished
                  </button>
                </div>
              </div>

              <div class="col-sm-2">
                <div class="text-sm-end">
                  <button
                    type="button"
                    class="btn btn-success btn-rounded mb-2 me-2"
                     @click="addNewJob()"
                  >
                    <i class="mdi mdi-plus me-1"></i> Add New Job
                  </button>
                </div>
              </div>

                    <b-modal
                    v-model="showModal"
                    title="Add A New Job"
                    title-class="text-black font-18"
                    body-class="p-3"
                    size="lg"
                    hide-footer
                  >
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

                      <div class="text-end pt-5 mt-3">
                        <b-button variant="light" @click="showModal = false">Close</b-button>
                        <b-button type="submit" variant="success" class="ms-1"
                          >Add New Job</b-button
                        >
                      </div>
                    </form>
                  </b-modal>

              
                </div>
              </div>
              <!-- end col-->

            <div class="table-responsive">
              <table class="table table-centered table-nowrap">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Job Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="job in jobsData.data" :key="job.id">
                    <td>
                        <input
                          :id="`customCheck${job.id}`"
                          type="checkbox"
                          class="form-check-input"
                          :value="`${job.id}`"
                          @click="updateJobsArr"
                        />

                    </td>
                    <td>{{job.job_name}}</td>
                    <td>
                      {{job.start_date}}            
                    </td>
                    <td>
                      {{job.end_date}}            
                    </td>
                    <td>
                      {{job.status}}            
                    </td>
                    <td>
                        <router-link :to="edit_job_url(job.id)" class="mr-3">
                          <i class="fas fa-pencil-alt text-success me-1"></i> Edit
                        </router-link> 
                        <!-- &nbsp;&nbsp;&nbsp;
                        <a href="javascript:void(0)" @click="deleteJob(job.id)"><i class="fas fa-trash-alt text-danger me-1"></i> Delete</a> -->
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <pagination align="center" :data="jobsData" @pagination-change-page="getResults"></pagination>

            </div> <!-- card -->


          </div>
        </div>
    <!-- end row -->
  </Layout>
</template>
