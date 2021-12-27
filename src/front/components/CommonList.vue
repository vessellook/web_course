<template>
  <div class="list">
    <template v-for="props in propsArray" :key="props.key">
      <div class="item" :class="{'disabled': props.disabled}" @click="props.disabled || $emit('clicked', props.key)">{{ props.label }}</div>
    </template>
  </div>
</template>

<script>
export default {
  name: "CommonList",
  props: {
    propsArray: {
      validator: (value) => value.every(props => ['key', 'label'].every(key => props.hasOwnProperty(key)))
    }
  },
  emits: ['clicked']
}
</script>

<style scoped>
.list {
  margin: 5px;
}

.item {
  padding: 1em 0.5em;
  border: 1px solid #ccc;
  cursor: pointer;
}

.item:hover {
  background-color: #acd4;
}

.item.disabled {
  cursor: not-allowed;
  background-color: #eee;
}
</style>