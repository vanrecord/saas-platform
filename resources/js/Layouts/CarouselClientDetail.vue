<template>
  <Carousel :autoplay="4000" :wrapAround="true" :transition="500" :breakpoints="breakpoints">
    <Slide v-for="slide in client_network_provider['providers']" :key="slide">
      <div class="carousel__item w-56 h-80 lg:w-72 lg:h-96 px-1 py-2">
        <img :src="$page.props.image_url+slide.image_provider" class="object-scale-down object-center w-full h-full" alt="Client Network" />
      </div>
    </Slide>

    <template #addons>
      <Navigation />
    </template>
  </Carousel>
</template>

<script>
import { defineComponent } from 'vue'
import { Carousel, Navigation, Slide } from 'vue3-carousel'

import 'vue3-carousel/dist/carousel.css'

export default defineComponent({
  components: {
    Carousel,
    Slide,
    Navigation,
  },
  props: [
    'image_url',
    'client_network_provider'
  ],
  data: () => ({
    // breakpoints are mobile first
    // any settings not specified will fallback to the carousel settings
    breakpoints: {
      // 460px and up
      460: {
        itemsToShow: 1,
        snapAlign: 'center',
      },
      // 640px and up
      640: {
        itemsToShow: 2,
        snapAlign: 'center',
      },
      // 700px and up
      700: {
        itemsToShow: 2.65,
        snapAlign: 'center',
      },
      // 1024 and up
      1024: {
        itemsToShow: 3,
        snapAlign: 'center',
      },
      // 1024 and up
      1280: {
        itemsToShow: 3,
        snapAlign: 'center',
      },
      // 1536 and up 
      1536: {
        itemsToShow: 5.10,
        snapAlign: 'center',
      }
    },
  }),
})
</script>

<style scoped>
.carousel__slide {
  padding: 5px;
}

.carousel__viewport {
  perspective: 2000px;
}

.carousel__track {
  transform-style: preserve-3d;
}

.carousel__slide--sliding {
  transition: 0.5s;
}

.carousel__slide {
  opacity: 0.9;
  transform: rotateY(-20deg) scale(0.95);
}

.carousel__slide--active ~ .carousel__slide {
  transform: rotateY(20deg) scale(0.95);
}

.carousel__slide--prev {
  opacity: 1;
  transform: rotateY(-10deg) scale(0.95);
}

.carousel__slide--next {
  opacity: 1;
  transform: rotateY(10deg) scale(0.95);
}

.carousel__slide--active {
  opacity: 1;
  transform: rotateY(0) scale(1.1);
}
</style>
