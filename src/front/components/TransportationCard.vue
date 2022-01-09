<template>
  <div class="container">
    <form class="form">
      <div v-if="transportation" class="title">{{ 'Поставка № ' + transportation.id }}</div>
      <text-field class="label" label="Запланированная дата поставки" mandatory>
        <Datepicker v-model="newPlannedDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                    :enableTimePicker="false" :monthChangeOnScroll="false" :format="format"
                    :previewFormat="format"></Datepicker>
      </text-field>
      <text-field class="label" label="Фактическая дата поставки">
        <Datepicker v-model="newRealDate" locale="ru" selectText="Выбрать" cancelText="Отмена"
                    :enableTimePicker="false" :monthChangeOnScroll="false" :format="format"
                    :previewFormat="format"></Datepicker>
      </text-field>
      <text-field class="label" label="Количество товаров" v-model="newNumber" mandatory></text-field>
      <div class="conflict-message" v-show="isConflictHappened">
        Информация не сохранена. Другой пользователь внёс изменения. Обновите страницу
      </div>
      <common-button :ready="!!(newStatus && newPlannedDate && newNumber && (newStatus !== 'finished' || newRealDate))" value="Сохранить"
                     @submit="saveData"></common-button>
      <loading-image v-show="isLoading"></loading-image>
    </form>
    <div class="link-wrap">
      <router-link class="link" v-if="transportation" :to="{name: 'OrderList', params: {id: transportation.orderId}}">
        {{ 'Перейти к заказу № ' +  transportation.orderId}}</router-link>
    </div>
  </div>
</template>

<script>
import Transportation from "@/models/Transportation";
import {updateTransportation} from "@/api/transportation";
import TextField from "@/components/TextField";
import Datepicker from "vue3-date-time-picker";
import 'vue3-date-time-picker/dist/main.css';
import CommonButton from "@/components/CommonButton";
import vSelect from "vue-select";
import LoadingImage from "@/components/LoadingImage";

export default {
  name: "TransportationCard",
  components: {TextField, Datepicker, CommonButton, vSelect, LoadingImage},
  props: {
    transportation: Transportation
  },
  emits: ['updateTransportation'],
  data() {
    let data = {
      newPlannedDate: null,
      newRealDate: null,
      newNumber: null,
      isLoading: false,
      isConflictHappened: false,
    };
    if (this.transportation) {
      Object.assign(data, {
        newPlannedDate: this.transportation.plannedDate,
        newRealDate: this.transportation.realDate,
        newNumber: '' + this.transportation.number,
      });
    }
    return data;
  },
  computed: {
    newStatus() {
      return this.transportation?.realDate ? 'finished' : 'planned';
    }
  },
  watch: {
    transportation() {
      this.newPlannedDate = this.transportation.plannedDate;
      this.newRealDate = this.transportation.realDate;
      this.newNumber = '' + this.transportation.number;
      this.isLoading = false;
      this.isConflictHappened = false;
    },
  },
  methods: {
    format: date => date.toLocaleDateString('ru'),
    saveData() {
      let newTransportation = new Transportation({
        id: this.transportation.id,
        orderId: this.transportation.orderId,
        plannedDate: this.newPlannedDate,
        realDate: this.newRealDate,
        number: +this.newNumber,
        status: this.newStatus
      });
      let partialEmit = transportation => this.$emit('updateTransportation', transportation);
      this.isLoading = true;
      updateTransportation({
        transportationId: this.transportation.id,
        oldTransportation: this.transportation,
        newTransportation,
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
  min-height: 600px;
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