<template>
  <Layout>
    <PageHeader :title="title" :items="items" />

    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between">
          <div class="mb-3" style="width: 20%">
            <select
              id="dpdwn-user"
              class="form-control"
              v-model="selected.selectedCustomer"
              @change="onChange($event)"
            >
              <option value="" disabled hidden>Select a Customer</option>
              <option
                v-for="(item, index) in users"
                v-bind:key="index"
                :value="item.id"
              >
                {{ item.name }} {{ item.last_name }}
              </option>
            </select>
          </div>
          <div class="mb-3">
            <button class="btn btn-success" @click="exportCurrentWeek()">
              <i class="bx bx-export"></i> Export Current Week
            </button>
            <button
              :disabled="show"
              class="btn btn-primary"
              @click="importLastWeek()"
            >
              <i class="bx bx-import"></i> Import From Last Week
            </button>
            <b-overlay :show="show" no-wrap> </b-overlay>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="row mb-2">
              <div class="d-flex justify-content-between align-items-center">
                <div class="search-box me-2 mb-2 d-inline-block">
                  <div class="position-relative">
                    <input
                      type="text"
                      class="form-control"
                      v-on:keyup="searchWorkers()"
                      v-model="search"
                      placeholder="Search..."
                    />
                    <i class="bx bx-search-alt search-icon"></i>
                  </div>
                </div>

                <div class="timesheet">
                  <div class="search-box me-2 mb-2 d-inline-block">
                    <div class="position-relative">
                      <i
                        class="bx bx-left-arrow-alt search-icon"
                        @click="previousWeeks()"
                      ></i>
                      <input
                        type="text"
                        class="form-control"
                        v-model="currentActivedate"
                        readonly
                        style="background-color: #fff; width: 150px"
                      />
                      <i
                        class="bx bx-right-arrow-alt search-icon"
                        @click="comingWeeks()"
                        style="left: 125px"
                      ></i>
                    </div>
                  </div>
                </div>

                <div class="">
                  <label for="from_date">From Date</label>
                  <div class="search-box me-2 mb-2">
                    <div class="position-relative">
                      <input
                        type="date"
                        v-model="copyDate.FromDate"
                        class="form-control"
                        placeholder="From Date"
                      />
                    </div>
                  </div>
                </div>
                <div class="">
                  <label for="from_date">To Date</label>
                  <div class="search-box me-2 mb-2">
                    <div class="position-relative">
                      <input
                        type="date"
                        v-model="copyDate.ToDate"
                        class="form-control"
                        placeholder="To date"
                      />
                    </div>
                  </div>
                </div>
                <div>
                  <input
                    type="button"
                    @click="copyData()"
                    class="btn btn-primary"
                    value="Update"
                  />
                </div>
              </div>
              <div class="col-sm-7">
                <div class="text-sm-end"></div>
              </div>
              <!-- end col-->
            </div>
            <div class="table-responsive">
              <table
                class="
                  table table-bordered table-centered table-nowrap
                  scheduler-table
                "
              >
                <thead>
                  <tr>
                    <th>Worker Name</th>
                    <th v-for="(th, k) in tblHeader" :key="k">
                      {{ th }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(w, k) in workers" :key="k">
                    <td>{{ w.name }} {{ w.last_name }}</td>
                    <template v-for="(user_col, ck) in datesRange">
                      <td
                        :key="ck"
                        style="text-align: center"
                        @mouseover="showIcons"
                        @mouseleave="hideIcons"
                      >
                        <template
                          v-if="
                            currentSchedule[w.id] &&
                            currentSchedule[w.id][user_col]
                          "
                        >
                          <p @click="updateJob(w, user_col)">
                            <strong
                              >{{
                                currentSchedule[w.id][user_col]["start_time"]
                              }}
                              -
                              {{
                                currentSchedule[w.id][user_col]["end_time"]
                              }}</strong
                            >
                            <br />
                            <span
                              >{{
                                currentSchedule[w.id][user_col]["total_hours"]
                              }}
                            </span>
                          </p>
                        </template>
                        <template v-else>
                          <!-- <i
                            class="bx bx-plus-medical"
                            @click="updateJob(w, user_col)"
                            style="display: none"
                          ></i> -->
                          <i
                            class="bx bx-plus-medical"
                            @click="showTemplates($event, w, user_col)"
                            style="display: none"
                          ></i>
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
            <h5 class="m-0">Update Timesheet</h5>
          </div>
          <div class="rightbar-title px-3 py-4">
            <label>User</label>
            <p>
              <input
                type="text"
                class="form-control"
                v-model="selected.selectedUsername"
                readonly
              />
            </p>
          </div>

          <!-- Settings -->
          <h6 class="text-center mb-0">{{ selected.selectedDateTitle }}</h6>

          <div class="p-4">
            <div class="col-md-12">
              <div>
                <label>Date</label>
                <p>{{ selected.selectedDate }}</p>
              </div>
              <div class="alert alert-danger" v-if="errorMesg">
                {{ errorMesg }}
              </div>
              <div>
                <label>Start</label>
                <span>
                  <input
                    type="time"
                    id="appt"
                    v-model="selected.selectedStartTime"
                    @change="calculateDuration"
                    class="form-control smlTxt"
                  />
                </span>
                <label>End</label>
                <span>
                  <input
                    type="time"
                    id="appt"
                    v-model="selected.selectedEndTime"
                    @change="calculateDuration"
                    class="form-control smlTxt"
                  />
                </span>
                <div class="p-3">
                  <span
                    >Duration:
                    <strong>{{ selected.selectedDuration }}</strong></span
                  >
                </div>
              </div>
              <hr class="mt-1" />
            </div>
            <div class="">
              <input
                type="button"
                class="btn btn-primary"
                value="Update"
                @click="updateSelectedJob()"
              />
              <input
                type="button"
                class="btn"
                value="Close timesheet"
                @click="hide()"
              />
            </div>
          </div>
        </simplebar>
      </div>

      <!-- Right bar overlay-->
      <div class="rightbar-overlay"></div>
      <div class="template-list" v-if="templatePopup" :style="styleObject">
        <div class="row">
          <div class="col-md-8"><strong>Select Time</strong></div>
        </div>
        <div class="row mt-2">
          <div>
            <label class="mb-0">Start</label>
            <span class="">
              <input
                type="time"
                v-model="TimeData.selectedStartTime"
                id="appt"
                @change="calculateDuration"
                class="form-control smlTxt p-1"
              />
            </span>
            <label class="mb-0">End</label>
            <span>
              <input
                type="time"
                v-model="TimeData.selectedEndTime"
                @change="calculateDuration"
                id="appt"
                class="form-control smlTxt p-1"
              />
            </span>
          </div>
          <div class="mt-3">
            <input
              type="button"
              class="btn btn-primary"
              @click="update_timesheet()"
              value="Update"
            />
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
.list-group-item p {
  margin: 0px;
  padding: 0px;
  cursor: pointer;
}
.list-group-item:hover {
  background-color: #eff2f7;
}
.template-list {
  background-color: #fff;
  position: absolute;
  width: 220px;
  padding: 10px;
  height: 202px;
  z-index: 100;
  border: 2px solid #eff2f7;
  border-radius: 5px;
  overflow-y: auto;
  overflow-x: hidden;
}
.search-box .search-icon {
  font-size: 16px;
  position: absolute;
  left: 13px;
  top: 0;
  line-height: 35px;
}
.scheduler-table td {
  cursor: pointer;
}
</style>
<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import { required } from "vuelidate/lib/validators";
import pagination from "laravel-vue-pagination";
import moment from "moment";
import simplebar from "simplebar-vue";
import VueTimepicker from "vue2-timepicker";
import "vue2-timepicker/dist/VueTimepicker.css";

/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader, pagination, simplebar, VueTimepicker },
  data() {
    return {
      styleObject: {
        top: "",
        left: "",
      },
      templatePopup: false,
      errorMesg: "",
      usersData: Object,
      title: "TIME SHEET",
      search: "",
      jobs: [],
      show: false,
      searchQuery: "",
      selected: {
        selectedDateTitle: "",
        selectedDate: "",
        selectedStartTime: "",
        selectedEndTime: "",
        selectedUserId: "",
        selectedUsername: "",
        selectedCustomer: "",
        selectedDuration: "",
        selectedType: "schedule",
      },
      currentActivedate: String,
      currentActiveweek: 0,
      displayMonth: false,
      currentActiveMonth: 0,
      currentSchedule: [],
      items: [
        {
          text: "Dashboard",
          href: "/",
        },
        {
          text: "Customer Timesheet",
          active: true,
        },
      ],
      tblHeader: [],
      datesRange: [],
      tempDatesRange: [],
      prevDatesRange: [],
      showModal: false,
      showMultiModal: false,
      submitted: false,
      excelImported: [],
      users: [],
      data: {
        customerId: "",
        date_range: "",
      },
      CustomerId: "",
      workers: Object,
      tempworkers: Object,
      copyDate: {
        FromDate: "",
        ToDate: "",
        customer_id: "",
      },

      TimeData: {
        selectedStartTime: "",
        selectedEndTime: "",
        selectedDateTitle: "",
        selectedDate: "",
        selectedUsername: "",
        selectedUserId: "",
        selectedDuration: "",
        selectedCustomer: "",
        selectedDuration: "",
        selectedType: "",
      },
    };
  },
  computed: {
    resultQuery() {
      if (this.searchQuery) {
        return this.templatesList.filter((item) => {
          return this.searchQuery
            .toLowerCase()
            .split(" ")
            .every((v) => item.title.toLowerCase().includes(v));
        });
      } else {
        return this.templatesList;
      }
    },
  },
  methods: {
    copyData() {
      if (this.CustomerId == "") {
        alert("Select Customer First");
        return;
      }
      this.copyDate.customer_id = this.CustomerId;
      this.$swal
        .fire({
          title: "Do you want to save the changes?",
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: `Copy`,
          
        })
        .then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            axios
              .post("/api/copy_date_record", this.copyDate)
              .then((response) => {
                this.copyDate = {
                  FromDate: "",
                  ToDate: "",
                  customer_id: this.CustomerId,
                };
                this.templatePopup = false;
                this.getResults();
              });
            $(this.$el).fadeOut(300, () => toastr.success("Data Copied."));
          }
        });
      // this.$swal(
      //   {
      //     title: "Copy",
      //     text: "Are you sure? You won't be able to revert this!",
      //     type: "warning",
      //     showCancelButton: true,
      //     confirmButtonColor: "#3085d6",
      //     confirmButtonText: "Yes, Copy it!",
      //     closeOnConfirm: true,
      //   },
      //   () => {
      //     axios
      //       .post("/api/copy_date_record", this.copyDate)
      //       .then((response) => {
      //         this.copyDate = {
      //           FromDate: "",
      //           ToDate: "",
      //           customer_id: this.CustomerId,
      //         };
      //         this.templatePopup = false;
      //         this.getResults();
      //       });

      //     $(this.$el).fadeOut(300, () => toastr.success("Data Copied."));
      //   }
      // );
    },
    update_timesheet() {
      this.errorMesg = "";
      this.TimeData.selectedType = "schedule";
      if (this.TimeData.selectedStartTime == "") {
        this.errorMesg = "Please Select Start Time";
        return false;
      }
      console.log(this.TimeData);
      axios
        .post("/api/save/update_user_enteries_new", this.TimeData)
        .then((response) => {
          this.timeData = {
            selectedDateTitle: "",
            selectedDate: "",
            selectedStartTime: "",
            selectedEndTime: "",
            selectedUserId: "",
            selectedUsername: "",
            selectedDuration: "",
            selectedCustomer: this.CustomerId,
          };
          this.templatePopup = false;
          this.getResults();
        });
    },
    showTemplates(e, user, job_date) {
      this.TimeData.selectedDateTitle =
        moment(job_date).format("ddd, MMM D, yyyy");

      this.TimeData.selectedDate = moment(job_date).format("M/D/yyyy");
      this.TimeData.selectedUsername = user.name + " " + user.last_name;
      this.TimeData.selectedUserId = user.id;
      this.TimeData.selectedCustomer = this.CustomerId;

      for (let i = 0; i < e.target.children.length; i++) {
        if (e.target.children[i].tagName == "I")
          e.target.children[i].style.display = "inline-block";
      }

      this.styleObject.left = parseInt(e.clientX + 20) + "px";
      this.styleObject.top = e.clientY + "px";
      this.templatePopup = true;
    },
    showIcons(e) {
      for (let i = 0; i < e.target.children.length; i++) {
        if (e.target.children[i].tagName == "I")
          e.target.children[i].style.display = "inline-block";
      }
    },

    hideIcons(e) {
      for (let i = 0; i < e.target.children.length; i++) {
        if (e.target.children[i].tagName == "I")
          e.target.children[i].style.display = "none";
      }
    },
    updateSelectedJob() {
      this.errorMesg = "";
      this.selected.selectedType = "schedule";
      if (this.selected.selectedStartTime == "") {
        this.errorMesg = "Please Select Start Time";
        return false;
      }

      axios
        .post("/api/save/update_user_enteries_new", this.selected)
        .then((response) => {
          this.selected = {
            selectedDateTitle: "",
            selectedDate: "",
            selectedStartTime: "",
            selectedEndTime: "",
            selectedUserId: "",
            selectedUsername: "",
            selectedDuration: "",
            selectedCustomer: this.CustomerId,
          };
          this.hide();
          this.getResults();
        });
    },
    calculateDuration() {
      if (this.TimeData.start_time != "" && this.TimeData.end_time != "") {
        var startTime = moment(this.TimeData.selectedStartTime, "hh:mm A");
        var endTime = moment(this.TimeData.selectedEndTime, "hh:mm A");
        let duration = moment.duration(endTime.diff(startTime));
        var hours = parseInt(duration.asHours());
        var minutes = parseInt(duration.asMinutes()) % 60;
        this.TimeData.selectedDuration = hours + ":" + minutes;
      }
    },

    onChange(event) {
      this.CustomerId = event.target.value;
      this.data.customerId = this.CustomerId;
      this.data.date_range = this.tempDatesRange;
      axios.post("/api/get/timesheet", this.data).then(({ data }) => {
        this.currentSchedule = data;
        this.datesRange = this.tempDatesRange;
      });
    },

    //
    edit_user_url(id) {
      //router.push({ name: 'user', params: { id } });
      return "/user/" + id;
    },
    previousWeeks() {
      if (this.CustomerId == "") {
        alert("Select Customer First");
        return;
      }
      this.templatePopup = false;
      if (this.displayMonth == false) {
        this.currentActiveweek = this.currentActiveweek + 7;
      } else {
        this.currentActiveMonth = this.currentActiveMonth + 1;
      }
      this.getCurrentWeek();
      this.getResults();
    },
    comingWeeks() {
      if (this.CustomerId == "") {
        alert("Select Customer First");
        return;
      }
      this.templatePopup = false;
      if (this.displayMonth == false) {
        this.currentActiveweek = this.currentActiveweek - 7;
      } else {
        this.currentActiveMonth = this.currentActiveMonth - 1;
      }
      this.getCurrentWeek();
      this.getResults();
    },
    hide() {
      document.body.classList.toggle("right-bar-enabled");
    },

    updateJob(user, job_date) {
      if (this.CustomerId == "") {
        alert("Select Customer First");
        return;
      }
      this.templatePopup = false;
      if (
        this.currentSchedule[user.id] &&
        this.currentSchedule[user.id][job_date]
      ) {
        this.selected = {
          selectedStartTime:
            this.currentSchedule[user.id][job_date]["start_time"],
          selectedEndTime: this.currentSchedule[user.id][job_date]["end_time"],
          selectedDuration:
            this.currentSchedule[user.id][job_date]["total_hours"],
        };
      }
      document.body.classList.toggle("right-bar-enabled");
      this.selected.selectedDateTitle =
        moment(job_date).format("ddd, MMM D, yyyy");
      this.selected.selectedDate = moment(job_date).format("M/D/yyyy");

      this.selected.selectedUsername = user.name + " " + user.last_name;
      this.selected.selectedUserId = user.id;
    },
    importLastWeek() {
      if (this.CustomerId == "") {
        alert("Select Customer First");
        return;
      }
      this.show = true;

      var currentDate = moment().subtract(7, "days");
      var weekStart = currentDate.clone().startOf("isoWeek");
      var weekEnd = currentDate.clone().endOf("isoWeek");
      for (var i = 0; i <= 6; i++) {
        this.prevDatesRange.push(
          moment(weekStart).add(i, "days").format("yyyy-MM-DD")
        );
      }
      this.data.customerId = this.CustomerId;
      this.data.date_range = this.prevDatesRange;
      axios.post("/api/import-fromlastweek", this.data).then((response) => {
        this.show = false;
        this.getResults();
        this.$swal(response.data.message);
      });
    },
    exportCurrentWeek() {
      if (this.CustomerId == "") {
        alert("Select Customer First");
        return false;
      }
      this.data.customerId = this.CustomerId;
      this.data.date_range = this.tempDatesRange;
      axios({
        method: "post",
        url: "/api/get/weeklyTimeSchdule",
        responseType: "blob",
        data: this.data,
      }).then(({ data }) => {
        console.log(data);
        try {
          const response = data;
          const url = window.URL.createObjectURL(
            new Blob([response], { type: "text/csv" })
          );
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "Timesheet_Report.csv");
          document.body.appendChild(link);
          link.click();
        } catch (error) {
          // Catch error response here. You may show and error message to client
        }
      });
    },
    getCurrentWeek() {
      this.tblHeader = [];
      this.tempDatesRange = [];
      if (this.displayMonth == false) {
        var currentDate = moment().subtract(this.currentActiveweek, "days");
        var weekStart = currentDate.clone().startOf("isoWeek");
        var weekEnd = currentDate.clone().endOf("isoWeek");
        for (var i = 0; i <= 6; i++) {
          this.tblHeader.push(
            moment(weekStart).add(i, "days").format("ddd  M/DD")
          );
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
      } else {
        var currentDate = moment().subtract(this.currentActiveMonth, "months");
        const startOfMonth = currentDate.clone().startOf("month");
        const endOfMonth = currentDate.clone().endOf("month");
        let j = currentDate.daysInMonth();
        for (var i = 0; i < j; i++) {
          this.tblHeader.push(
            moment(startOfMonth).add(i, "days").format("ddd  M/DD")
          );
          this.tempDatesRange.push(
            moment(startOfMonth).add(i, "days").format("yyyy-MM-DD")
          );
        }
        this.currentActivedate =
          moment(startOfMonth).format("MMM") +
          " " +
          moment(startOfMonth).format("DD") +
          " - " +
          moment(endOfMonth).format("DD");
      }
    },
    getWorkers() {
      axios.get(`/api/users?type=worker`).then(({ data }) => {
        this.workers = data.data;
        console.log(this.workers);
      });
    },
    searchWorkers() {
      //     if(this.search.length==0){
      //         this.workers = this.tempworkers;
      //     }else{
      //          let workerss = this.workers.filter( (worker) => worker.name.includes(this.search));
      //   this.tempworkers = this.workers;
      //  this.workers = workerss;

      //     }
      axios
        .get("/api/search-workers", { params: { keyword: this.search } })
        .then((res) => (this.workers = res.data))
        .catch((error) => {});
      //   console.log(this.search);
    },
    getResults() {
      this.data.customerId = this.CustomerId;
      this.data.date_range = this.tempDatesRange;
      axios.post("/api/get/timesheet", this.data).then(({ data }) => {
        this.currentSchedule = data;
        this.datesRange = this.tempDatesRange;
      });
    },
  },
  watch: {
    datesRange() {},
  },
  mounted() {},
  created() {
    if (!this.$gate.isAdmin()) this.$router.push({ name: "userTimeSheet" });

    this.getCurrentWeek();
    // this.getResults();
    this.getWorkers();
    axios.get(`/api/users?type=customers`).then(({ data }) => {
      this.users = data.data;
    });
  },
};
</script>

