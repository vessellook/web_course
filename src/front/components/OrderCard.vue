<template>
  <div id="order-card" class="container">
    <form class="form">
      <div class="title" v-if="order">{{ 'Заказ № ' + order.id }}</div>
      <text-field class="label" label="Адрес" v-model="newAddress" mandatory></text-field>
      <text-field class="label" label="Дата заключения договора">
        <DatepickerCustom v-model="newDate" teleport="#order-card"></DatepickerCustom>
      </text-field>
      <text-field class="label" label="Номер договора" v-model="newAgreementCode"></text-field>
      <text-field class="label" label="Ссылка на договор" v-model="newAgreementUrl"></text-field>
      <div class="conflict-message" v-show="isConflictHappened">
        Информация не сохранена. Другой пользователь внёс изменения. Обновите страницу
      </div>
      <common-button :ready="!!newAddress" value="Сохранить" @submit="saveData"></common-button>
      <loading-image v-show="isLoading"></loading-image>
    </form>
    <div class="link-wrap">
      <router-link class="link" v-if="order" :to="{name: 'CustomerList', params: {id: order.customerId}}">Перейти к
        заказчику
      </router-link>
    </div>
    <transportations-of-order v-if="order" :transportations="transportations"></transportations-of-order>

  </div>
</template>

<script>
import 'vue3-date-time-picker/dist/main.css'
import Order from "@/models/Order";
import TextField from "@/components/TextField";
import CommonButton from "@/components/CommonButton";
import {getOrder, getOrderWithTransportations, updateOrder} from "@/api/order";
import TransportationsOfOrder from "@/components/TransportationsOfOrder";
import LoadingImage from "@/components/LoadingImage";
import DatepickerCustom from "@/components/DatepickerCustom";

export default {
  name: "OrderCard",
  components: {DatepickerCustom, LoadingImage, CommonButton, TextField, TransportationsOfOrder},
  props: {
    orderId: Number
  },
  emits: ['updateOrders'],
  data() {
    return {
      newAddress: null,
      newDate: null,
      newAgreementCode: null,
      newAgreementUrl: null,
      order: null,
      transportations: null,
      isLoading: false,
      isConflictHappened: false,
    }
  },
  mounted() {
    if (this.orderId) {
      getOrderWithTransportations({
        orderId: this.orderId,
        token: this.$store.state.token
      }).then(arr => {
        this.order = arr.order;
        this.transportations = arr.transportations;
      });
    }
  },
  watch: {
    order() {
      this.newAddress = this.order.address;
      this.newDate = this.order.date;
      this.newAgreementCode = this.order.agreementCode;
      this.newAgreementUrl = this.order.agreementUrl;
      this.isLoading = false;
      this.isConflictHappened = false;
    }
  },
  methods: {
    format: date => date.toLocaleDateString('ru'),
    saveData() {
      let newOrder = new Order({
        id: this.order.id,
        productId: this.order.productId,
        customerId: this.order.customerId,
        address: this.newAddress,
        date: this.newDate,
        agreementCode: this.newAgreementCode,
        agreementUrl: this.newAgreementUrl
      });
      let partialEmit = order => this.$emit('updateOrders', order);
      this.isLoading = true;
      updateOrder({
        orderId: this.order.id,
        oldOrder: this.order,
        newOrder,
        token: this.$store.state.token
      }).then(partialEmit, () => {
        this.isConflictHappened = true;
      })
          .finally(() => this.isLoading = false);
    }
  }
}
</script>

<style scoped>
.container {
  min-height: 700px;
  position: relative;
}

.form {
  padding: 5px;
  margin: 10px;
  border: 1px solid #ccc;
  width: 400px;
}

.label {
  margin-bottom: 15px;
}

.title {
  font-size: 1.5em;
  margin-bottom: 15px;
}

.link-wrap {
  text-align: right;
  margin: 10px;
}

.link {
  color: #28556C;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}

.conflict-message {
  color: red;
}
</style>