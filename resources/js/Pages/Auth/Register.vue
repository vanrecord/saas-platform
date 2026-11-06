<template>
  <Head title="Register · Dental Care Management Portal" />

  <div class="register-wrapper">
    <main class="shell">
      <!-- Left Hero Column -->
      <section class="welcome" aria-label="Welcome">
        <div class="kicker">DENTAL CARE · MANAGEMENT PORTAL</div>
        <h1>WELCOME</h1>
        <h2>YOUR PRACTICE. ONE SIMPLE PLACE.</h2>
        <p>
          Sign up to manage patients, dental charts, treatment notes, and your clinic workflow securely.
        </p>

        <div class="features">
          <span class="pill">Patient records</span>
          <span class="pill">Interactive dental charts</span>
          <span class="pill">Role-based access</span>
        </div>
      </section>

      <!-- Right Register Form Column -->
      <section class="login-side" aria-label="Register form">
        <div class="card">
          <!-- Dental Tooth Brandmark Icon -->
          <div class="brandmark" aria-hidden="true">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M7.3 3.2c-2.4 0-4 2-3.6 5 .5 4 2.2 11.6 4.4 12.1 1.9.5 1.7-5.1 3.9-5.1s2 5.6 3.9 5.1c2.2-.5 3.9-8.1 4.4-12.1.4-3-1.2-5-3.6-5-1.8 0-2.9 1.1-4.7 1.1S9.1 3.2 7.3 3.2Z"
              />
            </svg>
          </div>

          <h3>Register</h3>
          <p class="sub">Create your account to start managing your dental clinic.</p>

          <form @submit.prevent="submit" novalidate>
            <!-- Name Field -->
            <div class="field">
              <label for="name">Name</label>
              <div
                class="input-wrap"
                :class="{ 'has-error': form.errors.name }"
              >
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  name="name"
                  autocomplete="name"
                  placeholder="Enter your full name"
                  required
                  autofocus
                  class="border-0 ring-0 outline-none shadow-none focus:ring-0 focus:outline-none focus:border-transparent focus:shadow-none bg-transparent"
                />
              </div>
              <div v-if="form.errors.name" class="error-msg" role="alert">
                {{ form.errors.name }}
              </div>
            </div>

            <!-- Email Field -->
            <div class="field">
              <label for="email">Email</label>
              <div
                class="input-wrap"
                :class="{ 'has-error': form.errors.email }"
              >
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  name="email"
                  autocomplete="email"
                  placeholder="Enter your email"
                  required
                  class="border-0 ring-0 outline-none shadow-none focus:ring-0 focus:outline-none focus:border-transparent focus:shadow-none bg-transparent"
                />
              </div>
              <div v-if="form.errors.email" class="error-msg" role="alert">
                {{ form.errors.email }}
              </div>
            </div>

            <!-- Password Field -->
            <div class="field">
              <label for="password">Password</label>
              <div
                class="input-wrap"
                :class="{ 'has-error': form.errors.password }"
              >
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  name="password"
                  autocomplete="new-password"
                  placeholder="Create a password"
                  required
                  class="border-0 ring-0 outline-none shadow-none focus:ring-0 focus:outline-none focus:border-transparent focus:shadow-none bg-transparent"
                />
                <button
                  type="button"
                  class="show"
                  @click="showPassword = !showPassword"
                  :aria-label="showPassword ? 'Hide password' : 'Show password'"
                >
                  {{ showPassword ? 'HIDE' : 'SHOW' }}
                </button>
              </div>
              <div v-if="form.errors.password" class="error-msg" role="alert">
                {{ form.errors.password }}
              </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="field">
              <label for="password_confirmation">Confirm Password</label>
              <div
                class="input-wrap"
                :class="{ 'has-error': form.errors.password_confirmation }"
              >
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  name="password_confirmation"
                  autocomplete="new-password"
                  placeholder="Confirm your password"
                  required
                  class="border-0 ring-0 outline-none shadow-none focus:ring-0 focus:outline-none focus:border-transparent focus:shadow-none bg-transparent"
                />
                <button
                  type="button"
                  class="show"
                  @click="showConfirmPassword = !showConfirmPassword"
                  :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'"
                >
                  {{ showConfirmPassword ? 'HIDE' : 'SHOW' }}
                </button>
              </div>
              <div
                v-if="form.errors.password_confirmation"
                class="error-msg"
                role="alert"
              >
                {{ form.errors.password_confirmation }}
              </div>
            </div>

            <!-- Terms & Policy Checkbox (if enabled) -->
            <div
              v-if="$page.props.jetstream?.hasTermsAndPrivacyPolicyFeature"
              class="terms-row"
            >
              <label class="terms-label">
                <input
                  id="terms"
                  v-model="form.terms"
                  type="checkbox"
                  name="terms"
                  required
                />
                <span>
                  I agree to the
                  <a target="_blank" :href="route('terms.show')">Terms of Service</a>
                  and
                  <a target="_blank" :href="route('policy.show')">Privacy Policy</a>
                </span>
              </label>
              <div v-if="form.errors.terms" class="error-msg" role="alert">
                {{ form.errors.terms }}
              </div>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              class="primary"
              :disabled="form.processing"
              :class="{ 'is-loading': form.processing }"
            >
              Register
            </button>

            <!-- Bottom redirect to Login -->
            <div class="foot">
              Already registered?
              <Link :href="route('login')" class="signin-link">
                Sign in
              </Link>
            </div>
          </form>
        </div>
      </section>
    </main>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { Head, Link } from '@inertiajs/inertia-vue3'

