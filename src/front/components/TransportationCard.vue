<template>
  <form class="form">
    <div v-if="transportation" class="title">{{ 'Поставка № ' + transportation.id }}</div>
    <text-field class="label" label="Запланированная дата поставки" mandatory>
      <Datepicker v-model="newPlannedDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                  :enableTimePicker="false"></Datepicker>
    </text-field>
    <text-field class="label" label="Фактическая дата поставки">
      <Datepicker v-model="newRealDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                  :enableTimePicker="false"></Datepicker>
    </text-field>
    <text-field class="label" label="Количество товаров" v-model="newNumber" mandatory></text-field>
    <text-field class="label" label="Текущее состояние поставки" mandatory>
      <v-select :options="statuses" v-model="newStatus" :clearable="false"></v-select>
    </text-field>
    <common-button :ready="!!(newStatus && newPlannedDate && newNumber && (newStatus !== 'finished' || newRealDate))" value="Сохранить"
                   @submit="saveData"></common-button>
  </form>
  <router-link class="link" v-if="transportation" :to="{name: 'OrderList', params: {id: transportation.orderId}}">
    {{ 'Перейти к заказу № ' +  transportation.orderId}}</router-link>
</template>

<script>
import Transportation from "@/models/Transportation";
import {updateTransportation} from "@/api/transportation";
import TextField from "@/components/TextField";
import Datepicker from "vue3-date-time-picker";
import CommonButton from "@/components/CommonButton";
import vSelect from "vue-select";

export default {
  name: "TransportationCard",
  components: {TextField, Datepicker, CommonButton, vSelect},
  props: {
    transportation: Transportation
  },
  emits: ['updateTransportation'],
  data() {
    let data = {
      newPlannedDate: null,
      newRealDate: null,
      newNumber: null,
      newStatus: null,
      statuses: [
        {id: 'planned', label: 'Запланирована'},
        {id: 'finished', label: 'Завершена'}
      ]
    };
    if (this.transportation) {
      Object.assign(data, {
        newPlannedDate: this.transportation.plannedDate,
        newRealDate: this.transportation.realDate,
        newNumber: '' + this.transportation.number,
        newStatus: data.statuses.find(status => status.id === this.transportation.status),
      });
    }
    return data;
  },
  watch: {
    transportation() {
      this.newPlannedDate = this.transportation.plannedDate;
      this.newRealDate = this.transportation.realDate;
      this.newNumber = '' + this.transportation.number;
      this.newStatus = this.statuses.find(status => status.id === this.transportation.status);
    }
  },
  methods: {
    saveData() {
      let newTransportation = new Transportation({
        id: this.transportation.id,
        orderId: this.transportation.orderId,
        plannedDate: this.newPlannedDate,
        realDate: this.newRealDate,
        number: +this.newNumber,
        status: this.newStatus.id
      });
      this.$emit('updateTransportation', newTransportation);
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
</style>