<template>
  <div class="list">
    <product-card v-for="product in products" :key="product.id" :="product" @addToCart="addToCart(product)"></product-card>
  </div>
</template>

<script>
import ProductCard from "./ProductCard";
import Product from "@/models/Product";

export default {
  name: "ProductList",
  components: {ProductCard},
  emits: ['register'],
  props: {
    products: {
      type: Array,
      validator(products) {
        return products.reduce((prev, current) => prev && current instanceof Product, true);
      }
    }
  },
  methods: {
    addToCart(product) {
      if(this.$store.getters.isRegistered) {
      } else {
        console.log('x');
        this.$emit('register');
      }
    }
  }
}
</script>

<style scoped>
</style>