export default defineComponent({
  components: {
    Head,
    Link,
  },
  data() {
    return {
      showPassword: false,
      showConfirmPassword: false,
      form: this.$inertia.form({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
      }),
    }
  },
  methods: {
    submit() {
      const targetUrl = typeof this.route === 'function' ? this.route('register') : '/register'
      this.form.post(targetUrl, {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    },
  },
})
</script>

<style scoped>
:root {
  --blue: #087bd0;
  --blue2: #0063a8;
  --ink: #073f70;
  --muted: #536679;
  --line: #d6e0e9;
}

* {
  box-sizing: border-box;
}

.register-wrapper {
  margin: 0;
  min-height: 100vh;
  background: #f4f7fb;
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  color: #12324c;
  display: grid;
  place-items: center;
  padding: 28px;
}

.shell {
  width: min(1120px, 100%);
  min-height: 580px;
  border-radius: 24px;
  overflow: hidden;
  display: grid;
  grid-template-columns: 1.28fr 0.95fr;
  background: linear-gradient(115deg, #0b8fe8 0%, #0875c9 45%, #064f87 100%);
  box-shadow: 0 24px 65px rgba(9, 54, 94, 0.2);
  position: relative;
}

.welcome {
  position: relative;
  overflow: hidden;
  padding: clamp(36px, 6vw, 82px);
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  isolation: isolate;
}

.welcome:before,
.welcome:after {
  content: "";
  position: absolute;
  border-radius: 50%;
  z-index: -1;
  pointer-events: none;
}

.welcome:before {
  width: 660px;
  height: 660px;
  left: -330px;
  top: -185px;
  background: rgba(0, 57, 126, 0.18);
  box-shadow: inset -35px 0 0 rgba(255, 255, 255, 0.03);
}

.welcome:after {
  width: 470px;
  height: 240px;
  left: -120px;
  bottom: -120px;
  background: linear-gradient(130deg, rgba(0, 168, 255, 0.72), rgba(0, 68, 143, 0.15));
  transform: rotate(-17deg);
}

.kicker {
  font-size: 13px;
  font-weight: 750;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  opacity: 0.82;
  margin-bottom: 22px;
}

.welcome h1 {
  font-size: clamp(38px, 5vw, 57px);
  line-height: 1;
  letter-spacing: 0.055em;
  margin: 0 0 18px;
  font-weight: 850;
  color: #ffffff;
}

.welcome h2 {
  font-size: 15px;
  letter-spacing: 0.13em;
  margin: 0 0 18px;
  font-weight: 750;
  color: rgba(255, 255, 255, 0.95);
}

.welcome p {
  line-height: 1.9;
  max-width: 390px;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.91);
  margin: 0;
}

.features {
  margin-top: 34px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.pill {
  font-size: 11px;
  padding: 8px 12px;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  background: rgba(255, 255, 255, 0.09);
  letter-spacing: 0.025em;
  color: #ffffff;
}

.login-side {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px;
  background: rgba(3, 58, 99, 0.16);
}

.card {
  width: min(100%, 410px);
  padding: 30px 32px 24px;
  border-radius: 24px;
  background: #ffffff;
  box-shadow: 0 18px 38px rgba(0, 25, 48, 0.2);
}

.brandmark {
  width: 43px;
  height: 43px;
  border-radius: 13px;
  background: #e6f4ff;
  display: grid;
  place-items: center;
  color: #0878c8;
  margin-bottom: 12px;
}

.brandmark svg {
  width: 25px;
  height: 25px;
}

.card h3 {
  margin: 0;
  color: #073f70;
  font-size: 28px;
  font-weight: 800;
  letter-spacing: 0.015em;
}

.sub {
  margin: 4px 0 18px;
  color: #6a7886;
  font-size: 11px;
  line-height: 1.6;
}

.field {
  margin: 0 0 11px;
}

.field label {
  display: block;
  font-size: 12px;
  color: #425469;
  margin: 0 0 5px;
  font-weight: 650;
}

.input-wrap {
  display: flex;
  align-items: center;
  border: 1px solid #d6e0e9;
  border-radius: 12px;
  height: 44px;
  transition: 0.2s;
  box-shadow: 0 1px 2px rgba(20, 54, 85, 0.025);
  background: #ffffff;
}

.input-wrap:focus-within {
  border-color: #1488d8;
  box-shadow: 0 0 0 3px rgba(20, 136, 216, 0.12);
}

.input-wrap input,
.input-wrap input:focus,
.input-wrap input:focus-visible,
.input-wrap input:active {
  min-width: 0;
  width: 100%;
  border: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  -webkit-appearance: none;
  background: transparent !important;
  padding: 0 14px;
  font-size: 13px;
  color: #17354d;
  --tw-ring-offset-shadow: 0 0 #0000 !important;
  --tw-ring-shadow: 0 0 #0000 !important;
  --tw-shadow: 0 0 #0000 !important;
  --tw-ring-color: transparent !important;
}

.input-wrap input::placeholder {
  color: #96a3af;
}

.input-wrap.has-error {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}

.error-msg {
  color: #dc2626;
  font-size: 11px;
  margin-top: 4px;
  font-weight: 550;
}

.show {
  border: 0;
  background: transparent;
  color: #07578f;
  font-size: 10px;
  letter-spacing: 0.04em;
  font-weight: 800;
  padding: 10px 12px;
  cursor: pointer;
  flex-shrink: 0;
}

.terms-row {
  margin: 10px 0 14px;
}

.terms-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: #647384;
  cursor: pointer;
}

.terms-label input {
  accent-color: #0879ca;
  width: 14px;
  height: 14px;
  margin: 0;
}

.terms-label a {
  color: #07588f;
  text-decoration: underline;
  font-weight: 600;
}

.primary {
  width: 100%;
  height: 45px;
  border: 0;
  border-radius: 11px;
  color: white;
  font-weight: 800;
  letter-spacing: 0.03em;
  background: linear-gradient(100deg, #0786d8, #0063a9);
  box-shadow: 0 5px 12px rgba(0, 105, 180, 0.22);
  cursor: pointer;
  transition: transform 0.16s, filter 0.16s;
  font-size: 13px;
  margin-top: 6px;
}

.primary:hover:not(:disabled) {
  filter: brightness(1.06);
  transform: translateY(-1px);
}

.primary:active:not(:disabled) {
  transform: translateY(0);
}

.primary:disabled,
.primary.is-loading {
  opacity: 0.6;
  cursor: not-allowed;
}

.foot {
  border-top: 1px solid #edf1f5;
  margin-top: 18px;
  padding-top: 14px;
  text-align: center;
  font-size: 11px;
  color: #6d7b88;
}

.signin-link {
  color: #07588f;
  text-decoration: none;
  font-weight: 700;
  margin-left: 4px;
}

.signin-link:hover {
  text-decoration: underline;
}

@media (max-width: 760px) {
  .register-wrapper {
    padding: 14px;
  }

  .shell {
    grid-template-columns: 1fr;
    max-width: 510px;
  }

  .welcome {
    padding: 28px 30px 25px;
    min-height: 235px;
  }

  .welcome h1 {
    font-size: 39px;
  }

  .welcome p {
    font-size: 12px;
    line-height: 1.7;
  }

  .kicker {
    margin-bottom: 15px;
  }

  .features {
    margin-top: 18px;
  }

  .login-side {
    padding: 20px 14px 26px;
  }

  .card {
    padding: 24px 20px 20px;
  }

  .welcome:before {
    width: 420px;
    height: 420px;
    left: -235px;
    top: -145px;
  }
}

@media (prefers-reduced-motion: reduce) {
  * {
    transition: none !important;
    scroll-behavior: auto !important;
  }
}
</style>