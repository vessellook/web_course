<template>
  <h1 class="title">Создание заказа</h1>
  <form>
    <text-field class="label" label="Заказчик" mandatory>
      <v-select :options="customers" label="name" v-model="customer"></v-select>
    </text-field>
    <text-field class="label" label="Продукт" mandatory>
      <v-select :options="products" label="name" v-model="product"></v-select>
    </text-field>
    <text-field class="label" label="Адрес" v-model="address" mandatory></text-field>
    <text-field class="label" label="Дата заключения договора">
      <Datepicker v-model="date" locale="ru" selectText="Выбрать" cancelText="Отмена"
                  :enableTimePicker="false"></Datepicker>
    </text-field>
    <text-field class="label" label="Номер договора" v-model="agreementCode"></text-field>
    <text-field class="label" label="Ссылка на договор" v-model="agreementUrl"></text-field>
  </form>
  <template v-for="(transportation, index) in transportations" :key="index">
    <form class="form">
      <text-field class="label" label="Запланированная дата поставки" mandatory>
        <Datepicker v-model="transportation.plannedDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                    :enableTimePicker="false"></Datepicker>
      </text-field>
      <text-field class="label" label="Фактическая дата поставки">
        <Datepicker v-model="transportation.realDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                    :enableTimePicker="false"></Datepicker>
      </text-field>
      <text-field class="label" label="Количество товаров" v-model="transportation.number" mandatory></text-field>
      <text-field class="label" label="Текущее состояние поставки" mandatory>
        <v-select :options="statuses" v-model="transportation.status" :clearable="false" :reduce="result => result.id"></v-select>
      </text-field>
    </form>
  </template>
  <div style="margin-bottom: 10px">
    <common-button value="Добавить перевозку" @submit="addTransportation"></common-button>
  </div>
  <common-button :ready="!!(address && customer && product)" value="Сохранить" @submit="saveData"></common-button>
</template>

<script>
import Datepicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css'
import Order from "@/models/Order";
import TextField from "@/components/TextField";
import CommonButton from "@/components/CommonButton";
import {getCustomers} from "@/api/customer";
import {getProducts} from "@/api/product";
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import {createOrder} from "@/api/order";
import Transportation from '@/models/Transportation';
import {BadStatusError} from "@/api/common";
import {INVALIDATE_TOKEN} from "@/store/mutations";


export default {
  name: "CreateOrder",
  components: {CommonButton, TextField, Datepicker, vSelect},
  props: {
    productId: {default: null},
    customerId: {default: null}
  },
  data() {
    return {
      address: null,
      date: null,
      agreementCode: null,
      agreementUrl: null,
      customer: null,
      product: null,
      products: [],
      customers: [],
      transportations: [],
      statuses: [{id: 'planned', label: 'Запланирована'}, {id: 'finished', label: 'Завершена'}]
    }
  },
  watch: {
    customer(value, previousValue) {
      if (!this.address || (previousValue && this.address === previousValue.address)) {
        this.address = value ? value.address : null;
      }
    }
  },
  mounted() {
    getCustomers({token: this.$store.state.token})
        .then(customers => this.customers = customers)
        .then(() => {
          let findFn = customer => customer.id === +this.customerId;
          if (this.customerId && this.customers && this.customers.some(findFn)) {
            this.customer = this.customers.find(findFn);
          }
        })
        .catch(error => {
          if (error instanceof BadStatusError) {
            this.$store.commit(INVALIDATE_TOKEN);
            this.$router.replace('/');
          }
        });
    getProducts({token: this.$store.state.token})
        .then(products => this.products = products)
        .then(() => {
          if (this.productId && this.products && this.products.some(product => product.id === this.productId)) {
            this.product = this.products.find(product => product.id === this.productId);
          }
        });
  },
  methods: {
    saveData() {
      let order = new Order({
        id: null,
        productId: this.product.id,
        customerId: this.customer.id,
        address: this.address,
        agreementCode: this.agreementCode,
        agreementUrl: this.agreementUrl,
        date: this.date
      });
      createOrder({
        customerId: this.customer.id,
        order,
        token: this.$store.state.token,
        transportations: this.transportations.filter(transportation => transportation.number && transportation.status && transportation.plannedDate)
          .map(transportation => {transportation.number = +transportation.number; return transportation})
      }).then(order => this.$router.replace({name: 'OrderList', params: {id: order.id}}));
    },
    addTransportation() {
      this.transportations.push(new Transportation({
        id: null,
        orderId: null,
        plannedDate: null,
        realDate: null,
        number: null,
        status: null
      }))
    }
  }
}
</script>

<style scoped>
.label {
  margin-bottom: 15px;
}

.title {
  font-size: 1.5em;
  margin-bottom: 15px;
  color: #28556C;
}

.form {
  padding: 5px;
  border: 1px solid #ccc;
  width: 400px;
  margin-bottom: 15px;
}
</style>