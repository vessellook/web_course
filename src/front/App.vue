<template>
  <div id="app">
    <div class="header">
        <img class="logo" src="/static/img/logo2.png" alt="Логотип" title="logo">
      <div class="nav">
        <router-link :to="{name: 'CustomerList'}" class="nav__link" v-if="isRegistered">Заказчики</router-link>
        <router-link :to="{name: 'OrderList'}" class="nav__link" v-if="isRegistered">Заказы</router-link>
        <router-link :to="{name: 'TransportationList'}" class="nav__link" v-if="isRegistered">Поставки</router-link>
        <router-link :to="{name: 'UserList'}" class="nav__link" v-if="isRegistered && role === 'director'">Пользователи</router-link>
        <router-link :to="{name: 'Profile'}" class="nav__link" v-if="isRegistered">Профиль</router-link>
        <div class="nav__link" v-if="!isRegistered" @click="showModal">Войти</div>
        <div class="nav__link" v-if="isRegistered" @click="logout">Выйти</div>
      </div>
    </div>
    <div class="main">
      <router-view/>
    </div>
    <base-modal v-show="isShow" @close="closeModal">
      <authentication-form @loggedIn="closeModal"></authentication-form>
    </base-modal>
  </div>
</template>

<script>
import {mapGetters, mapMutations, mapActions, mapState} from 'vuex';
import {INVALIDATE_TOKEN} from "./store/mutations";
import BaseModal from '@/components/BaseModal';
import AuthenticationForm from "@/components/AuthenticationForm";
import {RENEW_TOKEN} from "@/store/actions";

export default {
  name: "App",
  components: {BaseModal, AuthenticationForm},
  data: () => ({
    isShow: false
  }),
  computed: {
    ...mapGetters({
      isRegistered: 'isRegistered'
    }),
    ...mapState({
      finishedLoading: 'finishedLoading',
      role: 'role'
    })
  },
  watch: {
    finishedLoading(value) {
      console.log(1);
      if(value) {
        if(this.$store.isRegistered) {
          this.renewToken();
        } else {
          this.$router.replace('/');
        }
      }
    }
  },
  methods: {
    logout() {
      this.$store.commit(INVALIDATE_TOKEN);
      this.$router.replace('/');
    },
    ...mapActions({renewToken: RENEW_TOKEN}),
    closeModal() {
      this.isShow = false;
    },
    showModal() {
      this.isShow = true;
    }
  }
}
</script>

<style scoped>
.header {
  display: grid;
  grid-template-columns: 100px 1fr;
  width: 100%;
  border-bottom: 1px solid #ccc;
  position: fixed;
  top: 0;
  z-index: 10;
}

.logo {
  width: 100px;
}

.nav {
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav__link:not(:first-child):before {
  content: '|';
  margin: 0 5px;
}

.nav__link {
  text-decoration: none;
  color: #000;
  cursor: pointer;
}

.nav__link:hover {
  color: #28556C;
  text-decoration: underline;
}

.main {
  margin: 120px 10px 0;
}
</style>

<style>
html, body {
  margin: 0;
  padding: 0;
}

.nav__link.router-link-active {
  color: #28556C;
  font-weight: bold;
}
</style>