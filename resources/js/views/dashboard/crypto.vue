<script>
import simplebar from "simplebar-vue";

import Layout from "../../layouts/main";
import PageHeader from "../../components/page-header";
import ChartData from "./data-crypto";

export default {
    components: {
        Layout,
        PageHeader,
        simplebar
    },
    data() {
        return {
            title: "Kaoska Staffing",
            items: [
                {
                    text: "Dashboards",
                    href: "javascript:void(0)"
                },
                {
                    text: "Crypto",
                    active: true
                }
            ],
            selection: "one_year"
        };
    },
    methods: {

    }
};
</script>
<template>
    <Layout>
        <PageHeader :title="title" :items="items" />
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <b-dropdown
                            class="float-end ms-2"
                            variant="white"
                            right
                            toggle-class="text-muted p-0"
                            menu-class="dropdown-menu-end"
                            >
                            <template v-slot:button-content>
                                <i class="mdi mdi-dots-horizontal font-size-18"></i>
                            </template>
                            <b-dropdown-item>Action</b-dropdown-item>
                            <b-dropdown-item>Another action</b-dropdown-item>
                            <b-dropdown-item>Something else here</b-dropdown-item>
                        </b-dropdown>
                        <div>
                            <div class="mb-4 me-3">
                                <i
                                    class="mdi mdi-account-circle text-primary h1"
                                ></i>
                            </div>

                            <div>
                                <h5>Henry Wells</h5>
                                <p class="text-muted mb-1">
                                    henrywells@abc.com
                                </p>
                                <p class="text-muted mb-0">Id no: #SK0234</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-sm-6">
                                <div>
                                    <p class="fw-medium mb-2">Balance :</p>
                                    <h4>$ 6134.39</h4>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mt-4 mt-sm-0">
                                    <p class="fw-medium mb-2">Coin :</p>
                                    <div
                                        class="d-inline-flex align-items-center mt-1"
                                    >
                                        <a
                                            href="javascript: void(0);"
                                            class="m-1"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Bitcoin"
                                        >
                                            <div class="avatar-xs">
                                                <span
                                                    class="avatar-title rounded-circle bg-soft bg-warning text-warning font-size-18"
                                                >
                                                    <i
                                                        class="mdi mdi-bitcoin"
                                                    ></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a
                                            href="javascript: void(0);"
                                            class="m-1"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Ethereum"
                                        >
                                            <div class="avatar-xs">
                                                <span
                                                    class="avatar-title rounded-circle bg-soft bg-primary text-primary font-size-18"
                                                >
                                                    <i
                                                        class="mdi mdi-ethereum"
                                                    ></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a
                                            href="javascript: void(0);"
                                            class="m-1"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Litecoin"
                                        >
                                            <div class="avatar-xs">
                                                <span
                                                    class="avatar-title rounded-circle bg-soft bg-info text-info font-size-18"
                                                >
                                                    <i
                                                        class="mdi mdi-litecoin"
                                                    ></i>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-top">
                        <div class="text-center">
                            <a href="#" class="btn btn-outline-light me-2 w-md"
                                >Deposit</a
                            >
                            <a href="#" class="btn btn-primary me-2 w-md"
                                >Buy / Sell</a
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div>
                        <div class="row">
                            <div class="col-lg-9 col-sm-8">
                                <div class="p-4">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p>Kaoskastaffing Crypto Dashboard</p>

                                    <div class="text-muted">
                                        <p class="mb-1">
                                            <i
                                                class="mdi mdi-circle-medium align-middle text-primary me-1"
                                            ></i>
                                            If several languages coalesce
                                        </p>
                                        <p class="mb-1">
                                            <i
                                                class="mdi mdi-circle-medium align-middle text-primary me-1"
                                            ></i>
                                            Sed ut perspiciatis unde
                                        </p>
                                        <p class="mb-0">
                                            <i
                                                class="mdi mdi-circle-medium align-middle text-primary me-1"
                                            ></i>
                                            It would be necessary
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-4 align-self-center">
                                <div>
                                    <img
                                        src="/images/crypto/features-img/img-1.png"
                                        alt=""
                                        class="img-fluid d-block"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-4">
                                    <i
                                        class="mdi mdi-bitcoin h2 text-warning align-middle mb-0 me-3"
                                    ></i>
                                    Bitcoin
                                </p>

                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <h5>$ 9134.39</h5>
                                            <p
                                                class="text-muted text-truncate mb-0"
                                            >
                                                + 0.0012 ( 0.2 % )
                                                <i
                                                    class="mdi mdi-arrow-up ml-1 text-success"
                                                ></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-4">
                                    <i
                                        class="mdi mdi-ethereum h2 text-primary align-middle mb-0 me-3"
                                    ></i>
                                    Ethereum
                                </p>

                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <h5>$ 245.44</h5>
                                            <p
                                                class="text-muted text-truncate mb-0"
                                            >
                                                - 4.102 ( 0.1 % )
                                                <i
                                                    class="mdi mdi-arrow-down ml-1 text-danger"
                                                ></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <p class="text-muted mb-4">
                                    <i
                                        class="mdi mdi-litecoin h2 text-info align-middle mb-0 me-3"
                                    ></i>
                                    litecoin
                                </p>

                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <h5>$ 63.61</h5>
                                            <p
                                                class="text-muted text-truncate mb-0"
                                            >
                                                + 1.792 ( 0.1 % )
                                                <i
                                                    class="mdi mdi-arrow-up ml-1 text-success"
                                                ></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6">
  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->


        <!-- end row -->

        <!-- end row -->
    </Layout>
</template>
