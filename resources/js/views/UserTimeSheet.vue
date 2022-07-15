 <template>
  <Layout>
    <PageHeader :title="title" :items="items" />

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">

            <div class="row mb-2">

              <div class="col-sm-3">
                  &nbsp;
              </div>

              <div class="col-sm-2">
                <div class="search-box me-2 mb-2 d-inline-block">
                  <div class="position-relative">
                    <i class="bx bx-left-arrow-alt search-icon"  @click="previousWeeks()"></i>
                    <input type="text" class="form-control" v-model="currentActivedate" readonly style="background-color:#fff; width:135px" />
                    <i class=" bx bx-right-arrow-alt search-icon" @click="comingWeeks()" style="left:106px;"></i>                   
                  </div>
                </div>
              </div>              
              <div class="col-sm-7">
                <div class="text-sm-end">
                 <input type="button" value="Save Time Log" @click="saveTime('save')" class="btn btn-info" />
                  <input type="button" value="Submit Time Log" @click="saveTime('submit')" class="btn btn-success" />
                </div>
              </div>
              <!-- end col-->
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-centered table-nowrap scheduler-table">
                <thead>
                  <tr>
                    <th>&nbsp;</th>
                    <th colspan="2" style="text-align:center;" v-for="(job,k) in activeJobs" :key="k">
                      {{ job.job.job_name }}
                    </th>
                    <th>Break</th>
                    <th>Total Hours</th>
                  </tr>
                  <tr>
                    <th>&nbsp;</th>
                    <template v-for="(job,k) in activeJobs">
                      <th :key="k">Start Time</th>
                      <th :key="k+20">End Time</th>
                    </template>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                  </tr>

                  <template v-for="(th,k) in tblHeader">
                    <tr :key="k">
                      <td>{{ th }}</td>
                      <template v-for="(job,j) in activeJobs">
                        <template v-if="sechedule[tempDatesRange[k]] != null && sechedule[tempDatesRange[k]]['job_id'] == job.job.id" >
                          <td :key="j">
                            <input type="time" id="appt" :readonly="sechedule[tempDatesRange[k]]['editable'] > 0" :value="sechedule[tempDatesRange[k]]['user_start_time']" @change="dailyTotalHours($event , tempDatesRange[k] , job.job.id,sechedule[tempDatesRange[k]]['schedule_id'],'start_date')" class="form-control smlTxt" />
                          </td>
                          <td :key="j+20">
                            <input type="time" id="appt" :readonly="sechedule[tempDatesRange[k]]['editable'] > 0" :value="sechedule[tempDatesRange[k]]['user_end_time']" @change="dailyTotalHours($event , tempDatesRange[k] , job.job.id,sechedule[tempDatesRange[k]]['schedule_id'],'end_date')" class="form-control smlTxt" />
                          </td>
                        </template>
                        <template v-else>
                          <td :key="j+25">&nbsp;</td>
                          <td :key="j+29">&nbsp;</td>
                        </template>
                      </template>
                      <td>30 min</td>
                      <td>
                        <input type="text" v-if="sechedule[tempDatesRange[k]] != null"
                              readonly v-model="sechedule[tempDatesRange[k]]['user_total_hours']" class="form-control smlTxt" />
                      </td>
                    </tr>
                  </template>
                </thead>
                <tbody>
                  
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- end row -->


  </Layout>
</template>
<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import simplebar from "simplebar-vue";
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import moment from 'moment';

/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader  , simplebar , VueTimepicker },
  data() {
    return {
      activeJobs: Object,
      sechedule:Object,
      currentActiveweek:0,
      startOfWeek:'',
      endOfWeek:'',
      title: "Manage Timesheet",
      search:"",
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "User Time Sheet",
          active: true
        }
      ],
      tblHeader:[],
      datesRange:[],
      tempDatesRange:[],
      showModal: false,
      showMultiModal:false,
      submitted: false,
      workers: Object,
    };
  },
  computed: {
  }  ,
  methods: {
    dailyTotalHours(e,date,job_id,schedule_id,type){
      if(type == 'start_date'){
        this.sechedule[date].user_start_time = e.target.value;
      }else{
        this.sechedule[date].user_end_time = e.target.value;
      }

      for (const [key, value] of Object.entries(this.sechedule)) {
        if(value.user_start_time != '' && value.user_end_time != ''){
          var startTime = moment(value.user_start_time, "HH:mm");
          var endTime = moment(value.user_end_time, "HH:mm");
          let duration = moment.duration(endTime.diff(startTime));
          var hours = parseInt(duration.asHours());
          var minutes = parseInt(duration.asMinutes())%60;
          value.user_total_hours = moment(hours+':'+minutes,"HH:mm").format("HH:mm");
        }
      }      
      console.log(this.sechedule);

    },
    saveTime(type){
      if(type == 'submit'){
        let n = confirm('Are you sure you want to submit the timesheet you wont be able to edit it again later?')
        if(!n) return false
      }
      axios
        .post("/api/save/update_user_enteries?user_id="+window.user.id+'&type='+type, this.sechedule)
        .then(response => {
            this.$swal(response.data.message);
            this.getResults();
        })
    },
    previousWeeks(){
      this.currentActiveweek = this.currentActiveweek + 7;
       this.getCurrentWeek();
       this.getResults();
    },
    comingWeeks(){
      this.currentActiveweek = this.currentActiveweek - 7;      
       this.getCurrentWeek();
       this.getResults();
    },    
    getResults(){
      axios
        .get('/api/get/user_schedule?user_id=' + window.user.id + '&start_date=' + this.startOfWeek + '&end_date=' + this.endOfWeek)
        .then(({ data }) => {
            this.activeJobs = data.jobs;
            this.sechedule = data.scheduler;
            console.log(this.sechedule);
          });
    },
    getCurrentWeek(){
        this.tblHeader = [];
        this.tempDatesRange = [];

        var currentDate = moment().subtract(this.currentActiveweek, "days");
        var weekStart = currentDate.clone().startOf('isoWeek');
        var weekEnd   = currentDate.clone().endOf('isoWeek');
        for (var i = 0; i <= 6; i++) {
          this.tblHeader.push(moment(weekStart).add(i, 'days').format("dddd"));
          this.tempDatesRange.push(moment(weekStart).add(i, 'days').format("yyyy-M-DD"));
        }
        this.currentActivedate = moment(weekStart).format("MMM") + ' ' + moment(weekStart).format("DD") + ' - ' + moment(weekEnd).format("DD");
        this.startOfWeek = moment(weekStart).format("YYYY-MM-DD");
        this.endOfWeek = moment(weekEnd).format("YYYY-MM-DD");;
    },    

  },
  watch:{
    datesRange(){
      
    }
  },
  mounted() {

  },
  created() {    
    this.getCurrentWeek();
    this.getResults();
    
  }

};
</script>
<style scoped>
.smlTxt{ max-width:120px; }
.list-group-item p{ margin: 0px; padding: 0px; cursor: pointer; }
.list-group-item:hover{ background-color: #eff2f7; }
.template-list{ background-color: #fff; position: absolute; width: 220px; padding: 10px; height: 350px; z-index: 100; border:2px solid #eff2f7; border-radius: 5px; overflow-y: auto; overflow-x: hidden; }
.scheduler-table td{ cursor: pointer; }
</style>
