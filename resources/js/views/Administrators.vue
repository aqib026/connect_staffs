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
      usersData: Object,
      title: "Users",
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
      inputs: [{
        n_first_name: '',
        n_last_name: '',
        n_phone_number:''
      }],
      showModal: false,
      showMultiModal:false,
      submitted: false,
      user: {
        name: "",
        last_name: "",
        phone_number: "",
        email: "",
        user_password: "",
        type:'admin'
      },
    };
  },
  validations: {
    user: {
      name: { required },
      last_name: { required },
      phone_number: { required },
      email: { required },
      user_password: { required },
    },
  },
  methods: {
    //
    edit_user_url(id){
      //router.push({ name: 'user', params: { id } });
      return '/user/'+id;
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
        .get(`/api/users?page=${page}&search=${this.search}&type=admin`)
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
    add(index) {
        this.inputs.push({
                              n_first_name: '',
                              n_last_name: '',
                              n_phone_number:''
                            });
      },
      remove(index) {
        this.inputs.splice(index, 1);
      },    

    handleMultiSubmit(e){
        this.showMultiModal = false;
        axios
          .post("/api/save/multiusers?type=admin", this.inputs)
          .then(response => {
              this.showForm = false;
              this.getResults();
              this.$swal(response.data.message);
              this.inputs = [{
                              n_first_name: '',
                              n_last_name: '',
                              n_phone_number:''
                            }];
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
                        last_name: "",
                        phone_number: "",
                        email: "",
                        user_password: "",
                        type:'admin'
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
                    <i class="mdi mdi-plus me-1"></i> Add New User
                  </button>
                  <button
                    type="button"
                    class="btn btn-info btn-rounded mb-2 me-2"
                     @click="showMultiModal = true"
                  >
                    <i class="mdi mdi-plus me-1"></i> Add Multi Users
                  </button>
                    <b-modal
                    v-model="showModal"
                    title="Add New User"
                    title-class="text-black font-18"
                    body-class="p-3"
                    hide-footer
                  >
                  <form @submit.prevent="handleSubmit">
                      <div class="row">
                        <div class="col-12">
                          <div class="mb-3">
                            <label for="name">First Name</label>
                            <input
                              id="name"
                              v-model="user.name"
                              type="text"
                              class="form-control"
                              placeholder="Insert first name"
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
                        <div class="col-12">
                          <div class="mb-3">
                            <label for="name">Last Name</label>
                            <input
                              id="name"
                              v-model="user.last_name"
                              type="text"
                              class="form-control"
                              placeholder="Last name"
                              :class="{
                                'is-invalid':
                                  submitted && $v.user.last_name.$error,
                              }"
                            />
                            <div
                              v-if="
                                submitted && !$v.user.last_name.required
                              "
                              class="invalid-feedback"
                            >
                              This value is required.
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="mb-3">
                            <label for="phone_number">Phone</label>
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
                        <div class="col-12">
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
                            <label for="user_password">Password</label>
                            <input
                              id="user_password"
                              v-model="user.user_password"
                              type="password"
                              class="form-control"
                              placeholder="Enter Password"
                              :class="{
                                'is-invalid':
                                  submitted && $v.user.user_password.$error,
                              }"
                            />
                            <div
                              v-if="submitted && !$v.user.user_password.required"
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
                          >Add New User</b-button
                        >
                      </div>
                    </form>
                  </b-modal>

                    <b-modal
                    v-model="showMultiModal"
                    title="Add Multiples Users"
                    title-class="text-black font-18"
                    body-class="p-3"
                    size="xl"
                    hide-footer
                  >
                    <form @submit.prevent="handleMultiSubmit">
                      <div class="row">

                        <div class="col-md-12">
                          <div class="row" v-for="(input,k) in inputs" :key="k">
                            <div class="col">
                              <input type="text" class="form-control inlineform" required v-model="input.n_first_name" placeholder="Enter First Name" />
                            </div>
                            <div class="col">
                              <input type="text" class="form-control" required v-model="input.n_last_name" placeholder="Enter Last Name" />
                            </div>
                            <div class="col">
                              <input type="text" class="form-control" required v-model="input.n_phone_number" placeholder="Enter Phone Number" />
                              <span>
                                <i
                                  class="fas fa-minus-circle"
                                  @click="remove(k)"
                                  v-show="k || ( !k && inputs.length > 1)"
                                ></i>
                                <i
                                  class="fas fa-plus-circle"
                                  @click="add(k)"
                                  v-show="k == inputs.length-1"
                                ></i>
                              </span>                          
                            </div>
                          </div>
                        </div>

                      </div>

                      <div class="text-end pt-5 mt-3">
                        <b-button variant="light" @click="showMultiModal = false">Close</b-button>
                        <b-button type="submit" variant="success" class="ms-1"
                          >Add New User</b-button
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Created On</th>
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
                    <td>{{users.name}} {{users.last_name}}</td>
                    <td><p class="mb-0">{{users.email}}</p></td>
                    <td>
                      {{users.phone}}            
                    </td>
                    <td>{{users.created_at}}</td>
                    <td>
                        <router-link :to="edit_user_url(users.id)" class="mr-3">
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
