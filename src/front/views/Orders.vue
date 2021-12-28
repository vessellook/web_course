<template>
  <h1 class="title">Заказы</h1>
  <common-list :propsArray="propsArray" @clicked="openPopup(orders[$event])"></common-list>
  <common-button value="Добавить заказ" @submit="createNewOrder"></common-button>
  <base-modal v-if="currentOrder" @close="closePopup">
    <order-card :order-id="currentOrder.id" @update-orders="updateOrders().finally(closePopup)"></order-card>
  </base-modal>
</template>

<script>
import {getOrders} from "@/api/order";
import BaseModal from "@/components/BaseModal";
import OrderCard from "@/components/OrderCard";
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";
import {BadStatusError} from "@/api/common";
import {INVALIDATE_TOKEN} from "@/store/mutations";
import {mapState} from "vuex";

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
    ...mapState({
      finishedLoading: 'finishedLoading',
      role: 'role'
    }),
    propsArray() {
      return this.orders.map((order, index) => ({
        key: index,
        label: 'Заказ № ' + order.id
      }));
    },
    currentOrder() {
      if (this.id === 'new' && this.orders) {
        return new Order({
          id: null,
          productId: null,
          customerId: null,
          address: '',
          date: null,
          agreementCode: null,
          agreementUrl: null
        })
      } else if (this.id && this.orders) {
        return this.orders[this.id - 1];
      } else {
        return null;
      }

    }
  },
  watch: {
    finishedLoading(value) {
      if(value) {
        this.updateOrders();
      }
    }
  },
  mounted() {
    if(this.finishedLoading) {
      this.updateOrders();
    }
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
      return getOrders({token: this.$store.state.token})
          .then(orders => this.orders = orders)
          .catch(error => {
            if(error instanceof BadStatusError && error.status === 401) {
              this.$store.commit(INVALIDATE_TOKEN);
              this.$router.replace('/');
            }
          });
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