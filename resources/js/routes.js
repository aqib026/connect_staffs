export default [
    { path: '/', name: 'dashboard', component: require('./views/JobScheduler.vue').default },
    { path: '/user-timesheet', name: 'userTimeSheet', component: require('./views/UserTimeSheet.vue').default },
    { path: '/users', name: 'users', component: require('./views/Users.vue').default },
    { path: '/profile/:id', name: 'profile', component: require('./views/contacts/contacts-profile.vue').default },
    { path: '/administrators', name: 'administrators', component: require('./views/Administrators.vue').default },
    { path: '/customers', name: 'customers', component: require('./views/Customers.vue').default },
    { path: '/jobs', name: 'jobs', component: require('./views/Jobs.vue').default },
    {
        name: 'edit',
        path: "/user/:id",
        component: require("./views/EditUser.vue").default
    },
    {
        name: 'edit_job',
        path: "/job/:id",
        component: require("./views/EditJob.vue").default
    },
    {
        name: 'edit_customer',
        path: "/customer/:id",
        component: require("./views/EditCustomer.vue").default
    },
    {
        name: 'job-scheduler',
        path: "/job-scheduler",
        component: require("./views/JobScheduler.vue").default
    },
    { path: '/admin-user-timesheet', name: 'adminUserTimeSheet', component: require('./views/AdminUserTimeSheet.vue').default },
    { path: '/nadmin-user-timesheet', name: 'adminUserTimeSheet', component: require('./views/NAdminUserTimeSheet.vue').default },
    { path: '/shift-comparison', name: 'shiftComparison', component: require('./views/ShiftComparison.vue').default },
    {
        name: 'payments',
        path: "/payments",
        component: require("./views/Payment.vue").default
    },
    {
        name: 'calendar',
        path: "/calendar",
        component: require("./views/calendar/calendar.vue").default
    },
    {
        name: 'reporting',
        path: "/reporting",
        component: require("./views/Reporting.vue").default
    },


];