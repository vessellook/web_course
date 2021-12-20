<template>
  <div class="image-wrap">
    <slot :src="src" :alt="alt" :title="title" :color="color">
      <img :src="src" :alt="alt" :title="title" :style="`background: ${color}`">
    </slot>
  </div>
</template>

<script>
// https://habr.com/ru/company/skillbox/blog/592643/
export default {
  props: {
    alt: String,
    title: String,
    src: String,
    color: {
      type: String,
      default: 'wheat',
    }
  },
  mounted() {
    this.$el
  },
  name: "SafeImage",

}
</script>

<style scoped>
.image-wrap {
  position: relative;
}

.image-wrap img {
  max-width: 100%;
  max-height: 100%;
}

.image-wrap img:before {
  content: attr(alt);
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  background-color: inherit;
  display: flex;
  align-items: center;
  justify-content: center;
}


</style>