<template>
  <OnClickOutside class="datepicker-container" @trigger="close">
    <div class="input-wrap">
      <div class="input" @click="open">{{ format(modelValue) || ' ' }}</div>
      <div class="clear" v-if="modelValue" @click="clear">{{ modelValue ? 'x' : '' }}</div>
    </div>
    <Datepicker v-show="opened" :modelValue="modelValue" @update:modelValue="$emit('update:modelValue', $event)"
                locale="ru" selectText="Выбрать" cancelText="Отмена"
                :enableTimePicker="false" :autoApply="true" :monthChangeOnScroll="false"
                :format="format" :previewFormat="format" :teleport="teleport" :altPosition="true"
                inline noToday class="datepicker"
    />
  </OnClickOutside>
</template>

<script>
import Datepicker from "vue3-date-time-picker";
import {OnClickOutside} from '@vueuse/components';

export default {
  name: "DatepickerCustom",
  components: {Datepicker, OnClickOutside},
  props: {
    modelValue: null,
    teleport: {
      default: "body"
    },
    closeOnAutoApply: {
      type: Boolean,
      default: true
    }
  },
  emits: ["update:modelValue"],
  data: () => ({
    opened: false
  }),
  methods: {
    format: date => date ? date.toLocaleDateString('ru') : '',
    clear() {
      this.$emit("update:modelValue", null);
    },
    close() {
      this.opened = false;
    },
    open() {
      this.opened = true;
    }
  }
}
</script>

<style scoped>
.datepicker-container {
  position: relative;
}

.input-wrap {
  display: grid;
  grid-template-columns: 1fr 20px;
  width: 100%;
  box-sizing: border-box;
  outline: none;
  line-height: 1.5em;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1em;
  padding: 6px 12px;
}

.input {
  height: 1em;
  cursor: pointer;
}

.clear {
  font-family: monospace;
  cursor: pointer;
}
</style>

<style>
.datepicker {
  position: absolute;
  top: 100%;
  left: 0;
}
</style>