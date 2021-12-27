<template>
  <div class="transportations">
    <div class="title" v-show="transportations.length > 0">Список поставок</div>
    <common-list :propsArray="propsArray" @clicked="switchToTransportation(transportations[$event])"></common-list>
    <form class="form" v-show="isNewTransportation">
      <div class="title">Новая поставка</div>
      <text-field class="label" label="Запланированная дата поставки" mandatory>
        <Datepicker v-model="plannedDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                    :enableTimePicker="false"></Datepicker>
      </text-field>
      <text-field class="label" label="Фактическая дата поставки">
        <Datepicker v-model="realDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                    :enableTimePicker="false"></Datepicker>
      </text-field>
      <text-field class="label" label="Количество товаров" v-model="number" mandatory></text-field>
      <text-field class="label" label="Текущее состояние поставки" mandatory>
        <v-select :options="statuses" v-model="status" :clearable="false"></v-select>
      </text-field>
      <common-button :ready="!!(status && plannedDate && number)" value="Добавить поставку"
                     @submit="saveData"></common-button>
    </form>
    <common-button value="Добавить поставку" v-show="!isNewTransportation"
                   @submit="createNewTransportation"></common-button>
  </div>
</template>

<script>
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";
import {createTransportation, getTransportations} from "@/api/transportation";
import TextField from "@/components/TextField";
import Datepicker from "vue3-date-time-picker";
import vSelect from "vue-select";
import Transportation from "@/models/Transportation";

export default {
  name: "TransportationsOfOrder",
  components: {CommonList, CommonButton, TextField, Datepicker, vSelect},
  props: {
    orderId: {
      required: true
    }
  },
  data() {
    let statuses = [
      {id: 'planned', label: 'Запланирована'},
      {id: 'finished', label: 'Завершена'}
    ];
    return {
      transportations: [],
      isNewTransportation: false,
      plannedDate: null,
      realDate: null,
      number: null,
      status: statuses[0],
      statuses
    }
  },
  computed: {
    propsArray() {
      return this.transportations.map((transportation, index) => ({
        key: index,
        label: 'Поставка № ' + transportation.id
      }));
    }
  },
  mounted() {
    getTransportations({
      orderId: this.orderId,
      token: this.$store.state.token
    }).then(transportations => this.transportations = transportations);
  },
  methods: {
    switchToTransportation(transportation) {
      this.$router.push({name: 'TransportationList', params: {id: transportation.id}});
    },
    createNewTransportation() {
      this.isNewTransportation = true;
    },
    saveData() {
      let transportation = new Transportation({
        id: null,
        orderId: this.orderId,
        plannedDate: this.plannedDate,
        realDate: this.realDate,
        number: +this.number,
        status: this.status.id
      });
      createTransportation({
        orderId: this.orderId,
        transportation,
        token: this.$store.state.token
      }).then(transportation => {
        this.transportations.push(transportation);
        this.isNewTransportation = false;
      })
    }
  }
}
</script>

<style scoped>
.transportations {
  margin: 10px;
}

.form {
  padding: 5px;
  border: 1px solid #ccc;
  width: 400px;
}

.label {
  margin-bottom: 15px;
}

.title {
  font-size: 1.2em;
  margin-bottom: 15px;
}

</style>