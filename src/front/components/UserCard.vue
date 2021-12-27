<template>
  <form class="form">
    <div class="title" v-if="user.id">{{ 'Пользователь ' + user.login }}</div>
    <div class="subtitle" v-if="user.id">Смена пароля</div>
    <div class="subtitle" v-if="!user.id">Создание нового пользователя</div>
    <text-field class="label" v-if="!user.id" label="Логин" v-model="login" mandatory></text-field>
    <text-field class="label" label="Пароль" type="password" v-model="password" mandatory></text-field>
    <common-button :ready="!!password" @submit="changePassword" v-if="user.id" value="Сменить пароль"></common-button>
    <common-button :ready="!!(password && login)" @submit="createUser" v-if="!user.id" value="Зарегистрировать пользователя"></common-button>
  </form>
</template>

<script>
import TextField from "@/components/TextField";
import User from "@/models/User";
import {registerUser, updateUserPassword} from "@/api/user";
import CommonButton from "@/components/CommonButton";

export default {
  name: "UserCard",
  components: {TextField, CommonButton},
  props: {
    user: User
  },
  emits: ['updateUsers'],
  data() {
    return {
      login: '',
      password: ''
    }
  },
  methods: {
    changePassword() {
      let partialEmit = user => this.$emit('updateUsers', user);
      updateUserPassword({
        userId: this.user.id,
        password: this.password,
        token: this.$store.state.token
      }).then(partialEmit, partialEmit);
    },
    createUser() {
      let partialEmit = user => this.$emit('updateUsers', user);
      registerUser({
        user: new User({id: null, role: 'operator', login: this.login}),
        password: this.password,
        token: this.$store.state.token
      }).then(partialEmit, partialEmit);
    }
  }
}
</script>

<style scoped>
.form {
  padding: 5px;
  margin: 10px;
  border: 1px solid #ccc;
  width: 400px;
}

.label {
  margin-bottom: 15px;
}

.title {
  font-size: 1.5em;
  margin-bottom: 15px;
}

.subtitle {
  font-size: 1.2em;
  margin-bottom: 15px;
}
</style>