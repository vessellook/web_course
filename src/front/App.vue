<template>
  <div id="app">
    <div class="header">
      <router-link to="/"><img class="logo" src="/static/img/logo2.ny.png" alt="Логотип" title="logo"></router-link>
      <div class="nav">
        <router-link :to="{name: 'CustomerList'}" class="nav__link" v-if="isRegistered">Заказчики</router-link>
        <router-link :to="{name: 'OrderList'}" class="nav__link" v-if="isRegistered">Заказы</router-link>
        <router-link :to="{name: 'TransportationList'}" class="nav__link" v-if="isRegistered">Поставки</router-link>
        <router-link :to="{name: 'UserList'}" class="nav__link" v-if="isRegistered && role === 'director'">
          Пользователи
        </router-link>
        <router-link :to="{name: 'Profile'}" class="nav__link" v-if="isRegistered">Профиль</router-link>
        <div class="nav__link" v-if="!isRegistered" @click="showModal">Войти</div>
        <div class="nav__link" v-if="isRegistered" @click="logout">Выйти</div>
      </div>
    </div>
    <div class="main">
      <router-view/>
    </div>
    <base-modal v-if="isShow" @close="closeModal">
      <authentication-form @loggedIn="closeModal"></authentication-form>
    </base-modal>
    <base-modal v-if="wasRegistered && !isRegistered" @close="$router.replace('/'); wasRegistered = false">
      <div class="message">Сессия закончилась</div>
    </base-modal>
  </div>
</template>

<script>
import {mapGetters, mapActions, mapState, useStore} from 'vuex';
import {INVALIDATE_TOKEN} from "./store/mutations";
import BaseModal from '@/components/BaseModal';
import AuthenticationForm from "@/components/AuthenticationForm";
import {RENEW_TOKEN} from "@/store/actions";
import {useIdle, useMouse, debounceFilter, watchWithFilter} from "@vueuse/core";
import {reactive, watch} from "vue";

export default {
  name: "App",
  components: {BaseModal, AuthenticationForm},
  setup() {
    const store = useStore();
    const {idle} = useIdle(process.env.INACTIVITY_TIMEOUT * 1000);
    const mouse = reactive(useMouse());
    watch(idle, (idle) => idle && store.state.token && store.dispatch(RENEW_TOKEN));
    watchWithFilter(
        mouse,
        () => store.state.token && store.dispatch(RENEW_TOKEN),
        {eventFilter: debounceFilter(10 * 1000, {maxWait: process.env.INACTIVITY_TIMEOUT * 500})}
    );
    return {mouse, idle};
  },
  data() {
    return {
      isShow: false,
      wasRegistered: this.$store.getters.isRegistered
    };
  }
  ,
  computed: {
    ...mapGetters({
          isRegistered: 'isRegistered'
        }),
    ...
        mapState({
          finishedLoading: 'finishedLoading',
          role: 'role'
        })
  }
  ,
  watch: {
    finishedLoading(value) {
      if (value) {
        this.updateToken();
      }
    }
    ,
    isRegistered(value) {
      this.wasRegistered = this.wasRegistered || value;
    }
    ,

  }
  ,
  mounted() {
    if (this.finishedLoading) {
      this.updateToken();
    }
  }
  ,
  methods: {
    ...
        mapActions({renewToken: RENEW_TOKEN}),
    logout() {
      this.$store.commit(INVALIDATE_TOKEN);
      this.wasRegistered = false;
      this.$router.replace('/');
    }
    ,
    closeModal() {
      this.isShow = false;
    }
    ,
    showModal() {
      this.isShow = true;
    }
    ,
    updateToken() {
      console.log('state on startup: ', this.$store.state);
      if (this.$store.getters.isRegistered) {
        this.renewToken();
      } else {
        this.$router.replace('/');
      }
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
  background: white;
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

.message {
  display: flex;
  height: 100%;
  align-items: center;
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