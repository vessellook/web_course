<template>
  <div class="orders">
    <div class="title" v-show="orders.length > 0">Список заказов</div>
    <common-list :propsArray="propsArray" @clicked="switchToOrder(orders[$event])"></common-list>
    <common-button value="Добавить заказ" @submit="createNewOrder"></common-button>
  </div>
</template>

<script>
import {getOrders} from "@/api/order";
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";

export default {
  name: "OrdersOfCustomer",
  components: {CommonList, CommonButton},
  props: {
    customerId: null
  },
  data: () => ({
    orders: []
  }),
  computed: {
    propsArray() {
      return this.orders.map((order, index) => ({
        key: index,
        label: 'Заказ № ' + order.id
      }));
    }
  },
  mounted() {
    getOrders({customerId: this.customerId, token: this.$store.state.token})
        .then(orders => this.orders = orders);
  },
  methods: {
    switchToOrder(order) {
      this.$router.push({name: 'OrderList', params: {id: order.id}});
    },
    createNewOrder() {
      this.$router.push({name: 'NewOrder', params: {customerId: this.customerId}});
    }
  }
}
</script>

<style scoped>
.orders {
  margin: 10px;
}

.title {
  text-align: center;
}
</style>