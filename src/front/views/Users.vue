<template>
  <h1 class="title">Пользователи</h1>
  <common-list :propsArray="propsArray" @clicked="openPopup(users[$event])"></common-list>
  <common-button value="Добавить пользователя" @submit="openPopupForNewUser"></common-button>
  <base-modal v-if="currentUser" @close="closePopup">
    <user-card :user="currentUser" @update-customers="updateCustomers"></user-card>
  </base-modal>
</template>

<script>
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";
import BaseModal from "@/components/BaseModal";
import UserCard from "@/components/UserCard";
import User from "@/models/User";
import {getUsers} from "@/api/user";

export default {
  name: "Users",
  components: {CommonList, CommonButton, BaseModal, UserCard},
  props: {
    id: {
      default: null
    }
  },
  data() {
    return {
      users: []
    }
  },
  computed: {
    propsArray() {
      return this.users.map((user, index) => ({
        key: index,
        label: user.login,
        disabled: user.role === 'director'
      }));
    },
    currentUser() {
      if (this.id === 'new') {
        return new User({
          id: null,
          role: 'operator',
          login: ''
        });
      } else if (this.id && this.users) {
        return this.users[this.id - 1];
      } else {
        return null;
      }
    }
  },
  mounted() {
    getUsers(this.$store.state.token).then(users => this.users = users);
  },
  methods: {
    closePopup() {
      if (this.id !== null) {
        this.$router.push({name: 'UserList'});
      }
    },
    openPopup(user) {
      this.$router.push({name: 'UserList', params: {id: user.id}});
    },
    openPopupForNewUser() {
      this.$router.push({name: 'UserList', params: {id: 'new'}});
    },
    updateCustomers() {
      getUsers(this.$store.state.token)
          .then(users => this.users = users)
          .finally(this.closePopup);
    }
  }
}
</script>

<style scoped>
.title {
  font-size: 1.5em;
  color: #28556C;
}
</style>