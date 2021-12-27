<template>
  <div class="tab-label">Войти</div>
  <form class="login-form">
    <TextField class="label" label="Логин" v-model="login" mandatory></TextField>
    <TextField class="label" type="password" label="Пароль" v-model="password" mandatory></TextField>
    <div class="button-wrap">
      <common-button :ready="ready" value="Войти"
                     @submit="logIn({login: this.login, password: this.password})"></common-button>
    </div>
  </form>
</template>

<script>
import TextField from "@/components/TextField";
import {getToken} from "@/api";
import {UPDATE_TOKEN} from "@/store/mutations";
import CommonButton from "@/components/CommonButton";

export default {
  name: "AuthenticationForm",
  emits: ['loggedIn'],
  components: {CommonButton, TextField},
  data: () => ({
    login: '',
    password: ''
  }),
  computed: {
    ready() {
      return !!this.login && !!this.password;
    }
  },
  methods: {
    logIn({login, password}) {
      getToken({login, password})
          .then(token => this.$store.commit(UPDATE_TOKEN, token))
          .then(() => this.$emit('loggedIn'))
          .finally(this.reset)
    },
    reset() {
      this.login = '';
      this.password = '';
    }
  }
}
</script>

<style scoped>
.tab-label {
  border-bottom: 1px solid #999;
  line-height: 30px;
  text-align: center;
  border-bottom: 4px solid #28556C;
}

.login-form {
  max-width: 100%;
  width: 400px;
  padding: 10px 20px;
}

.label {
  margin-bottom: 5px;
}
</style>