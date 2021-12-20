<template>
<div v-show="isShow" class="modal__wrap">
  <OnClickOutside @trigger="close" class="modal">
    <div class="modal__content">
      <slot></slot>
    </div>
      <div class="button" @click="close"></div>
  </OnClickOutside>
</div>
</template>

<script>
import {OnClickOutside} from '@vueuse/components';
export default {
  name: "BaseModal",
  components: {OnClickOutside},
  emits: ['close'],
  props: {
    isShow: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    close() {
      if(this.isShow) this.$emit('close');
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
  margin: 145px auto auto;
  position: relative;
  display: flex;
  justify-content: center;
  background: #fff;
  cursor: initial;
}

.button {
  position: absolute;
  top: -30px;
  right: -30px;
  cursor: pointer;
  font-size: 30px;
}

.button:before {
  content: "\2715";
}
</style>