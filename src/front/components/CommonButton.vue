<template>
  <div class="button" :class="{'ready': ready}" @click="click">{{ value }}</div>
</template>

<script>
export default {
  name: "CommonButton",
  props: {
    value: String,
    ready: {
      type: Boolean,
      default: true
    }
  },
  emits: ['submit'],
  methods: {
    click() {
      if(this.ready) {
        this.$emit('submit');
      }
    }
  }
}
</script>

<style scoped>
.button {
  color: gray;
  border: 1px solid gray;
  display: inline-block;
  padding: 5px;
  cursor: not-allowed;
}

.ready {
  color: #28556C;
  border-color: #28556C;
  cursor: pointer;
}

.ready:hover {
  background-color: #acd4;
}

</style>