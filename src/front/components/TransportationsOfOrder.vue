<template>
  <div id="transportations-of-order" class="transportations">
    <div class="title" v-show="transportationsReal.length > 0">Список поставок</div>
    <common-list :propsArray="propsArray" @clicked="switchToTransportation(transportationsReal[$event])"></common-list>
    <div class="container" v-show="isNewTransportation">
      <form class="form">
        <div class="title">Новая поставка</div>
        <text-field class="label" label="Запланированная дата поставки" mandatory>
          <DatepickerCustom v-model="plannedDate" teleport="#transportations-of-order"></DatepickerCustom>
        </text-field>
        <text-field class="label" label="Фактическая дата поставки">
          <DatepickerCustom v-model="real" teleport="#transportations-of-order"></DatepickerCustom>
        </text-field>
        <text-field class="label" label="Количество товаров" v-model="number" mandatory></text-field>
        <common-button :ready="!!(status && plannedDate && number)" value="Добавить поставку"
                       @submit="saveData"></common-button>
      </form>
    </div>
    <common-button value="Добавить поставку" v-show="!isNewTransportation"
                   @submit="createNewTransportation"></common-button>
  </div>
</template>

<script>
import CommonList from "@/components/CommonList";
import CommonButton from "@/components/CommonButton";
import {createTransportation} from "@/api/transportation";
import TextField from "@/components/TextField";
import Transportation from "@/models/Transportation";
import DatepickerCustom from '@/components/DatepickerCustom';

export default {
  name: "TransportationsOfOrder",
  components: {CommonList, CommonButton, TextField, DatepickerCustom},
  props: {
    transportations: {
      required: true
    }
  },
  data() {
    let statuses = [
      {id: 'planned', label: 'Запланирована'},
      {id: 'finished', label: 'Завершена'}
    ];
    return {
      transportationsReal: this.transportations,
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
      return this.transportationsReal.map((transportation, index) => ({
        key: index,
        label: 'Поставка № ' + transportation.id
      }));
    }
  },
  watch: {
    transportations() {
      this.transportationsReal = this.transportations;
    }
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
        this.transportationsReal.push(transportation);
        this.isNewTransportation = false;
      })
    }
  }
}
</script>

<style scoped>
.container {
  min-height: 600px;
}

.transportations {
  margin: 10px;
}

.form {
  margin: 5px;
  padding: 1em 0.5em;
  border: 1px solid #ccc;
}

.label {
  margin-bottom: 15px;
}

.title {
  font-size: 1.2em;
  margin-bottom: 15px;
}

</style>