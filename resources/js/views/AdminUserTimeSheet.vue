<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
    <form>
      <div class="row">
        <div class="col-4">
          <div class="mb-3">
            <select
              id="dpdwn-user"
              class="form-control"
              v-model="selectedUserName"
              @change="onChange($event)"
            >
              <option value="" disabled hidden>Select a User to view Timesheet</option>
              <option
                v-for="(item, index) in users"
                v-bind:key="index"
                :value="item.id"
              >
                {{ item.name }}  {{item.last_name}}
              </option>
            </select>
          </div>
        </div>
        <div class="col-3">
          <div class="mb-3">
            <button
              class="btn btn-primary"
              :disabled="selectedUserName === ''"
              @click.prevent="getTimesheet()"
            >
              View Timesheet
            </button>
          </div>
        </div>
      </div>
    </form>
    <template v-if="showTimesheet === true">
          <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">

            <div class="row mb-2">
              <div class="col-sm-2">
                <div class="search-box me-2 mb-2 d-inline-block">
                  <div class="position-relative">
                    <i class="bx bx-left-arrow-alt search-icon"  @click="previousWeeks()"></i>
                    <input type="text" class="form-control" v-model="currentActivedate" readonly style="background-color:#fff; width:148px" />
                    <i class=" bx bx-right-arrow-alt search-icon" @click="comingWeeks()" style="left:120px;"></i>                   
                  </div>
                </div>
              </div>  
              <div class="col-sm-3">
                  &nbsp;
              </div>            
              <div class="col-sm-7">
                <div class="text-sm-end">
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
                            <input type="time" id="appt"  :value="sechedule[tempDatesRange[k]]['user_start_time']" @change="dailyTotalHours($event , tempDatesRange[k] ,'start_date')" class="form-control smlTxt" />
                          </td>
                          <td :key="j+20">
                            <input type="time" id="appt"  :value="sechedule[tempDatesRange[k]]['user_end_time']" @change="dailyTotalHours($event , tempDatesRange[k] ,'end_date')" class="form-control smlTxt" />
                          </td>
                        </template>
                        <template v-else>
                          <td :key="j+25">&nbsp;</td>
                          <td :key="j+29">&nbsp;</td>
                        </template>
                      </template>
                      <td></td>
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
    </template>
  </Layout>
</template>
<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import pagination from "laravel-vue-pagination";
import "vue2-timepicker/dist/VueTimepicker.css";
import moment from "moment";
/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader, pagination },
  data() {
    return {
      activeJobs: Object,
      sechedule: Object,
      key: "",
      selectedUserName: "",
      showTimesheet: false,
      selectedUserID: "",
      startOfWeek: "",
      endOfWeek: "",
      tblHeader: [],
      tempDatesRange: [],
      currentActiveweek: 0,
      users: [],
      title: "Manage User Timesheets",
      items: [
        {
          text: "Dashboard",
          href: "/",
        },
        {
          text: "Timesheet",
          active: true,
        },
      ],
      showModal: false,
      showMultiModal: false,
      submitted: false,
    };
  },
  methods: {
    getCurrentWeek() {
      this.tblHeader = [];
      this.tempDatesRange = [];

      var currentDate = moment().subtract(this.currentActiveweek, "days");
      var weekStart = currentDate.clone().startOf("isoWeek");
      var weekEnd = currentDate.clone().endOf("isoWeek");
      for (var i = 0; i <= 6; i++) {
        this.tblHeader.push(moment(weekStart).add(i, "days").format("dddd"));
        this.tempDatesRange.push(
          moment(weekStart).add(i, "days").format("yyyy-MM-DD")
        );
      }
      this.currentActivedate =
        moment(weekStart).format("MMM") +
        " " +
        moment(weekStart).format("DD") +
        " - " +
        moment(weekEnd).format("DD");
      this.startOfWeek = moment(weekStart).format("YYYY-MM-DD");
      this.endOfWeek = moment(weekEnd).format("YYYY-MM-DD");
    },
    onChange(event) {
      this.selectedUserID = event.target.value;
      console.log("selectedUserID: " + this.selectedUserID);
    },

    getTimesheet() {
      axios
        .get(
          "/api/get/user_schedule?user_id=" +
            this.selectedUserID +
            "&start_date=" +
            this.startOfWeek +
            "&end_date=" +
            this.endOfWeek
        )
        .then(({ data }) => {
          this.activeJobs = data.jobs;
          this.sechedule = data.scheduler;
          console.log(this.sechedule);
          console.log("this.activeJobs: " + this.activeJobs);
          console.log("this.sechedule: " + this.sechedule);
          this.showTimesheet = true;
        });
    },
    previousWeeks() {
      this.currentActiveweek = this.currentActiveweek + 7;
      this.getCurrentWeek();
      this.getTimesheet();
    },
    comingWeeks() {
      this.currentActiveweek = this.currentActiveweek - 7;
      this.getCurrentWeek();
      this.getTimesheet();
    },
    saveTime(type){
      axios
        .post("/api/save/update_user_enteries?user_id="+this.selectedUserID+'&type='+type, this.sechedule)
        .then(response => {
            this.$swal(response.data.message);
            this.getTimesheet();
        })
    },
    dailyTotalHours(e, date, type) {
      if (type == "start_date") {
        this.sechedule[date].user_start_time = e.target.value;
      } else {
        this.sechedule[date].user_end_time = e.target.value;
      }

      for (const [key, value] of Object.entries(this.sechedule)) {
        if (value.user_start_time != "" && value.user_end_time != "") {
          var startTime = moment(value.user_start_time, "HH:mm");
          var endTime = moment(value.user_end_time, "HH:mm");
          let duration = moment.duration(endTime.diff(startTime));
          var hours = parseInt(duration.asHours());
          var minutes = parseInt(duration.asMinutes()) % 60;
          value.user_total_hours = moment(
            hours + ":" + minutes,
            "HH:mm"
          ).format("HH:mm");
        }
      }
      console.log(this.sechedule);
    },
  },
  created() {
    this.getCurrentWeek();
    axios.get(`/api/users?type=worker`).then(({ data }) => {
      this.users = data.data;
    });
  },
};
</script>