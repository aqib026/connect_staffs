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
                    <input type="text" class="form-control" v-on:keyup.enter="getResults()" v-model="search" placeholder="Search..." />
                    <i class="bx bx-search-alt search-icon"></i>
                  </div>
                </div>
              </div>

              <div class="col-sm-2">
                <input type="button" class="btn" value="Monthly" @click="showMonthly()">
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

             
                </div>
              </div>
              <!-- end col-->
            </div>
            <div class="table-responsive">

              <table class="table table-bordered table-centered table-nowrap scheduler-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th v-for="(th,k) in tblHeader" :key="k">
                      {{ th }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  
                  <tr v-for="(w,k) in workers" :key="k">
                    <td>{{ w.name  }} {{ w.last_name  }}</td>
                    <template v-for="(user_col,ck) in datesRange" >
                      <td  :key="ck" style="text-align:center;" @mouseover="showIcons" @mouseleave="hideIcons"> 
                        <template  v-if="currentSchedule[w.id] && currentSchedule[w.id][user_col]">
                          <p @click="updateJob(w , user_col)">
                          <strong>{{ currentSchedule[w.id][user_col]['start_time'] }} -  {{ currentSchedule[w.id][user_col]['end_time'] }}</strong>
                          <br />
                          <span >{{ currentSchedule[w.id][user_col]['title'] }} </span>                                           
                          </p>
                        </template>                       
                        <template v-else>
                        <i class="bx bx-plus-medical" @click="updateJob(w , user_col)" style="display:none;"></i>
                        <i class="bx bx-list-ol" @click="showTemplates($event , w , user_col)" style="display:none;"></i>
                        </template>

                      </td>
                    </template>
                    
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- end row -->


    <div>
        <div class="right-bar">
            <simplebar class="h-100">
                  <div class="rightbar-title px-3 py-4">
                    <a
                        href="javascript:void(0);"
                        class="right-bar-toggle float-end"
                        @click="hide"
                    >
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                    <h5 class="m-0">Update Schedule</h5>
                </div>              
                <div class="rightbar-title px-3 py-4">
                   
                </div>

                <!-- Settings -->
                <h6 class="text-center mb-0">{{ selected.selectedDateTitle }}</h6>

                <div class="p-4">                  
                  <div class="col-md-12">
                    <div>
                      <label>Date</label>
                      <p>{{ selected.selectedDate }}</p>
                    </div>
                    <div class="alert alert-danger" v-if="errorMesg">{{ errorMesg }}</div>
                    <div>
                      <label>Start</label>
                        <span>
                          <input type="time" id="appt" v-model="selected.selectedStartTime" 
                                @change="calculateDuration" class="form-control smlTxt" />
                        </span>
                      <label>End</label>
                        <span>
                          <input type="time" id="appt" v-model="selected.selectedEndTime" 
                                @change="calculateDuration" class="form-control smlTxt" />
                        </span>
                        <div><strong>{{ selected.selectedDuration }}</strong></div>
                    </div>
                    <hr class="mt-1">
                    <div>
                      <label>Shift </label>
                      <p><input type="text" class="form-control" v-model="selected.selectedShiftTitle" /></p>
                      <label>Job Title</label>
                      <p>
                        <select class="form-control" id="customer_id" v-model="selected.selectedJob">
                          <option value="">Select a Job</option>
                          <template v-for="(job , id) in jobs" >
                            <option  :value="id" :key="id">{{ job }}</option>                              
                          </template>
                        </select>
                      </p>
                      <label>User</label>
                      <p><input type="text" class="form-control" v-model="selected.selectedUsername" /></p>
                    </div>
                  </div>
                  <div class="">
                    <input type="button" class="btn btn-primary" value="Update" @click="updateSelectedJob()">
                    <input type="button" class="btn btn-primary" value="Save As Template" @click="saveTemplate()">
                    <input type="button" class="btn " value="Close Schedule" @click="hide()">
                  </div>
                </div>
            </simplebar>
        </div>

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
    </div>    

      <div class="template-list" v-if="templatePopup" :style="styleObject">
          <div class="row">
            <div class="col-md-8"><strong>Select Template</strong></div>
            <div class="col-md-4 text-end" @click="addTemplteFromWidget" style="color:blue; cursor:pointer">+ Add </div>
          </div>

          <div class="row">
            <div class="col-md-12">
                <div class="search-box me-2 mb-2 d-inline-block mt-2">
                  <div class="position-relative">
                    <input type="text" class="form-control" style="border-radius:2px" v-model="searchQuery" placeholder="Search..." />
                    <i class="bx bx-search-alt search-icon"></i>
                  </div>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <ul class="list-group  mt-2">
                <li class="list-group-item text-center" v-for="(tl,k) in resultQuery" :key="k">
                  <div @click="assignTemplate(tl)">
                    <p>{{ tl.start_time}} - {{ tl.end_time}}</p>
                    <p>{{ tl.title}}</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
      </div>

  </Layout>
</template>

<style scoped>
.list-group-item p{
  margin: 0px; padding: 0px;
  cursor: pointer;
}
.list-group-item:hover{
  background-color: #eff2f7;
}
.template-list{
  background-color: #fff;
  position: absolute;
  width: 220px;
  padding: 10px;
  height: 350px;
  z-index: 100;
  border:2px solid #eff2f7;
  border-radius: 5px;
  overflow-y: auto;
  overflow-x: hidden;
}
.scheduler-table td{
  cursor: pointer;
}
</style>
<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import { required } from "vuelidate/lib/validators";
import pagination from 'laravel-vue-pagination'
import moment from 'moment';
import simplebar from "simplebar-vue";
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'

/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader , pagination , simplebar , VueTimepicker },
  data() {
    return {
      styleObject: {
        top:'',
        left:''
      },      
      templatePopup:false,
      errorMesg:'',
      templatesList:[],
      usersData: Object,
      title: "Job Scheduler",
      search:"",
      jobs:[],
      searchQuery:"",
      selected: {
        selectedDateTitle: '',
        selectedDate: '',
        selectedStartTime: '',
        selectedEndTime: '',
        selectedShiftTitle: '',
        selectedUserId:'',
        selectedJob:'',
        selectedUsername:'',
        selectedDuration:'',
        selectedType:'schedule'
      },
      currentActivedate:String,
      currentActiveweek:0,
      displayMonth:false,
      currentActiveMonth:0,
      currentSchedule:[],
      items: [
        {
          text: "Dashboard",
          href: "/"
        },
        {
          text: "JOB SCHEDULER",
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
    resultQuery(){
      if(this.searchQuery){
      return this.templatesList.filter((item)=>{
        return this.searchQuery.toLowerCase().split(' ').every(v => item.title.toLowerCase().includes(v))
      })
      }else{
        return this.templatesList;
      }
    }
  }  ,
  methods: {
    getJobs(){
      if(this.jobs.length < 1){
        axios
          .get("/api/get/jobs")
          .then(response => {
              this.jobs = response.data;
                        });      
      }
      console.log('hola' + this.jobs);
    },
    showIcons(e){
      for (let i = 0; i < e.target.children.length; i++) {
          if(e.target.children[i].tagName == 'I')
            e.target.children[i].style.display = 'inline-block';
      }      
    },
    hideIcons(e){
        for (let i = 0; i < e.target.children.length; i++) {
            if(e.target.children[i].tagName == 'I')
              e.target.children[i].style.display = 'none';
        }      
    },
    saveTemplate(){
      this.selected.selectedType = 'template';
      if(this.selected.selectedStartTime == ''){
        this.errorMesg = 'Please Select Start Time';
        return false;
      }
      if(this.selected.selectedShiftTitle == ''){
        this.errorMesg = 'Please Select Shift Title';
        return false;
      }
      axios
        .post("/api/save/scheduler", this.selected)
        .then(response => {
            this.selected =  {
              selectedDateTitle: '',
              selectedDate: '',
              selectedStartTime: '',
              selectedEndTime: '',
              selectedShiftTitle: '',
              selectedUserId:'',
              selectedJob:'',
              selectedUsername:'',
              selectedDuration:''
            };
            this.hide();
            this.getTemplates();
        })

    },
    assignTemplate(tl){

      this.selected.selectedStartTime = tl.start_time;
      this.selected.selectedEndTime   = tl.end_time;
      this.selected.selectedJob       = tl.job_id;
      this.selected.selectedDuration  = tl.total_hours;
      this.selected.selectedShiftTitle  = tl.title;
      this.selected.selectedType  = 'schedule';
      
        axios
          .post("/api/save/scheduler", this.selected)
          .then(response => {
              this.selected =  {
                selectedDateTitle: '',
                selectedDate: '',
                selectedStartTime: '',
                selectedEndTime: '',
                selectedShiftTitle: '',
                selectedUserId:'',
                selectedJob:'',
                selectedUsername:'',
                selectedDuration:''
              };
              this.templatePopup = false;
              this.getResults();
          })

    },
    updateSelectedJob(){
      this.errorMesg = '';
      this.selected.selectedType = 'schedule';
      if(this.selected.selectedStartTime == ''){
        this.errorMesg = 'Please Select Start Time';
        return false;
      }
      if(this.selected.selectedShiftTitle == ''){
        this.errorMesg = 'Please Select Shift Title';
        return false;
      }
      if(this.selected.selectedJob == ''){
        this.errorMesg = 'Please Select Job';
        return false;
      }
        axios
          .post("/api/save/scheduler", this.selected)
          .then(response => {
              this.selected =  {
                selectedDateTitle: '',
                selectedDate: '',
                selectedStartTime: '',
                selectedEndTime: '',
                selectedShiftTitle: '',
                selectedUserId:'',
                selectedJob:'',
                selectedUsername:'',
                selectedDuration:''
              };
              this.hide();
              this.getResults();
          })

    },
    calculateDuration(){
      // start time and end time
      if(this.selected.selectedStartTime != '' && this.selected.selectedEndTime != ''){
        var startTime = moment(this.selected.selectedStartTime, "hh:mm A");
        var endTime = moment(this.selected.selectedEndTime, "hh:mm A");
        let duration = moment.duration(endTime.diff(startTime));
        var hours = parseInt(duration.asHours());
        var minutes = parseInt(duration.asMinutes())%60;
        this.selected.selectedDuration = hours+':'+minutes;
      }
    },
    //
    edit_user_url(id){
      //router.push({ name: 'user', params: { id } });
      return '/user/'+id;
    },
    previousWeeks(){
      this.templatePopup = false;
      if(this.displayMonth == false){
      this.currentActiveweek = this.currentActiveweek + 7;
      }else{
        this.currentActiveMonth = this.currentActiveMonth + 1;
      }
      this.getCurrentWeek();
      this.getResults();
    },
    comingWeeks(){
      this.templatePopup = false;
      if(this.displayMonth == false){
      this.currentActiveweek = this.currentActiveweek - 7;      
      }else{
        this.currentActiveMonth = this.currentActiveMonth - 1;
      }
      this.getCurrentWeek();
      this.getResults();
    },
    hide() {
       document.body.classList.toggle("right-bar-enabled");
    },
    showTemplates(e , user , job_date){
      this.selected.selectedDateTitle = moment(job_date).format("ddd, MMM D, yyyy");
      this.selected.selectedDate = moment(job_date).format("M/D/yyyy");

      this.selected.selectedUsername = user.name + ' ' + user.last_name;
      this.selected.selectedUserId = user.id;
      for (let i = 0; i < e.target.children.length; i++) {
          if(e.target.children[i].tagName == 'I')
            e.target.children[i].style.display = 'inline-block';
      }      

      this.styleObject.left = parseInt(e.clientX+20) +'px';
      this.styleObject.top  = e.clientY+'px';
      this.templatePopup = true;
    },
    addTemplteFromWidget(){
      this.templatePopup = false;
       document.body.classList.toggle("right-bar-enabled");

    },
    updateJob(user , job_date) {
      this.templatePopup = false;
      if(this.currentSchedule[user.id] && this.currentSchedule[user.id][job_date]){
              this.selected =  {
                selectedStartTime: this.currentSchedule[user.id][job_date]['start_time'],
                selectedEndTime: this.currentSchedule[user.id][job_date]['end_time'],
                selectedShiftTitle: this.currentSchedule[user.id][job_date]['title'],
                selectedJob:this.currentSchedule[user.id][job_date]['job_id'],
                selectedDuration:this.currentSchedule[user.id][job_date]['total_hours']
              };
      }
       document.body.classList.toggle("right-bar-enabled");
       this.selected.selectedDateTitle = moment(job_date).format("ddd, MMM D, yyyy");
       this.selected.selectedDate = moment(job_date).format("M/D/yyyy");

       this.selected.selectedUsername = user.name + ' ' + user.last_name;
       this.selected.selectedUserId = user.id;

    },
    showMonthly(){
      this.displayMonth = true;
      this.getCurrentWeek();
      this.getResults();
    },
    getCurrentWeek(){
        this.tblHeader = [];
        this.tempDatesRange = [];
      if(this.displayMonth == false){
        var currentDate = moment().subtract(this.currentActiveweek, "days");
        var weekStart = currentDate.clone().startOf('isoWeek');
        var weekEnd   = currentDate.clone().endOf('isoWeek');
        for (var i = 0; i <= 6; i++) {
          this.tblHeader.push(moment(weekStart).add(i, 'days').format("ddd  M/DD"));
          this.tempDatesRange.push(moment(weekStart).add(i, 'days').format("yyyy-MM-DD"));
        }
        this.currentActivedate = moment(weekStart).format("MMM") + ' ' + moment(weekStart).format("DD") + ' - ' + moment(weekEnd).format("DD");
        console.log(this.tempDatesRange);
      }else{
        var currentDate = moment().subtract(this.currentActiveMonth, "months");
        const startOfMonth = currentDate.clone().startOf('month');
        const endOfMonth   = currentDate.clone().endOf('month');        
        let j = currentDate.daysInMonth();
        for (var i = 0; i < j; i++) {
          this.tblHeader.push(moment(startOfMonth).add(i, 'days').format("ddd  M/DD"));
          this.tempDatesRange.push(moment(startOfMonth).add(i, 'days').format("yyyy-MM-DD"));
        }
        this.currentActivedate = moment(startOfMonth).format("MMM") + ' ' + moment(startOfMonth).format("DD") + ' - ' + moment(endOfMonth).format("DD");

      }
    },
    getWorkers(){
      axios
        .get(`/api/users?type=worker`)
        .then(({ data }) => {this.workers = data.data;});
    },
    getResults(){
      axios
        .post('/api/get/scheduler',this.tempDatesRange)
        .then(({ data }) => {
       
          this.currentSchedule = data;
          this.datesRange = this.tempDatesRange;
          console.log(this.currentSchedule);
          
          });

    },
    getTemplates(){
      axios
        .post('/api/get/templates',['template'])
        .then(({ data }) => {
          this.templatesList = data;
          console.log(this.templatesList);
          });
    }

  },
  watch:{
    datesRange(){
      
    }
  },
  mounted() {

  },
  created() {    
    if(!this.$gate.isAdmin()) this.$router.push({ name: 'userTimeSheet' })

    this.getCurrentWeek();
    this.getResults();
    this.getWorkers();
    this.getTemplates();
    this.getJobs();
    console.log(this.styleObject);
  }

};
</script>

