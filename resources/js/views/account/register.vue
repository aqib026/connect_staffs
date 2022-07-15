<script>
/**
 * Register component
 */
export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      phone_number:"",
      password_confirmation: "",
      isRegisterError: false,
      registerSuccess: false,
    };
  },
  props: {
    submitUrl: {
      type: String,
      required: true,
    },
    regError: {
      type: String,
      required: false,
      default: () => null,
    },
  },
  mounted() {
    this.isRegisterError = !!this.regError;
  },
};
</script>

<template>
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
      <div class="card overflow-hidden">
        <div class="bg-soft bg-primary">
          <div class="row">
            <div class="col-7">
              <div class="text-primary p-4">
                <h5 class="text-primary">Free Register</h5>
                <p>Get your free Kaoskastaffing account now.</p>
              </div>
            </div>
            <div class="col-5 align-self-end">
              <img src="/images/profile-img.png" alt class="img-fluid" />
            </div>
          </div>
        </div>
        <div class="card-body pt-0">
          <div>
            <a href="/">
              <div class="avatar-md profile-user-wid mb-4">
                <span class="avatar-title rounded-circle bg-light">
                  <img src="/images/logo.svg" alt class="rounded-circle" height="34" />
                </span>
              </div>
            </a>
          </div>

          <b-alert
            v-model="registerSuccess"
            class="mt-3"
            variant="success"
            dismissible
          >Registration successfull.</b-alert>

          <b-alert v-model="isRegisterError" class="mt-3" variant="danger" dismissible>{{regError}}</b-alert>

          <b-form class="p-2" :action="submitUrl" method="POST">
            <slot />
            <b-form-group id="email-group" label="Name" label-for="name" class="mb-3">
              <b-form-input
                id="name"
                v-model="name"
                name="name"
                type="text"
                placeholder="Enter name"
                required
              ></b-form-input>
            </b-form-group>

            <b-form-group id="fullname-group" label="Email" label-for="email" class="mb-3">
              <b-form-input
                id="email"
                name="email"
                v-model="email"
                type="text"
                placeholder="Enter email"
                required
              ></b-form-input>
            </b-form-group>
            

            <b-form-group id="fullname-group" label="Phone Number" label-for="phone_number" role="group" class="mb-3">
              <div class="input-group-prepend" style="width:15%;display:inline-block;">
                <div class="input-group-text" style="background-color:#fff;width:100%;border-right:0px;display:inline-block;">+44</div>
              </div>
              <b-form-input
                id="phone_number"
                name="phone_number"
                v-model="phone_number"
                type="text"
                v-mask="'#### ######'"
                style="width: 85%;display: inline-block;margin-left: -8px;"
                placeholder="4321 123456"
                required
              ></b-form-input>
            </b-form-group>

            

            <b-form-group id="password-group" label="Password" label-for="password" class="mb-3">
              <b-form-input
                id="password"
                v-model="password"
                name="password"
                type="password"
                placeholder="Enter password"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group label="Confirm Password" label-for="password-confirm" class="mb-3">
              <b-form-input
                id="password-confirm"
                v-model="password_confirmation"
                name="password_confirmation"
                type="password"
                placeholder="Confirm password"
                required
              ></b-form-input>
            </b-form-group>

            <div class="mt-4 d-grid">
              <b-button type="submit" variant="primary" class="btn-block">Register</b-button>
            </div>


            <div class="mt-4 text-center">
              <p class="mb-0">
                By registering you agree to the Kaoskastaffing
                <a
                  href="javascript: void(0);"
                  class="text-primary"
                >Terms of Use</a>
              </p>
            </div>
          </b-form>
        </div>
        <!-- end card-body -->
      </div>
      <!-- end card -->

      <div class="mt-5 text-center">
        <p>
          Already have an account ?
          <a href="/login" class="fw-medium text-primary">Login</a>
        </p>
        <p>
          © {{ new Date().getFullYear() }} Kaoskastaffing.
        </p>
      </div>
    </div>
    <!-- end col -->
  </div>
</template>

<style lang="scss" module></style>
