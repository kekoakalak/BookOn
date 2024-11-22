<template>
  <div
    class="core-login row full-height full-width justify-center items-center relative bg-white"
  >
    <div class="row justify-center items-center" style="flex-grow: 1">
      <q-form @submit.prevent="login" class="col-12 q-px-lg">
        <transition
          appear
          name="fade"
          @before-enter="beforeEnter"
          @enter="enter"
          @leave="leave"
        >
          <div
            v-if="errorMessage"
            class="errorMessage text-16 q-px-md q-py-sm q-mb-md rounded-borders text-center"
            role="alert"
          >
            {{ errorMessage }}
            <q-icon
              name="close"
              class="q-pl-lg"
              size="xs"
              @click="errorMessage = ''"
            />
          </div>
        </transition>

        <div class="text-weight-bold text-h5 q-pa-none">
          Welcome!
          <br />
          <div
            class="text-weight-normal q-pb-lg q-pl-xl q-pt-none text-teal"
            style="font-size: 0.875rem"
          >
            to bookOn
          </div>
        </div>

        <div class="q-pt-md text-white">
          <q-input
            dense
            borderless
            v-model="form.username"
            placeholder="Username"
            class="log-input q-pb-none q-pb-sm q-mb-md"
            :class="!errorMessage ? 'border-bottom-white' : 'border-bottom-red'"
            :disable="state.isLoading"
          >
            <template v-slot:prepend>
              <q-icon name="person" class="q-px-md" />
            </template>
          </q-input>

          <q-input
            dense
            borderless
            :type="isPwd ? 'password' : 'text'"
            v-model="form.password"
            placeholder="Password"
            class="log-input q-pb-none q-pb-sm"
            :class="!errorMessage ? 'border-bottom-white' : 'border-bottom-red'"
            :disable="state.isLoading"
          >
            <template v-slot:prepend>
              <q-icon name="lock" class="q-px-md" />
            </template>
            <template v-slot:append>
              <q-icon
                :name="isPwd ? 'visibility_off' : 'visibility'"
                class="cursor-pointer q-px-md"
                @click="isPwd = !isPwd"
              />
            </template>
          </q-input>
        </div>

        <div class="q-pt-lg">
          <q-btn
            rounded
            flat
            dense
            no-caps
            class="log-btn full-width q-py-sm"
            label="Log In"
            :disable="
              disabledBtn || !form.username || !form.password || state.isLoading
            "
            :loading="state.isLoading"
            :loading-label="state.isLoading ? 'Logging in...' : ''"
            @click="login"
          />
        </div>
        <div class="text-center q-pa-sm">Forgot password?</div>
      </q-form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { api } from "../boot/axios";

const form = ref({
  username: "",
  password: "",
});
const errorMessage = ref(null);
const isPwd = ref(true);
const disabledBtn = ref(false);
const router = useRouter();

const state = ref({
  isLoading: false,
});


// Transition hooks
const beforeEnter = (el) => {
  el.style.opacity = 0;
};
const enter = (el, done) => {
  el.style.transition = "opacity 0.5s";
  el.style.opacity = 1;
  done();
};
const leave = (el, done) => {
  el.style.transition = "opacity 0.5s";
  el.style.opacity = 0;
  done();
};


const login = async () => {
  try {
    state.value.isLoading = true; // Set loading state
    console.log("Sending login request...");

    const response = await api.post("/auth/login.php", {
      email: form.value.username,
      password: form.value.password,
    });

    console.log("Login response:", response); // Log the full response

    if (response.data.success) {
      console.log("Logged in user data:", response.data);
      sessionStorage.setItem("user_id", response.data.user_id);
      sessionStorage.setItem("user_type", response.data.user_type);

      if (response.data.user_type === "provider") {
        sessionStorage.setItem("provider_id", response.data.user_id);
      }

      // Redirect based on user type
      if (response.data.user_type === "provider") {
        router.push({ name: "Home" });
      } else {
        router.push({ name: "Services" });
      }
    } else {
      errorMessage.value = response.data.error || "Login failed";
      state.value.isLoading = false;
    }
  } catch (error) {
    console.error("Error during login:", error);
    errorMessage.value = "An error occurred during login.";
    state.value.isLoading = false;
  }
};
</script>

<style lang="scss" scoped>
.full-height {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  .log-input {
    width: 100%;
  }
  .log-btn {
    margin-top: 20px;
    background-color: teal;
    color: #ffffff;
  }
  .log-input {
    border: solid 1px teal;
    border-radius: 10px;
    padding: 0;
  }
  .log-btn {
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .log-btn[disabled] {
    background-color: rgba(0, 128, 128, 0.8);
    color: #ffffff;
  }

  .full-width {
    width: 100%;
  }
  .text-black {
    color: #000;
  }
  .text-white {
    color: #fff;
  }
  .errorMessage {
    top: 20px;
    border: solid 1px red;
    color: red;
  }
}
</style>
