<template>
  <div class="button" @click="$emit('click')">{{ value }}</div>
</template>

<script>
export default {
  name: "CommonButton",
  props: {
    value: String
  }
}
</script>

<style scoped>
.button {
  color: green;
  border: 1px solid green;
  display: inline-block;
  padding: 5px;
  cursor: pointer;
}

.button:hover {
  color: white;
  border-color: white;
  background-color: green;
}
</style>