<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import { required } from "vuelidate/lib/validators";
import pagination from 'laravel-vue-pagination'
import moment from 'moment';

/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader , pagination },
  data() {
    return {
      payementsData: {
                type:Object,
                default:null
            },
      title: "Reporting Module",
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "Reporting",
          active: true
        }
      ],
      userFields:[],
      currentActiveweek:0,
      datesRange:[],
      startDate:String,
      endDate:String,
      tempDatesRange:[],
      currentActivedate:String,
      workers: Object,
      customers:Object,
      jobs:Object,
      shifts:Object,
      weeklyPayment:'',
      weeklyTimeSheet:{
        week:'',
        user:'',
        job:''
      },
      missedDays:{
        week:'',
        user:'',
        job:''
      },
      missedHours:{
        week:'',
        user:'',
        job:''
      },
      dailyReport:{
        customer:'',
        shift:'',
        job:'',
        start_date:'',
        end_date:'',
      }
    };
  },
  methods: {
    AddToUserFields(field){
      if(this.userFields.indexOf(field) !== -1){
        const index = this.userFields.indexOf(field);
        this.userFields.splice(index, 1);
      }else{
        this.userFields.push(field);
      }
    },
    getCustomers(){
        axios
          .get("/api/get/customers")
          .then(response => {
              this.customers = response.data;
                        });      
    },
    getJobs(){
        axios
          .get("/api/get/jobs")
          .then(response => {
              this.jobs = response.data;
                        });      
    },
    getSchedules(){
        axios
          .get("/api/get/shifts")
          .then(response => {
              this.shifts = response.data;
                        });      
    },
    ExportUsers(){
      if(this.userFields.length < 1){
        this.$swal("Please Select Fields To Export Users");
        return false;
      }
      axios({
          method: 'post',
          url: '/api/get/user_csv',
          responseType: 'blob',
          data:this.userFields
        })
        .then(({ data }) => {
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'employees.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });
    },
    WeeklyPaymentReport(){
      if(this.weeklyPayment == ''){
        this.$swal("Please Select Week Date To Export Report");
        return false;
      }
      axios({
          method: 'post',
          url: '/api/get/weekly_payment',
          responseType: 'blob',
          data:this.weeklyPayment
        })
        .then(({ data }) => {
            console.log(data);
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'weeklypayment_report.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });

    },
    ExportDailyReport(){
      if(this.dailyReport.start_date == ''
          || this.dailyReport.end_date == ''){
        this.$swal("Please Select Start & End Date To Export Report");
        return false;
      }
      axios({
          method: 'post',
          url: '/api/get/daily_report',
          responseType: 'blob',
          data:this.dailyReport
        })
        .then(({ data }) => {
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'DailyWorkReport.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });
    },
    TimesheetReviewReport(){
      if(this.weeklyTimeSheet.week == ''){
        this.$swal("Please Select Week Date To Export Report");
        return false;
      }
      axios({
          method: 'post',
          url: '/api/get/weekly_timesheet',
          responseType: 'blob',
          data:this.weeklyTimeSheet
        })
        .then(({ data }) => {
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'weekly_timesheet_report.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });

    },
    MissedDaysReport(){
      if(this.missedDays.week == ''){
        this.$swal("Please Select Week Date To Export Report");
        return false;
      }
      axios({
          method: 'post',
          url: '/api/get/missed_days',
          responseType: 'blob',
          data:this.missedDays
        })
        .then(({ data }) => {
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'missed_days_report.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });

    },
        MissedHoursReport(){
      if(this.missedHours.week == ''){
        this.$swal("Please Select Week Date To Export Report");
        return false;
      }
      axios({
          method: 'post',
          url: '/api/get/missed_hours',
          responseType: 'blob',
          data:this.missedHours
        })
        .then(({ data }) => {
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'missed_hours_report.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });

    },
    getWorkers(){
      axios
        .get("/api/get/workers")
        .then(response => {
            this.workers = response.data;
                      });      


    }
  },
  mounted() {
  },
  created() {
    this.getCustomers();
    this.getWorkers();
    this.getJobs();
    this.getSchedules();
    //this.getResults();
  }

};
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />

    <div class="row">

      <div class="col-6">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title text-centers">Get List Of Workers</h5>
                <p class="card-text export_users">
                  <span><input type="checkbox" @click="AddToUserFields('name')" id="name" /> <label for="name">First Name </label></span>
                  <span><input type="checkbox" @click="AddToUserFields('last_name')"  id="last_name" /> <label for="last_name">Last Name</label></span>
                  <span><input type="checkbox" @click="AddToUserFields('email')" id="email"  /> <label for="email">Email Address </label> </span>
                  <span><input type="checkbox" @click="AddToUserFields('phone_number')"  id="phone_number" /> <label for="phone_number">Phone Number </label> </span>
                  <span><input type="checkbox" @click="AddToUserFields('ni_number')"  id="ni_number" /> <label for="ni_number">NI Number </label> </span>
                  <span><input type="checkbox" @click="AddToUserFields('sort_code')"  id="sort_code" /> <label for="sort_code">Short Code </label> </span>
                  <span><input type="checkbox" @click="AddToUserFields('account_number')"  id="account_number" /> <label for="account_number">Account Number </label> </span>
                  <span><input type="checkbox" @click="AddToUserFields('rate')"   id="rate"/> <label for="rate">Rate </label> </span>
                  <span><input type="checkbox" @click="AddToUserFields('address')"   id="address"/> <label for="address">Address </label> </span>
                </p>
                <input type="button" value="Export Workers In CSV Format" @click="ExportUsers()" :disabled="this.userFields.length < 1" class="btn btn-success" />
            </div>
          </div>          
      </div>

      <div class="col-6">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title text-centers">Daily Report</h5>
                <div class="row">

                  <div class="col-6 form-group">
                    <label>Select Customer</label>
                    <select class="form-control" v-model="dailyReport.customer">
                      <option value="">Select a Customer</option>
                      <template v-for="(customer , id) in customers" >
                        <option  :value="id" :key="id">{{customer}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select Shift</label>
                    <select class="form-control" v-model="dailyReport.shift">
                      <option value="">Select a Shift</option>
                      <template v-for="(shift , id) in shifts" >
                        <option  :value="id" :key="id">{{shift}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select Job</label>
                    <select class="form-control" v-model="dailyReport.job">
                      <option value="">Select a job</option>
                      <template v-for="(job , id) in jobs" >
                        <option  :value="id" :key="id">{{job}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select Start Date</label>
                    <input class="form-control" type="date"  v-model="dailyReport.start_date"  />
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select End Date</label>
                    <input class="form-control" type="date" v-model="dailyReport.end_date" />
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group text-center">
                    <label>&nbsp;</label>
                    <input type="button" value="Export Daily Report In CSV Format" @click="ExportDailyReport()" 
                            class="btn btn-success mt-4" />
                  </div><!-- col-6 form-group -->


                </div>
            </div>
          </div>          
      </div>

      <div class="col-6">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title text-centers">Weekly Payment Report</h5>
                <div class="row">


                  <div class="col-6 form-group text-center">
                    <label>Select Week to Get Reporting</label>
                    <input type="week" v-model="weeklyPayment" class="form-control" />
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group text-center">
                    <label>&nbsp;</label>
                    <input type="button" value="Export Weekly Payment Report"  @click="WeeklyPaymentReport()" 
                            class="btn btn-success mt-4" />
                  </div><!-- col-6 form-group -->


                </div>
            </div>
          </div>          
      </div>

      <div class="col-6">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title text-centers">Timesheet Review Report</h5>
                <div class="row">


                  <div class="col-6 form-group">
                    <label>Select Week to Get Reporting</label>
                    <input type="week" v-model="weeklyTimeSheet.week" class="form-control" />
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select User</label>
                    <select class="form-control" v-model="weeklyTimeSheet.user">
                      <option value="">Select a user</option>
                      <template v-for="(worker , id) in workers" >
                        <option  :value="id" :key="id">{{worker}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select Job</label>
                    <select class="form-control" v-model="weeklyTimeSheet.job">
                      <option value="">Select a job</option>
                      <template v-for="(job , id) in jobs" >
                        <option  :value="id" :key="id">{{job}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group text-center">
                    <label>&nbsp;</label>
                    <input type="button" value="Export Timesheet Report"  @click="TimesheetReviewReport()" 
                            class="btn btn-success mt-4" />
                  </div><!-- col-6 form-group -->


                </div>
            </div>
          </div>          
      </div>
      <div class="col-6">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title text-centers">Missed Days</h5>
                <div class="row">


                  <div class="col-6 form-group">
                    <label>Select Week to Get Reporting</label>
                    <input type="week" v-model="missedDays.week" class="form-control" />
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select User</label>
                    <select class="form-control" v-model="missedDays.user">
                      <option value="">Select a user</option>
                      <template v-for="(worker , id) in workers" >
                        <option  :value="id" :key="id">{{worker}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select Job</label>
                    <select class="form-control" v-model="missedDays.job">
                      <option value="">Select a job</option>
                      <template v-for="(job , id) in jobs" >
                        <option  :value="id" :key="id">{{job}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group text-center">
                    <label>&nbsp;</label>
                    <input type="button" value="Export Timesheet Report"  @click="MissedDaysReport()" 
                            class="btn btn-success mt-4" />
                  </div><!-- col-6 form-group -->


                </div>
            </div>
          </div>          
      </div>
  <div class="col-6">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title text-centers">Missed Hours</h5>
                <div class="row">


                  <div class="col-6 form-group">
                    <label>Select Week to Get Reporting</label>
                    <input type="week" v-model="missedHours.week" class="form-control" />
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select User</label>
                    <select class="form-control" v-model="missedHours.user">
                      <option value="">Select a user</option>
                      <template v-for="(worker , id) in workers" >
                        <option  :value="id" :key="id">{{worker}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group">
                    <label>Select Job</label>
                    <select class="form-control" v-model="missedHours.job">
                      <option value="">Select a job</option>
                      <template v-for="(job , id) in jobs" >
                        <option  :value="id" :key="id">{{job}}</option>                              
                      </template>
                    </select>
                  </div><!-- col-6 form-group -->

                  <div class="col-6 form-group text-center">
                    <label>&nbsp;</label>
                    <input type="button" value="Export Timesheet Report"  @click="MissedHoursReport()" 
                            class="btn btn-success mt-4" />
                  </div><!-- col-6 form-group -->


                </div>
            </div>
          </div>          
      </div>



    </div>
    <!-- end row -->
  </Layout>
</template>
<style scoped>
.export_users span{ display: inline-block; margin: 2px 10px; width: 200px;}
</style>