<template>
  <h1 class="subtitle">Смена пароля</h1>
  <text-field class="label" label="Пароль" type="password" v-model="password" mandatory></text-field>
  <common-button :ready="!!password" value="Сменить пароль" @submit="changePassword"></common-button>
</template>

<script>
import TextField from "@/components/TextField";
import CommonButton from "@/components/CommonButton";
import {getUser, updateUserPassword} from "@/api/user";
import {BadStatusError} from "@/api/common";
import {INVALIDATE_TOKEN} from "@/store/mutations";

export default {
  name: "Profile",
  components: {TextField, CommonButton},
  data() {
    return {
      password: null
    }
  },
  mounted() {
    getUser({
      token: this.$store.state.token
    }).then(user => this.login = user.login);
  },
  methods: {
    changePassword() {
      let partialEmit = user => this.$emit('updateUsers', user);
      updateUserPassword({
        password: this.password,
        token: this.$store.state.token
      }).then(partialEmit, partialEmit)
          .catch(error => {
            if(error instanceof BadStatusError) {
              this.$store.commit(INVALIDATE_TOKEN);
              this.$router.replace('/');
            }
          });
    }
  }
}
</script>

<style scoped>
.label {
  margin-bottom: 15px;
}

.subtitle {
  font-size: 1.5em;
  margin-bottom: 15px;
  color: #28556C;
}
</style>