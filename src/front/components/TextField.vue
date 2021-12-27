<template>
  <label :class="'field'">
    <span class="label">{{ label }}<span v-if="mandatory" class="required">*</span></span>
    <slot>
      <input class="input" :type="type" :value="modelValue" @input="change">
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
    type: {
      type: String,
      default: 'text'
    },
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
  box-sizing: border-box;
  outline: none;
  line-height: 1.5em;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1em;
  padding: 6px 12px;
}

.required {
  color: red;
}
</style>