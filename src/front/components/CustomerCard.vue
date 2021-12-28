<template>
  <form class="form">
    <text-field class="label" label="Имя заказчика" v-model="newName" mandatory></text-field>
    <text-field class="label" label="Адрес (по умолчанию)" v-model="newAddress"></text-field>
    <text-field class="label" label="Номер телефона" v-model="newPhoneNumber"></text-field>
    <common-button :ready="!!newName" value="Сохранить"
                   @submit="saveData"></common-button>
    <loading-image v-show="isLoading"></loading-image>
  </form>
</template>

<script>
import CommonButton from "@/components/CommonButton";
import {createCustomer, updateCustomer} from "@/api/customer";
import Customer from "@/models/Customer";
import TextField from "@/components/TextField";
import LoadingImage from "@/components/LoadingImage";

export default {
  name: "CustomerCard",
  components: {LoadingImage, TextField, CommonButton},
  props: {
    customer: Customer
  },
  emits: ['updateCustomers'],
  data() {
    if (this.customer) {
      return {
        newName: this.customer.name,
        newAddress: this.customer.address,
        newPhoneNumber: this.customer.phoneNumber,
        isLoading: false
      }
    }
    return {
      newName: '',
      newAddress: '',
      newPhoneNumber: '',
      isLoading: false
    }
  },
  watch: {
    customer() {
      this.newName = this.customer.name;
      this.newAddress = this.customer.address;
      this.newPhoneNumber = this.customer.phoneNumber;
      this.isLoading = false;
    }
  },
  methods: {
    saveData() {
      let newCustomer = new Customer({
        id: this.id,
        name: this.newName,
        address: this.newAddress || null,
        phoneNumber: this.newPhoneNumber || null
      });

      let partialEmit = customer => this.$emit('updateCustomers', customer);
      this.isLoading = true;
      if (this.customer.id) {
        updateCustomer({
          customerId: this.customer.id,
          oldCustomer: this.customer,
          newCustomer,
          token: this.$store.state.token
        }).then(partialEmit, partialEmit).finally(() => this.isLoading = false);
      } else {
        createCustomer({
          customer: newCustomer,
          token: this.$store.state.token
        }).then(partialEmit).finally(() => this.isLoading = false);
      }
    }
  }
}
</script>

<style scoped>
.label {
  margin-bottom: 15px;
}

.form {
  padding: 5px;
  margin: 10px;
  border: 1px solid #ccc;
  width: 400px;
}
</style>