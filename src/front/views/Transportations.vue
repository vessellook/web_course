<template>
  <h1 class="title">Поставки</h1>
  <common-list :propsArray="propsArray" @clicked="openPopup(transportations[$event])"></common-list>
  <base-modal v-if="id" @close="closePopup">
    <transportation-card :transportation="transportations[id - 1]"
                         @update-transportation="updateTransportation(transportations[id - 1], $event)"></transportation-card>
  </base-modal>
</template>

<script>
import CommonList from "@/components/CommonList";
import {getTransportations} from "@/api/transportation";
import BaseModal from "@/components/BaseModal";
import TransportationCard from "@/components/TransportationCard";
import {BadStatusError} from "@/api/common";
import {INVALIDATE_TOKEN} from "@/store/mutations";
import {mapState} from "vuex";

export default {
  name: "Transportations",
  components: {TransportationCard, BaseModal, CommonList},
  props: {
    id: null
  },
  data: () => ({
    transportations: []
  }),
  computed: {
    ...mapState({
      finishedLoading: 'finishedLoading',
      role: 'role'
    }),
    propsArray() {
      return this.transportations.map((transportation, index) => ({
        key: index,
        label: 'Поставка № ' + transportation.id
      }));
    }
  },
  watch: {
    finishedLoading(value) {
      if (value) {
        this.updateTransportations();
      }
    }
  },
  mounted() {
    if (this.finishedLoading) {
      this.updateTransportations();
    }
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
    updateTransportation(oldTransportation, newTransportation) {
      this.updateTransportations().finally(this.closePopup);
    },
    updateTransportations() {
      return getTransportations({token: this.$store.state.token})
          .then(transportations => this.transportations = transportations)
          .catch(error => {
            if(error instanceof BadStatusError && error.status === 401) {
              this.$store.commit(INVALIDATE_TOKEN);
              this.$router.replace('/');
            }
          });
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