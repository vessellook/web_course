<template>
  <label :class="'field'">
    <span class="label">{{ label }}<span v-if="mandatory" class="required">*</span></span>
    <slot>
      <input class="input" type="text" :name="name" :value="modelValue" @change="change">
    </slot>
  </label>
</template>

<script>
export default {
  name: "TextField",
  props: {
    mandatory: Boolean,
    label: {
      type: String,
      required: true
    },
    name: String,
    modelValue: String,
  },
  emits: ['update:modelValue'],
  methods: {
    change(event) {
      this.$emit('update:modelValue', event.target.value);
    }
  }
}
</script>

<style scoped>
.field,
.label {
  display: block;
}

.input {
  width: 100%;
}

.required {
  color: red;
}
</style>