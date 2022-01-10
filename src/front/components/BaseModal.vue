<template>
  <div class="modal__wrap">
    <OnClickOutside @trigger="emitClose" class="modal">
      <div class="modal__content">
        <slot></slot>
      </div>
      <div class="button" @click="emitClose"></div>
    </OnClickOutside>
  </div>
</template>

<script>
import {OnClickOutside} from '@vueuse/components';

export default {
  name: "BaseModal",
  components: {OnClickOutside},
  emits: ['close'],
  data: () => ({
  }),
  methods: {
    emitClose() {
      this.$emit('close');
    }
  }
}
</script>

<style scoped>
.modal__wrap {
  background: #ccc8;
  position: fixed;
  display: flex;
  justify-content: center;
  align-items: center;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  cursor: pointer;
  z-index: 20;
}

.modal {
  min-width: 300px;
  min-height: 200px;
  max-height: calc(100% - 160px);
  margin: 0 auto auto;
  top: 145px;
  position: relative;
  display: flex;
  justify-content: center;
  background: #fff;
  cursor: initial;
}

.modal__content {
  overflow-y: auto;
}

.button {
  position: absolute;
  top: -30px;
  right: -30px;
  cursor: pointer;
  font-size: 30px;
  overflow-y: auto;
}

.button:before {
  content: "\2715";
}
</style>