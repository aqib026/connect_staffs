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
      usersData: Object,
      title: "Users",
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
        last_name: "",
        phone: "",
        email: "",
        user_password: "",
        type:'worker'
      },
    };
  },
  validations: {
    user: {
      name: { required },
      last_name: { required },
      phone: { required },
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
    getResults(page = 1) {
      axios
        .get("api/users?page=" + page + '&type=worker')
        .then(({ data }) => (this.usersData = data.data));
    },
    deleteUser(user_id){
      let n = confirm('are you sure you want to delete the user?')
      if(n){
          axios
          .delete("/api/delete/user/"+user_id)
            .then(response => {
              console.log(response);
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
              this.user =               this.user = {
                        name: "",
                        last_name: "",
                        phone: "",
                        email: "",
                        user_password: "",
                        type:'worker'
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
    console.log(window.user);
     
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
            <div class="row mb-2">
              <div class="col-sm-4">
                <div class="search-box me-2 mb-2 d-inline-block">
                  <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search..." />
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
                            <label for="phone">Phone</label>
                            <input
                              id="phone"
                              v-model="user.phone"
                              type="text"
                              class="form-control"
                              placeholder="Enter Phonenumber"
                              :class="{
                                'is-invalid':
                                  submitted && $v.user.phone.$error,
                              }"
                            />
                            <div
                              v-if="submitted && !$v.user.phone.required"
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
                    hide-footer
                  >
                    <form @submit.prevent="handleMultiSubmit">
                      <div class="row">

                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
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
                  <tr v-for="users in usersData" :key="users.id">
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
            <!-- <ul class="pagination pagination-rounded justify-content-end mb-2">
              <li class="page-item disabled">
                <a class="page-link" href="javascript: void(0);" aria-label="Previous">
                  <i class="mdi mdi-chevron-left"></i>
                </a>
              </li>
              <li class="page-item active">
                <a class="page-link" href="javascript: void(0);">1</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript: void(0);">2</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript: void(0);">3</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript: void(0);">4</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript: void(0);">5</a>
              </li>
              <li class="page-item">
                <a class="page-link" href="javascript: void(0);" aria-label="Next">
                  <i class="mdi mdi-chevron-right"></i>
                </a>
              </li>
            </ul> -->
          </div>
        </div>
      </div>
    </div>
    <!-- end row -->
  </Layout>
</template>
