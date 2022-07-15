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
      title: "Payment Module",
      search:"",
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "Payments",
          active: true
        }
      ],
      currentActiveweek:0,
      datesRange:[],
      startDate:String,
      endDate:String,
      tempDatesRange:[],
      currentActivedate:String,
      workers: Object,
    };
  },
  methods: {
    clearPayment(e , user_id){
      if(e.target.checked){
        this.payementsData.advPayment[user_id].amount_paid = this.payementsData.advPayment[user_id].total_due;
        this.payementsData.advPayment[user_id].week_outstanding = 0;
      }
    },
    previousWeeks(){
      this.currentActiveweek = this.currentActiveweek + 7;
      this.getCurrentWeek();
    },
    comingWeeks(){
      this.currentActiveweek = this.currentActiveweek - 7;      
      this.getCurrentWeek();
    },
    async getResults(page = 1) {
        axios
          .get(`/api/get/payments?startDate=${this.startDate}&endDate=${this.endDate}`)
          .then(({ data }) => {
            this.payementsData = data.data;
            console.log(this.payementsData);            
            this.initialCalculation();
            });
    },
    getCurrentWeek(){
        this.tempDatesRange = [];
        var currentDate = moment().subtract(this.currentActiveweek, "days");
        var weekStart  = currentDate.clone().startOf('isoWeek');
        var weekEnd    = currentDate.clone().endOf('isoWeek');
        this.startDate = weekStart.format("yyyy-M-DD");
        this.endDate   = weekEnd.format("yyyy-M-DD");
        for (var i = 0; i <= 6; i++) {
          this.tempDatesRange.push(moment(weekStart).add(i, 'days').format("yyyy-M-DD"));
        }
        this.currentActivedate = moment(weekStart).format("MMM") + ' ' + moment(weekStart).format("DD") + ' - ' + moment(weekEnd).format("DD");
        this.getResults();             
    },  
    showCost(user_id , time , rate){
      if(time == '' || time == null) return 0;
      let timeArr = time.split(":");
      let hours   = timeArr[0];
      let minutes = timeArr[1];
      let hours_cost = hours * rate;
      let minutes_cost = minutes * ( rate / 60)
      let total_cost = hours_cost + minutes_cost;
      total_cost = parseFloat(total_cost).toFixed(2);
      this.payementsData.advPayment[user_id].cost = total_cost;
    },
    outstandingPayment:function(user_id){
      let outstanding = 
                parseFloat(this.payementsData.advPayment[user_id].cost) - 
                ( parseFloat(this.payementsData.advPayment[user_id].ni_payment) + parseFloat(this.payementsData.advPayment[user_id].tax) );
      if(outstanding < 0 ) outstanding = 0;    
      outstanding = parseFloat(outstanding).toFixed(2);
      this.payementsData.advPayment[user_id].outstanding = outstanding;    
      
      this.totalDue(user_id);
      this.amountPaid(user_id);

    },
    totalDue:function(user_id){
      let totalDue = 
                this.payementsData.advPayment[user_id].outstanding
                - this.payementsData.advPayment[user_id].amount;
      totalDue = parseFloat(totalDue).toFixed(2);                
      this.payementsData.advPayment[user_id].total_due = totalDue;
      return totalDue;
    },
    amountPaid:function(user_id){
      let week_outstanding = 
                this.payementsData.advPayment[user_id].total_due
                - this.payementsData.advPayment[user_id].amount_paid;
      week_outstanding = parseFloat(week_outstanding).toFixed(2);                
      this.payementsData.advPayment[user_id].week_outstanding = week_outstanding; 
    },
    saveAndUpdate(){
      axios
        .post("/api/save/weekly_payment?endDate="+this.endDate, this.payementsData)
        .then(response => {
        })

    },    
    initialCalculation:function(){
        if(this.payementsData.ue !== undefined){
          this.payementsData.ue.forEach((item) => {
            console.log(item);
              this.showCost(item.user_id,item.total_hours,item.user.rate);
              this.outstandingPayment(item.user_id);
          });          
        }
    }
  },
  mounted() {
     this.getResults();     
  },
  created() {
    this.getCurrentWeek();
    this.initialCalculation();
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
                    <i class="bx bx-left-arrow-alt search-icon"  @click="previousWeeks()"></i>
                    <input type="text" class="form-control" v-model="currentActivedate" readonly style="background-color:#fff; width:135px" />
                    <i class=" bx bx-right-arrow-alt search-icon" @click="comingWeeks()" style="left:106px;"></i>                   
                  </div>
                </div>
              </div>
              <div class="col-sm-3 offset-sm-6">
                <input type="button" value="Save & Update" @click="saveAndUpdate()" class="btn btn-success float-right" />
              </div>

                      
                </div>
            <div class="table-responsive">
              <table class="table table-centered table-nowrap">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>User Name</th>
                    <th>Job</th>
                    <th>Hours Worked</th>
                    <th>Rate</th>
                    <th>Cost</th>
                    <th>NI Payment</th>
                    <th>Tax</th>
                    <th>Outstanding Payment</th>
                    <th>Advance Payment</th>
                    <th>Total Due</th>
                    <th>Amount Paid</th>
                    <th>Amount Outstanding</th>
                    <th title="Check this box mark complete payment">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(payment , index) in payementsData.ue" :key="index">
                    <td> {{ index + 1}}</td>
                    <td> <span v-if="payment.user">{{ payment.user.name}} </span> </td>
                    <td> <span v-if="payment.job"> {{ payment.job.job_name}} </span> </td>
                    <td> {{ payment.total_week_hours }}</td>
                    <td> <span v-if="payment.user"> {{ payment.user.rate }} </span> </td>
                    <td>  <span v-if="payment.user" > 
                        {{ showCost(  payment.user_id , payment.total_week_hours , payment.user.rate ) }} 
                        {{ payementsData.advPayment[payment.user_id].cost }}
                      </span> </td>
                    <td>  <input type="text" class="form-control" 
                                  @keyup="outstandingPayment(payment.user_id)"
                                  v-model="payementsData.advPayment[payment.user_id].ni_payment" 
                                  style="width:80px" /> </td>
                    <td>  <input type="text" class="form-control" 
                                  @keyup="outstandingPayment(payment.user_id)"
                                  v-model="payementsData.advPayment[payment.user_id].tax" style="width:80px" /> </td>
                    <td> <span v-if="payementsData.advPayment[payment.user_id]"> {{ payementsData.advPayment[payment.user_id].outstanding }} </span> </td>
                    <td> <span v-if="payementsData.advPayment[payment.user_id]">{{ payementsData.advPayment[payment.user_id].amount }} </span></td>
                    <td> <span v-if="payementsData.advPayment[payment.user_id]"> {{ totalDue(payment.user_id)	 }} </span> </td>
                    <td>  <input type="text" class="form-control" 
                                  @keyup="amountPaid(payment.user_id)"
                                  v-model="payementsData.advPayment[payment.user_id].amount_paid" style="width:80px" /> </td>
                    <td> <span v-if="payementsData.advPayment[payment.user_id]">{{ payementsData.advPayment[payment.user_id].week_outstanding }} </span></td>
                    <td> <input type="checkbox" @click="clearPayment($event , payment.user_id)" /></td>
                  </tr>
                </tbody>
              </table>
            </div>

     </div>
              <!-- end col-->
            </div>
          
          </div>
        </div>
    <!-- end row -->
  </Layout>
</template>
