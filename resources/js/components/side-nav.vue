<script>
import MetisMenu from "metismenujs/dist/metismenujs";

import { menuItems } from "./menu";

/**
 * Side-nav component
 */
export default {
  data() {
    return {
      menuItems: menuItems,
      manage_users:false,
      manage_jobs:false,
      manage_scheduler:false,
      manage_timesheet:false,
      payment_module:false,
      report_module:false
    };
  },
  mounted: function () {
    // eslint-disable-next-line no-unused-vars
    var menuRef = new MetisMenu("#side-menu");
    if(this.$route.name.includes('job-scheduler')){
      this.manage_scheduler = true;
    }
    if(this.$route.name.includes('job')){
      this.manage_jobs = true;
    }
    if(this.$route.name.includes('user') || 
        this.$route.name.includes('administrator') ||
          this.$route.name.includes('customer') ){
      this.manage_users = true;
    }
    if(this.$route.name.includes('TimeSheet') ){
      this.manage_timesheet = true;
    }
    if(this.$route.name.includes('payments') ){
      this.payment_module = true;
    }
    if(this.$route.name.includes('reporting') ){
      this.report_module = true;
    }
  },
  methods: {
    hasItems(item) {
      return item.subItems !== undefined ? item.subItems.length > 0 : false;
    },
  },
};
</script>
<style scoped>
.router-link-active{
  color:#fff !important;
}
</style>
<template>
  <!-- ========== Left Sidebar Start ========== -->

  <!--- Sidemenu -->
  <div id="sidebar-menu">
    <!-- Left Menu Start -->
    <ul id="side-menu" class="metismenu list-unstyled">


        <li :class="{'mm-active':manage_users}" v-if="$gate.isAdmin()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Manage Users</span></a>  
          <ul  class="sub-menu" style="" :class="{'mm-show':manage_users}">
            <li>
              <router-link to="/users">Workers</router-link>
            </li>
            <li>
              <router-link to="/administrators">Administrators</router-link>
            </li>
            <li>
              <router-link to="/customers">Customers</router-link>
            </li>
          </ul>
        </li>      
        <li :class="{'mm-active':manage_jobs}" v-if="$gate.isAdmin()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Jobs Management</span></a>  
          <ul  class="sub-menu" style="" :class="{'mm-show':manage_jobs}">
            <li>
              <router-link to="/jobs">Jobs</router-link>
            </li>
            <!-- <li>
              <router-link to="/jobs">Un-Publish Jobs</router-link>
            </li> -->
          </ul>
        </li>      

        <li :class="{'mm-active':manage_scheduler}" v-if="$gate.isAdmin()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Scheduler</span></a>  
          <ul  class="sub-menu" :class="{'mm-show':manage_scheduler}" style="">
            <li>
              <router-link to="/job-scheduler">Job Scheduler</router-link>
            </li>
          </ul>
        </li>   

        <li :class="{'mm-active':manage_timesheet}" v-if="$gate.isAdmin()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Manage Timesheets</span></a>  
          <ul  class="sub-menu" :class="{'mm-show':manage_timesheet}" style="">
            <!-- <li>
              <router-link to="/admin-user-timesheet">Timesheets</router-link>
            </li> -->
            <li>
              <router-link to="/nadmin-user-timesheet">Timesheet</router-link>
            </li>
            <li>
              <router-link to="/shift-comparison">Shift Comparison</router-link>
            </li>
          </ul>
        </li>   

        <li class="mm-active" v-if="$gate.isUser()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Time Sheet</span></a>  
          <ul  class="sub-menu mm-show" style="">
            <li>
              <router-link to="/user-timesheet">Update Timesheet</router-link>
            </li>
          </ul>
        </li>      

        <li class="{'mm-active':payment_module}" v-if="$gate.isAdmin()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Payment Module</span></a>  
          <ul  class="sub-menu" :class="{'mm-show':payment_module}" style="">
            <li>
              <router-link to="/payments">Payment Module</router-link>
            </li>
          </ul>
        </li>      

        <li class="{'mm-active':report_module}" v-if="$gate.isAdmin()">
          <a href="javascript:void(0);" class="is-parent has-dropdown" >
            <i class="bx bx-home-circle"></i> <span>Reporting Module</span></a>  
          <ul  class="sub-menu" :class="{'mm-show':report_module}" style="">
            <li>
              <router-link to="/reporting">Report Module</router-link>
            </li>
          </ul>
        </li>      

    </ul>
  </div>
  <!-- Sidebar -->
</template>
