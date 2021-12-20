<template>
<product-list v-bind:products="products" @register="showModal"></product-list>
</template>

<script>
import ProductList from "../components/ProductList";
import {fetchProducts} from "@/api";
import {SHOW_LOGIN_MODAL} from "@/store/mutations";
import {mapMutations} from "vuex";
export default {
  name: "ProductCatalog",
  components: {ProductList},
  data: () => ({
    products: [], /** @var {Product[]} products */
  }),
  created() {
    console.log(1);
    fetchProducts()
        .then(products => this.products = products)
        .catch(error => { console.log(error); console.log(4)});
  },
  mounted() {
    console.log(3);
  },
  methods: mapMutations({
    showModal: SHOW_LOGIN_MODAL
  })
}
</script>

<style scoped>

</style>