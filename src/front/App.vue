<template>
  <div id="app">
    <div class="header">
      <router-link to="/"><img class="logo" src="/static/img/logo.png" alt="На главную" title="logo"></router-link>
      <div class="nav">
        <router-link class="nav__link" :to="{name: 'ProductCatalog'}">Каталог</router-link>
        <div class="nav__link" v-if="!isRegistered" @click="showModal">Войти</div>
        <router-link class="nav__link" v-if="isRegistered" :to="{name: 'ShoppingCart'}">В корзину</router-link>
        <div class="nav__link" v-if="isRegistered" @click="logout">Выйти</div>
      </div>
    </div>
    <div class="main">
      <router-view/>
    </div>
    <base-modal :isShow="isShow" @close="closeModal">
      <authentication-form @loggedIn="closeModal" @registered="closeModal"></authentication-form>
    </base-modal>
  </div>
</template>

<script>
import {mapGetters, mapMutations, mapState} from 'vuex';
import {HIDE_LOGIN_MODAL, INVALIDATE_TOKEN, SHOW_LOGIN_MODAL} from "./store/mutations";
import BaseModal from '@/components/BaseModal';
import AuthenticationForm from "@/components/AuthenticationForm";

export default {
  name: "App",
  components: {BaseModal, AuthenticationForm},
  computed: {
    ...mapState({
      isShow: 'isLoginModalShown'
    }),
    ...mapGetters({
      isRegistered: 'isRegistered'
    }),
  },
  methods: mapMutations({
    logout: INVALIDATE_TOKEN,
    showModal: SHOW_LOGIN_MODAL,
    closeModal: HIDE_LOGIN_MODAL
  })
}
</script>

<style scoped>
.header {
  display: grid;
  grid-template-columns: 100px 1fr;
  background: #eee;
  width: 100%;
  border-bottom: 1px solid #000;
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
  color: green;
  cursor: pointer;
}

.nav__link:hover {
  color: limegreen;
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
</style>