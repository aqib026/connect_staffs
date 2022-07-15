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
      usersData: {
                type:Object,
                default:null
            },
      title: "Customers",
      search:"",
      deleteArr: [],
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
      showModal: false,
      showMultiModal:false,
      submitted: false,
      user: {
        name: "",
        business_name: "",
        address:"",
        phone_number: "",
        email: "",
        type:'customers'
      },
    };
  },
  validations: {
    user: {
      name: { required },
      business_name: { required },
      phone_number: { required },
      address: { required },
      email: { required },
    },
  },
  methods: {
    //
    edit_customer_url(id){
      //router.push({ name: 'user', params: { id } });
      return '/customer/'+id;
    },
    updateDelArr(e){
       if (e.target.checked){
        this.deleteArr.push(e.target.value);
       }else{
        const index = this.deleteArr.indexOf(e.target.value);
        if (index > -1) {
          this.deleteArr.splice(index, 1);
        }         
       }
      console.log(this.deleteArr);
    },
    deleteAll(){
      if(this.deleteArr.length > 0){
        let n = confirm('are you sure you want to delete all these users ?');
        if(n){
        axios
          .post("/api/delete/multiusers", this.deleteArr)
          .then(response => {
              this.$swal('Users Deleted Successfully');
              this.getResults();
          })

        }
      }else{
          this.$swal('Please select users to delete !');
      }
    },
    async getResults(page = 1) {
      axios
        .get(`/api/users?page=${page}&search=${this.search}&type=customers`)
        .then(({ data }) => {this.usersData = data;});
    },
    deleteUser(user_id){
      let n = confirm('are you sure you want to delete the user?')
      if(n){
          axios
          .delete("/api/delete/user/"+user_id)
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
      console.log('form submited');
      this.submitted = true;

      // stop here if form is invalid
      this.$v.$touch();
      if (this.$v.$invalid) {
        console.log('first ');
        return;
      } else {
        this.showModal = false;
        axios
          .post("/api/save/user", this.user)
          .then(response => {
              this.showForm = false;
              this.getResults();
              
              this.user = {
                        name: "",
                        business_name: "",
                        phone_number: "",
                        email: "",
                        address:"",
                        type:'customers'
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
              <div class="col-sm-4">
                <div class="search-box me-2 mb-2 d-inline-block">
                  <div class="position-relative">
                    <input type="text" class="form-control" v-on:keyup.enter="getResults()" v-model="search" placeholder="Search..." />
                    <i class="bx bx-search-alt search-icon"></i>
                  </div>
                </div>
              </div>
              <div class="col-sm-8">
                <div class="text-sm-end">
                  <button
                    type="button"
                    class="btn btn-danger btn-rounded mb-2 me-2"
                     @click="deleteAll"
                  >
                    <i class="fas fa-trash-alt me-1"></i>Delete All
                  </button>
                  <button
                    type="button"
                    class="btn btn-success btn-rounded mb-2 me-2"
                     @click="showModal = true"
                  >
                    <i class="mdi mdi-plus me-1"></i> Add New Customer
                  </button>
                    <b-modal
                    v-model="showModal"
                    title="Add New Customer"
                    title-class="text-black font-18"
                    body-class="p-3"
                    size="lg"
                    hide-footer
                  >
                  <form @submit.prevent="handleSubmit">
                      <div class="row">
                        <div class="col-6">
                          <div class="mb-3">
                            <label for="name">Business Name</label>
                            <input
                              id="business_name"
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
                            <label for="name">Contact Name</label>
                            <input
                              id="name"
                              v-model="user.name"
                              type="text"
                              class="form-control"
                              placeholder="Contact Name"
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
                            <label for="phone">Phone</label>
                            <input
                              id="phone_number"
                              v-model="user.phone_number"
                              type="text"
                              class="form-control"
                              placeholder="Enter Phone Number"
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

                        <div class="col-12">
                          <div class="mb-3">
                            <label for="email">Address</label>
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

                      <div class="text-end pt-5 mt-3">
                        <b-button variant="light" @click="showModal = false">Close</b-button>
                        <b-button type="submit" variant="success" class="ms-1"
                          >Add New Customer</b-button
                        >
                      </div>
                    </form>
                  </b-modal>                
                </div>
              </div>
              <!-- end col-->
            </div>
            <div class="table-responsive">
              <table class="table table-centered table-nowrap">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Business Name</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="users in usersData.data" :key="users.id">
                    <td>
                     <div class="form-check font-size-16">
                        <input
                          :id="`customCheck${users.id}`"
                          type="checkbox"
                          class="form-check-input"
                          :value="`${users.id}`"
                          @click="updateDelArr"
                        />
                        <label
                          class="form-check-label"
                          :for="`customCheck${users.id}`"
                        >&nbsp;</label>
                      </div>
                    </td>
                    <td>{{users.business_name}}</td>
                    <td>{{users.name}}</td>
                    <td><p class="mb-0">{{users.email}}</p></td>
                    <td>
                      {{users.phone_number}}            
                    </td>
                    <td>
                        <router-link :to="edit_customer_url(users.id)" class="mr-3">
                          <i class="fas fa-pencil-alt text-success me-1"></i> Edit
                        </router-link> 
                        &nbsp;&nbsp;&nbsp;
                        <a href="javascript:void(0)" @click="deleteUser(users.id)"><i class="fas fa-trash-alt text-danger me-1"></i> Delete</a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <pagination align="center" :data="usersData" @pagination-change-page="getResults"></pagination>

          </div>
        </div>
      </div>
    </div>
    <!-- end row -->
  </Layout>
</template>
