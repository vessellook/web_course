<template>
  <h1 class="title">Заказчики</h1>
  <common-list :propsArray="propsArray" @clicked="openPopup(customers[$event])"></common-list>
  <common-button value="Добавить заказчика" @submit="openPopupForNewCustomer"></common-button>
  <base-modal v-if="currentCustomer" @close="closePopup">
    <template v-slot>
      <customer-card :customer="currentCustomer" @update-customers="updateCustomers"></customer-card>
      <orders-of-customer v-if="id" :customer-id="id"></orders-of-customer>
    </template>
  </base-modal>
</template>

<script>
import {getCustomers} from "@/api/customer";
import BaseModal from "@/components/BaseModal";
import CustomerCard from "@/components/CustomerCard";
import OrdersOfCustomer from "@/components/OrdersOfCustomer";
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";
import Customer from '@/models/Customer';

export default {
  name: "Customers",
  components: {CommonButton, CommonList, OrdersOfCustomer, CustomerCard, BaseModal},
  props: {
    id: {
      default: null
    }
  },
  data() {
    return {
      customers: []
    }
  },
  computed: {
    propsArray() {
      return this.customers.map((customer, index) => ({
        key: index,
        label: customer.name
      }));
    },
    currentCustomer() {
      if (this.id === 'new') {
        return new Customer({
          id: null,
          name: '',
          address: null,
          phoneNumber: null
        });
      } else if (this.id && this.customers) {
        return this.customers[this.id - 1];
      } else {
        return null;
      }
    }
  },
  mounted() {
    getCustomers({token: this.$store.state.token})
        .then(customers => this.customers = customers);
  },
  methods: {
    closePopup() {
      if (this.id !== null) {
        this.$router.push({name: 'CustomerList'});
      }
    },
    openPopup(customer) {
      this.$router.push({name: 'CustomerList', params: {id: customer.id}});
    },
    openPopupForNewCustomer() {
      this.$router.push({name: 'CustomerList', params: {id: 'new'}});
    },
    updateCustomers() {
      getCustomers({token: this.$store.state.token})
          .then(customers => this.customers = customers)
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