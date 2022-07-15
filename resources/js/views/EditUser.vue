<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import { required } from "vuelidate/lib/validators";
import moment from 'moment';

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
      showModal: false,
      submitted: false,
      user: {
        name: "",
        last_name: "",
        phone_number: "",
        email: "",
        type: "",
      },
      payments: {},
      currentweekVal: {},
      paymentData: {
        amount: "",
        notes: "",
        year: "",
        week: ""
      }
    };
  },
  validations: {
    user: {
      name: { required },
      last_name: { required },
      phone_number: { required },
      email: { required }
    },
  },
  methods: {
    getResults() {
      axios.all([
        axios.get(`/api/user/${this.$route.params.id}`),
        axios.get(`/api/payments/${this.$route.params.id}`)
    ])
    .then(axios.spread((response1, response2)=> {
        this.user = response1.data.data;
        console.log(this.user);
        this.payments = response2.data.data;
        console.log(this.payments);
    }))
    .catch(function (error) {

    });
    },
    savePayment() {
      axios
          .post(`/api/payments/${this.$route.params.id}`, this.paymentData)
          .then(response => {
            this.$swal(response.data.message);
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
            this.$swal(response.data.message);
            this.$router.push({ path : '/users' });
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
    getStartDate(year, week){
      return moment().year(year).week(week).startOf('isoweek').format("ddd, MMM-D-yyyy");
    },
    getEndDate(year, week){
      return moment().year(year).week(week).startOf('isoweek').add(6, "days").format("ddd, MMM-D-yyyy");
    },
  },
  mounted() {
    console.log(this.$route.params.id);
},
  created() {
    this.getResults();
  },
  beforeMount(){
    var currentDate = moment();
    this.paymentData.week = currentDate.week();
    this.paymentData.year = currentDate.year();
    this.currentweekVal = currentDate.clone().startOf('isoWeek').format("ddd, MMM-D-yyyy") + " To " + currentDate.clone().endOf('isoWeek').format("ddd, MMM-D-yyyy");
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
                                <div class="col-6">
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
                                <template v-if="user.type == 'worker'">
                                  <div class="col-6">
                                    <div class="mb-3">
                                      <label for="ni_number">NI Number</label>
                                      <input
                                        id="ni_number"
                                        v-model="user.ni_number"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter NI Number"
                                      />
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <div class="mb-3">
                                      <label for="account_number">Account Number</label>
                                      <input
                                        id="account_number"
                                        v-model="user.account_number"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter Account Number"
                                      />
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <div class="mb-3">
                                      <label for="sort_code">Sort Code</label>
                                      <input
                                        id="sort_code"
                                        v-model="user.sort_code"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter Sort Code"
                                      />
                                    </div>
                                  </div>
                                  <div class="col-6">
                                    <div class="mb-3">
                                      <label for="rate">Rate</label>
                                      <input
                                        id="rate"
                                        v-model="user.rate"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter Rate"
                                      />
                                    </div>
                                  </div>
                                  <div class="col-12">
                                    <div class="mb-3">
                                      <label for="post_address">Address</label>
                                      <input
                                        id="post_address"
                                        v-model="user.post_address"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter Post Address"
                                      />
                                    </div>
                                  </div>
                                </template>
                            </div>

                            <button type="submit"  class="btn btn-primary mr-1">
                                Save Changes
                            </button>
                            <router-link v-if="user.type == 'worker'" to="/users" type="reset" class="btn btn-secondary">
                                Cancel
                            </router-link>
                            <router-link v-if="user.type == 'admin'" to="/administrators" type="reset" class="btn btn-secondary">
                                Cancel
                            </router-link>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="user.type == 'worker'">
          <div class="row">
            <div class="col-12">
                  <div class="card">
                      <div class="card-body">
                          <h4 class="card-title">Manage Advance Payment of Current Week</h4>
                            <p class="card-title-desc">
                              Update advance payment below
                          </p>
                          <form> 
                              <div class="row">
                                  <div class="col-4">
                                    <div class="mb-3">
                                      <label for="currentweek">Current Week</label>
                                      <input
                                        id="currentweek"
                                        type="text"
                                        v-model="currentweekVal"
                                        class="form-control"
                                        readonly
                                      />
                                    </div>
                                  </div>
                                  <div class="col-4">
                                    <div class="mb-3">
                                      <label for="amount">Amount</label>
                                      <input
                                        id="amount"
                                        min="0"
                                        type="number"
                                        class="form-control"
                                        placeholder="Enter Advance Payment Amount"
                                        v-model.number="paymentData.amount"
                                      />
                                    </div>
                                  </div>
                                  <div class="col-4">
                                    <div class="mb-3">
                                      <label for="notes">Notes</label>
                                      <input
                                        id="notes"
                                        type="text"
                                        class="form-control"
                                        placeholder="Enter Notes"
                                        v-model="paymentData.notes"
                                      />
                                    </div>
                                  </div>
                              </div>
                              <button class="btn btn-primary" :disabled="paymentData.amount === '' || paymentData.amount === 0"
                                @click="savePayment">Save
                              </button>
                          </form>
                      </div>
                  </div>
            </div>
          </div>
          <div class="row">
              <div class="col-12">
                  <div class="card">
                      <div class="card-body">
                          <h4 class="card-title">Previous Payments</h4>
                          <p class="card-title-desc">
                              See details of older advance payments below
                          </p>
                          <div class="table-responsive">
                            <table class="table table-centered table-nowrap">
                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th>Week</th>
                                  <th>Amount</th>
                                  <th>Added On</th>
                                  <th>Notes</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="payment in payments" :key="payment.id">
                                  <td>{{payment.id}}</td>
                                  <td>{{getStartDate(payment.year, payment.week)}} to {{getEndDate(payment.year, payment.week)}}</td>
                                  <td>{{payment.amount}}</td>
                                  <td>{{payment.created_at | formatDate}}</td>
                                  <td>{{payment.notes}}</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </template>
  </Layout>
</template>