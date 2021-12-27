<template>
  <div v-show="!isRegistered" class="message">Добрый день. Пожалуйста, войдите в систему</div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "Home",
  computed: mapGetters({isRegistered: 'isRegistered'})
}
</script>

<style scoped>

</style>