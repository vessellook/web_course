<template>
  <h1 class="title">Заказы</h1>
  <common-list :propsArray="propsArray" @clicked="openPopup(orders[$event])"></common-list>
  <common-button value="Добавить заказ" @submit="createNewOrder"></common-button>
  <base-modal v-if="currentOrder" @close="closePopup">
    <order-card :order="currentOrder" @update-orders="updateOrders"></order-card>
  </base-modal>
</template>

<script>
import {getOrders} from "@/api/order";
import BaseModal from "@/components/BaseModal";
import OrderCard from "@/components/OrderCard";
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";

export default {
  name: "Orders",
  components: {OrderCard, BaseModal, CommonList, CommonButton},
  props: {
    id: {
      default: null
    }
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
    },
    currentOrder() {
      if(this.id === 'new' && this.orders) {
        return new Order({
          id: null,
          productId: null,
          customerId: null,
          address: '',
          date: null,
          agreementCode: null,
          agreementUrl: null
        })
      } else if(this.id && this.orders) {
        return this.orders[this.id - 1];
      } else {
        return null;
      }

    }
  },
  mounted() {
    getOrders({token: this.$store.state.token})
        .then(orders => this.orders = orders);
  },
  methods: {
    closePopup() {
      if (this.id !== null) {
        this.$router.push({name: 'OrderList'});
      }
    },
    openPopup(order) {
      this.$router.push({name: 'OrderList', params: {id: order.id}});
    },
    updateOrders() {
      getOrders({token: this.$store.state.token})
          .then(orders => this.orders = orders)
          .finally(this.closePopup);
    },
    createNewOrder() {
      this.$router.push({name: 'NewOrder'})
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