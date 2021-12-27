<template>
  <h1 class="title">Поставки</h1>
  <common-list :propsArray="propsArray" @clicked="openPopup(transportations[$event])"></common-list>
  <base-modal v-if="id" @close="closePopup">
    <transportation-card :transportation="transportations[id - 1]"
                         @update-transportations="updateTransportations"></transportation-card>
  </base-modal>
</template>

<script>
import CommonList from "@/components/CommonList";
import {getTransportations} from "@/api/transportation";
import BaseModal from "@/components/BaseModal";
import TransportationCard from "@/components/TransportationCard";
export default {
  name: "Transportations",
  components: {TransportationCard, BaseModal, CommonList},
  props: {
    id: null
  },
  data: ()=> ({
    transportations: []
  }),
  computed: {
    propsArray() {
      return this.transportations.map((transportation, index) => ({
        key: index,
        label: 'Поставка № ' + transportation.id
      }));
    }
  },
  mounted() {
    getTransportations({token: this.$store.state.token})
        .then(transportations => this.transportations = transportations);
  },
  methods: {
    closePopup() {
      if (this.id !== null) {
        this.$router.push({name: 'TransportationList'});
      }
    },
    openPopup(transportation) {
      this.$router.push({name: 'TransportationList', params: {id: transportation.id}});
    },
    updateTransportations() {
      getTransportations({token: this.$store.state.token})
          .then(transportations => this.transportations = transportations)
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