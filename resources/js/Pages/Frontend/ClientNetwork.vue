<template>
    <div class="gradient">
        <div class="lg:px-36 pt-5 w-full lg:flex lg:justify-between font-sans">
            <div class="lg:w-44 lg:h-44 w-16 h-16">
                <Link type="button" class="toggleColour text-white hover:text-gray-200 font-bold text-xl lg:text-2xl font-mono px-7 py-3.5 hover:scale-105 duration-300 ease-in-out w-44 h-44" title="Back to Homepage" href="/"> 
                    <img class="h-16 md:h-20 lg:h-28 2xl:h-32" :src="$page.props.home_page?.homepage_logo ? $page.props.image_url+$page.props.home_page.homepage_logo : '/images/dental/logo.png'">
                </Link>
            </div>
            <div class="lg:w-1/2 lg:mt-24 mt-10 px-6 2xl:px-24 mx-3">
                <span class="2xl:text-3xl lg:text-2xl text-xl text-white font-medium text-left" v-html="healthcare_subscriptions.title_network_provider"></span>
                <div class="text-white font-medium bg-blue-600 lg:text:md 2xl:text-xl text-md px-5 py-2 text-center inline-flex items-center font-sans shadow-lg flex justify-center lg:mt-5 mt-3">
                  Begin your journey with {{ client_network_provider.client_name }}
                </div>
            </div>
            <div class="lg:w-1/3 lg:mt-24 mt-2">
                <img :src="$page.props.image_url + client_network_provider.client_logo" 
                    class="object-scale-down object-center w-52 h-48 align-top mx-auto 2xl:mx-2" alt="Client Network" />
            </div>
        </div>
        <div class="relative -mt-12 lg:-mt-24">
            <svg viewBox="0 0 1428 174" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
                        <path d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496" opacity="0.100000001"></path>
                        <path d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z" opacity="0.100000001"></path>
                        <path d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z" id="Path-4" opacity="0.200000003"></path>
                    </g>
                </g>
            </svg>
        </div>
        <div class="text-left px-5 lg:px-24 2xl:px-28 lg:py-20 py-6 bg-gray-300">
            <!-- Carousel slide vue for Client -->
            <template v-if="client_network_provider['providers'].length > 0">
                <FrontendCarouselDetail :image_url="image_url" :client_network_provider="client_network_provider"/>
            </template>
            <template v-else>
               <div class="flex justify-center">
                   <h2 class="text-2xl lg:text-5xl 2xl:text-6xl no-image">No image network provider.!</h2> 
               </div>
               <div class="flex justify-center">
                   <p style='font-size:60px;'>&#127770;</p>
                   <p style='font-size:60px;'>&#127770;</p>
               </div>
            </template>
        </div>
    </div>
</template>

<script>
import FrontendCarouselDetail from "@/Layouts/CarouselClientDetail.vue"
import {
    Link
} from '@inertiajs/inertia-vue3';
export default {
    components:{
       FrontendCarouselDetail,
       Link
    },
    props: [
        'image_url',
        'config_api_url',
        'healthcare_subscriptions',
        'client_network_provider'
    ],
    data() {
        return {
            healthcare_subscriptions: [],
        }
    },
    created() {
        this.getHealthCareSubscriptions()
    },
    methods: {
        getHealthCareSubscriptions() {
            axios.get(this.config_api_url+'/healthcare-subscriptions')
            .then(response => {
                this.healthcare_subscriptions = JSON.parse(response.data)
            });
        },
    },
}
</script>

<style scoped>
.gradient {
    background: linear-gradient(10deg, #247ebd 0%, #34b148 100%);
}
.no-image {
  background: -webkit-linear-gradient(110deg, #247ebd 30%, #34b148 70%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>