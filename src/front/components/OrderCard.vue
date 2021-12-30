<template>
  <form class="form">
    <div class="title" v-if="order">{{ 'Заказ № ' + order.id }}</div>
    <text-field class="label" label="Адрес" v-model="newAddress" mandatory></text-field>
    <text-field class="label" label="Дата заключения договора">
      <Datepicker v-model="newDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                  :enableTimePicker="false"></Datepicker>
    </text-field>
    <text-field class="label" label="Номер договора" v-model="newAgreementCode"></text-field>
    <text-field class="label" label="Ссылка на договор" v-model="newAgreementUrl"></text-field>
    <div class="conflict-message" v-show="isConflictHappened">
      Информация не сохранена. Другой пользователь внёс изменения. Обновите страницу
    </div>
    <common-button :ready="!!newAddress" value="Сохранить" @submit="saveData"></common-button>
    <loading-image v-show="isLoading"></loading-image>
  </form>
  <router-link class="link" v-if="order" :to="{name: 'CustomerList', params: {id: order.customerId}}">Перейти к
    заказчику
  </router-link>
  <transportations-of-order v-if="order" :transportations="transportations"></transportations-of-order>
</template>

<script>
import Datepicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css'
import Order from "@/models/Order";
import TextField from "@/components/TextField";
import CommonButton from "@/components/CommonButton";
import {getOrder, getOrderWithTransportations, updateOrder} from "@/api/order";
import TransportationsOfOrder from "@/components/TransportationsOfOrder";
import LoadingImage from "@/components/LoadingImage";

export default {
  name: "OrderCard",
  components: {LoadingImage, CommonButton, TextField, Datepicker, TransportationsOfOrder},
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

.link {
  display: block;
  text-align: right;
  color: #28556C;
  text-decoration: none;
  margin: 10px;
}

.link:hover {
  text-decoration: underline;
}

.conflict-message {
  color: red;
}
</style>