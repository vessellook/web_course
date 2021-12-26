<template>
  <div class="product">
    <safe-image class="product__image" alt="Без изображения" :title="name" color="lightgreen"></safe-image>
    <div class="product__price">{{ preparedPrice }}</div>
    <div class="product__name">{{ name }}</div>
    <div class="product__uid">{{ uid }}</div>
    <!--    <label class="product__number-field">-->
    <!--      <span class="product__number-label">Количество</span>-->
    <!--      <input type="number" class="product__number" v-bind:min="1" v-bind:max="count" value="1">-->
    <!--    </label>-->
    <div class="product__button-wrap">
      <common-button @click="addToCart()" value="В корзину"></common-button>
    </div>
  </div>
</template>

<script>
import SafeImage from "./SafeImage";
import CommonButton from "@/components/CommonButton";

export default {
  components: {CommonButton, SafeImage},
  props: {
    id: {
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
    uid: {
      type: String,
      required: true,
    },
    price: {
      type: Number,
      required: true,
    },
    count: {
      type: Number,
      required: true,
    },
  },
  computed: {
    preparedPrice() {
      if (this.price < 10000) {
        return this.price + ' ₽'
      }
      let a = Math.floor(this.price / 1000);
      let b = Math.round((this.price % 1000) * 100) / 100;
      return `${a + ' ' || ''}${('000' + b).slice(-3)} ₽`
    }
  },
  emits: ['addToCart'],
  name: "ProductCard",
  methods: {
    addToCart() {
      this.$emit('addToCart', {productId: this.id});
      console.log(this.$store);
    }
  }
}
</script>

<style scoped>
.product {
  display: grid;
  padding: 20px;
  border: 1px solid #ccc;
  grid-column-gap: 20px;
  grid-template-columns: 200px 400px;
  grid-template-areas:
       "picture price"
       "picture name"
       "picture uid"
       "picture button";
  align-items: center;
}

.product__image {
  position: relative;
  width: 200px;
  height: 150px;
  grid-area: picture;
}

.product__price {
  font-size: 25px;
  grid-area: price;
}

.product__name {
  font-size: 30px;
  grid-area: name;
}

.product__uid {
  grid-area: uid;
}

.product__number-field {
  grid-area: number;
}

.product__number-label {
  display: block;
}

.product__button-wrap {
  grid-area: button;
}
</style>