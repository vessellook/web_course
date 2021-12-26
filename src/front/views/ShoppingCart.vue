<template>
  <div class="list">

  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "ShoppingCart",
  computed: mapState({
    cart: state => state.cart
  }),
}
</script>

<style scoped>

</style>