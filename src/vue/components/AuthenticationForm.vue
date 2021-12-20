<template>
  <div class="tab-labels">
    <div class="tab-label" :class="{'tab-label_selected': state === 'login'}"
         @click="switchToAuthorization">Войти
    </div>
    <div class="tab-label" :class="{'tab-label_selected': state === 'registration'}"
         @click="switchToRegistration">Зарегистрироваться
    </div>
  </div>
  <form class="login-form" v-show="state === 'login'">
    <TextField class="label" label="Логин" name="login" v-model="loginUsername" mandatory></TextField>
    <TextField class="label" label="Пароль" mandatory>
      <input name="password" type="password" style="width: 100%;" v-model="loginPassword">
    </TextField>
    <div class="button-wrap">
      <common-button @click="login({login: this.loginUsername, password: this.loginPassword})" value="Войти"></common-button>
    </div>
  </form>
  <form class="registration-form" v-show="state === 'registration'">
    <TextField class="label" label="Имя" name="name" v-model="name" mandatory></TextField>
    <TextField class="label" label="Почта" name="email" v-model="email" mandatory></TextField>
    <TextField class="label" label="Номер телефона" v-model="phoneNumber" name="phoneNumber"></TextField>
    <TextField class="label" label="Пароль" name="password" mandatory>
      <input name="password" type="password" style="width: 100%;" v-model="password">
    </TextField>
    <div class="button-wrap">
      <common-button @click="register({password, email, phoneNumber, name})" value="Зарегистрироваться"></common-button>
    </div>
  </form>
</template>

<script>
import TextField from "@/components/TextField";
import {getToken, registerUser} from "@/api";
import {UPDATE_TOKEN} from "@/store/mutations";
import CommonButton from "@/components/CommonButton";

export default {
  name: "AuthenticationForm",
  emits: ['loggedIn', 'registered'],
  components: {CommonButton, TextField},
  data: () => ({
    state: 'login',
    loginUsername: '',
    loginPassword: '',
    name: '',
    password: '',
    email: '',
    phoneNumber: '',

  }),
  methods: {
    switchToAuthorization() {
      this.state = 'login';
    },
    switchToRegistration() {
      this.state = 'registration';
    },
    register({name, email, phoneNumber, password}) {
      registerUser({name, login: email, email, phoneNumber: phoneNumber || null, password})
          .then(() => this.$emit('registered', {email, password}))
          .finally(this.reset);
    },
    login({login, password}) {
      getToken({login, password})
          .then(token => this.$store.commit(UPDATE_TOKEN, token))
          .then(this.$emit('loggedIn'))
          .finally(this.reset)
    },
    reset() {
      this.email = '';
      this.name = '';
      this.password = '';
      this.state = 'login';
      this.loginUsername = '';
      this.loginPassword = '';
      this.phoneNumber = '';
    }
  }
}
</script>

<style scoped>
.tab-labels {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.tab-label {
  border-bottom: 1px solid #999;
  line-height: 30px;
  text-align: center;
  cursor: pointer;
}

.tab-label:not(:first-of-type) {
  border-left: 1px solid #999;
}

.tab-label_selected {
  border-bottom: 4px solid green;
}

.registration-form,
.login-form {
  max-width: 100%;
  width: 400px;
  padding: 10px 20px;
}

.label {
  margin-bottom: 5px;
}
</style>