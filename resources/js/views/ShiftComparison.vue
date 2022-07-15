<script>
import Layout from "../layouts/main";
import PageHeader from "../components/page-header";
import "vue2-timepicker/dist/VueTimepicker.css";
import moment from "moment";
import xlsx from "xlsx";
/**
 * Customers component
 */
export default {
  components: { Layout, PageHeader },
  data() {
    return {
      dataArr: [],
      reportedDataArr: [],
      activeJobs: Object,
      sechedule: Object,
      loadTable: false,
      users: [],
      customers:[],
      selectedCustomerID: "",
      selectedJobID: "",
      currentActiveweek: 0,
      currentActivedate: String,
      tempDatesRange: [],
      startOfWeek: "",
      endOfWeek: "",
      excelImported:[],
      title: "Shift Comparison",
      items: [
        {
          text: "Dashboard",
          href: "/",
        },
        {
          text: "Shift Comparison",
          active: true,
        },
      ],
    };
  },
  methods: {
    importExcel(e) {
      const files = e.target.files;
      if (!files.length) {
        return;
      } else if (!/\.(xls|xlsx|csv)$/.test(files[0].name.toLowerCase())) {
        return alert(
          "The upload format is incorrect. Please upload xls or xlsx format"
        );
      }
      const fileReader = new FileReader();
      console.log(fileReader);
      fileReader.onload = (ev) => {
        try {
          console.log("here");
          const data = ev.target.result;
          const XLSX = xlsx;
          const workbook = XLSX.read(data, {
            type: "binary",
            cellDates: true,
            dateNF: 'dd/mm/yyyy;@'
          });
          const wsname = workbook.SheetNames[0]; // Take the first sheet，wb.SheetNames[0] :Take the name of the first sheet in the sheets
          const ws = XLSX.utils.sheet_to_json(workbook.Sheets[wsname],{ raw: false,}); // Generate JSON table content，wb.Sheets[Sheet名]    Get the data of the first sheet
          for (var i = 0; i < ws.length; i++) {
            if( ws[i]['Lastname'] != undefined 
                  && ws[i]['Customer'] != undefined 
                    && ws[i]['Jobtype'] != undefined)
            this.excelImported.push({
                                    'Lastname'   : ws[i]['Lastname'].trim(),
                                    'Customer'   : ws[i]['Customer'].trim(),
                                    'Jobtype'    : ws[i]['Jobtype'].trim(),
                                    'Date'       : ws[i]['Date'],
                                    'Start time' : ws[i]['Start time'].trim(),
                                    'Finish time' : ws[i]['Finish time'].trim(),
                                    'Hours worked' : ws[i]['Hours worked'].trim()
                                    });
          }

          this.getLoggedParam();
          console.log(this.excelImported);

        } catch (e) {
          return alert("Read failure!");
        }
      };
      fileReader.readAsBinaryString(files[0]);
      var input = this.$refs.fileInput;
      input.value = "";
      this.loadTable = true;
    },
    chooseFiles() {
      this.$refs.fileInput.click();
    },
    getLoggedParam() {
        axios
          .post("/api/get/get_user_schedule", this.excelImported)
          .then(response => {
            this.reportedDataArr = response.data;
            this.excelImported = [];
            console.log(this.reportedDataArr);
          });        
    },
    onChangeDpdwn() {
    },
    previousWeeks() {
      this.currentActiveweek = this.currentActiveweek + 7;
      this.getCurrentWeek();
      this.getJobSchedule();
    },
    comingWeeks() {
      this.currentActiveweek = this.currentActiveweek - 7;
      this.getCurrentWeek();
      this.getJobSchedule();
    },
    getCurrentWeek() {
      this.tempDatesRange = [];
      var currentDate = moment().subtract(this.currentActiveweek, "days");
      var weekStart = currentDate.clone().startOf("isoWeek");
      var weekEnd = currentDate.clone().endOf("isoWeek");
      this.currentActivedate =
        moment(weekStart).format("MMM") +
        " " +
        moment(weekStart).format("DD") +
        " - " +
        moment(weekEnd).format("DD");
      this.startOfWeek = moment(weekStart).format("YYYY-MM-DD");
      this.endOfWeek = moment(weekEnd).format("YYYY-MM-DD");
    },
    getDifference(excelHours, timesheetHours) {
      if (timesheetHours == undefined) {
        return "Missing Info";
      }
      var startTime = moment(excelHours, "HH:mm");
      var endTime = moment(timesheetHours, "HH:mm");
      let duration = moment.duration(endTime.diff(startTime));
      var hours = parseInt(duration.asHours());
      var minutes = parseInt(duration.asMinutes()) % 60;
      return moment(hours + ":" + minutes, "HH:mm").format("HH:mm");
    },
    getJobSchedule() {
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
        });
    },
    downloadComparison() {

      axios({
          method: 'post',
          url: '/api/download/shiftcomparison',
          responseType: 'blob',
          data:this.reportedDataArr
        })
        .then(({ data }) => {
            try {
              const response = data;
              const url = window.URL.createObjectURL(
                new Blob([response], { type: "text/csv" })
              );
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", 'shift-comparison.csv');
              document.body.appendChild(link);
              link.click();
            } catch (error) {
              // Catch error response here. You may show and error message to client
            }
        });      

    },
    ExcelDateToJSDate(serial, type) {
      var utc_days = Math.floor(serial - 25569);
      var utc_value = utc_days * 86400;
      var date_info = new Date(utc_value * 1000);

      var fractional_day = serial - Math.floor(serial) + 0.0000001;

      var total_seconds = Math.floor(86400 * fractional_day);

      var seconds = total_seconds % 60;

      total_seconds -= seconds;

      var hours = Math.floor(total_seconds / (60 * 60));
      var minutes = Math.floor(total_seconds / 60) % 60;

      if (type == "duration") {
        return moment(
          new Date(
            date_info.getFullYear(),
            date_info.getMonth(),
            date_info.getDate(),
            hours,
            minutes,
            seconds
          )
        ).format("HH:mm");
      } else if (type == "time")
        return moment(
          new Date(
            date_info.getFullYear(),
            date_info.getMonth(),
            date_info.getDate(),
            hours,
            minutes,
            seconds
          )
        ).format("hh:mm A");
      else if (type == "date")
        return moment(
          new Date(
            date_info.getFullYear(),
            date_info.getMonth(),
            date_info.getDate(),
            hours,
            minutes,
            seconds
          )
        ).format("yyyy-M-DD");
    },
  },
  created() {
    this.getCurrentWeek();
    axios.get(`/api/get/customers`).then(({ data }) => {
      this.customers = data;
    });
  },
};
</script>
<template>
  <Layout>
    <PageHeader :title="title" :items="items" />
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-3">
            <label>Pick a Customer to compare shifts</label>
            <div class="mb-3">
              <select
                class="form-control"
                v-model="selectedCustomerID"
                @change="onChangeDpdwn($event)"
              >
                <option value="" disabled hidden>Select a Customer</option>
                <option
                  v-for="(item, index) in customers"
                  v-bind:key="index"
                  :value="index"
                >
                  {{ item }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <template>
          <div class="row">
            <div class="col-3">
              <div class="mb-3">
                <input
                  type="file"
                  style="display: none"
                  ref="fileInput"
                  @change="importExcel"
                />
                <button class="btn btn-primary" @click.prevent="chooseFiles()">
                  Upload file
                </button>
              </div>
            </div>
          </div>
        </template>

        <!-- Table components -->
        <template v-if="loadTable == true">
          <table
            class="
              table table-bordered table-centered table-nowrap
              scheduler-table
            "
          >
            <thead>
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th colspan="3" style="text-align: center">
                  Customer Timesheet Timings
                </th>
                <th colspan="3" style="text-align: center">
                  Worker Timesheet Timings
                </th>
              </tr>
              <tr>
                <th style="text-align: center">Date</th>
                <th style="text-align: center">Worker Name</th>
                <th style="text-align: center">Customer</th>
                <th style="text-align: center">Job Title</th>
                <th style="text-align: center">Start Time</th>
                <th style="text-align: center">End Time</th>
                <th style="text-align: center">Hours Worked</th>
                <th style="text-align: center">Start Time</th>
                <th style="text-align: center">End Time</th>
                <th style="text-align: center">Hours Worked</th>
                <th style="text-align: center">Difference</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in reportedDataArr" v-bind:key="index">
                      <td>{{ item.date }}</td>
                      <td>{{ item.worker_name }}</td>
                      <td>{{ item.customer_name }}</td>
                      <td>{{ item.job_name }}</td>
                      <td>{{ item.customer_stime }}</td>
                      <td>{{ item.customer_etime }}</td>
                      <td>{{ item.customer_thours }}</td>
                      <td>{{ item.start_time }}</td>
                      <td>{{ item.end_date }}</td>
                      <td>{{ item.total_hours }}</td>
                      <td>{{ item.difference }}</td>
              </tr>
            </tbody>
          </table>
          <div class="col-3">
            <div class="mb-3">
              <button
                class="btn btn-primary"
                @click.prevent="downloadComparison()"
              >
                Download Comparison
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </Layout>
</template>
