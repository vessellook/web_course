import ProductCatalog from "@/views/ProductCatalog.vue";
import ShoppingCart from "@/views/ShoppingCart";

export default [
  {
    path: "/",
    name: "ProductCatalog",
    component: ProductCatalog,
  },
  {
    path: "/cart",
    name: "ShoppingCart",
    component: ShoppingCart
  }
];