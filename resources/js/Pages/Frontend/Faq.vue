<template>
    <Header :image_url="image_url" :faq_title="faq_title" :faq_image="faq_image" />
    <section class="gradient px-10">
        <div class="md:pt-8 lg:px-8 px-0 pb-96">
            <div class="flex justify-between">
                <div class="w-56 mx-auto lg:mx-0">
                    <a type="button" class="toggleColour text-white hover:text-gray-200 font-bold text-xl lg:text-2xl font-mono px-7 py-3.5 rounded-full hover:scale-105 duration-300 ease-in-out" title="Back to Homepage" href="/">
                        <div class="bg-teal-50 flex justify-center w-16 lg:w-24 2xl:w-28 2xl:ml-10">
                            <img class="h-16 md:h-20 lg:h-28 2xl:h-32" :src="$page.props.image_url+home_settings.homepage_logo">
                        </div>
                    </a>
                </div>
                <div class="flex justify-center max-w-full lg:-ml-56 w-96">
                    <p class="text-lg lg:text-3xl 2xl:text-4xl font-bold font-sans sm:mb-6 mb-3 text-white mt-10 lg:mt-20">{{ faq_title_product }}</p>
                </div>
                <div></div>
            </div>
            <div class="2xl:max-w-5xl max-w-3xl mx-auto space-y-4 my-8 sm:px-6 md:px-6 lg:px-0 px-4">
                <details v-for="faq_qustion in faq_qustions" class="group">
                    <summary class="flex items-center justify-between p-4 rounded-lg cursor-pointer bg-gray-50 mb-4">
                        <h2 class="font-medium hover:text-blue-500 text-md 2xl:text-xl">
                            {{ faq_qustion.question }}
                        </h2>

                        <svg
                            class="w-5 h-5 transition-transform duration-300 group-open:-rotate-180"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>

                    <div class="px-4 mt-4">
                    <p class="leading-relaxed text-white text-md 2xl:text-xl mb-12">
                        <span v-html="faq_qustion.answer"></span>
                    </p>
                    </div>
                </details>
            </div>
        </div>
    </section>
</template>
<script>
import Header from "@/Layouts/HeaderFaq.vue"
import {
    Link
} from '@inertiajs/inertia-vue3';
export default {
    components:{
       Header,
       Link
    },
    props: [
        'config_api_url',
        'image_url',
        'faq_title',
        'faq_image'
    ],
    data() {
        return {
            home_settings: [],
            faq_qustions:  [],
            faq_title: [],
            faq_image: [],
            faq_title_product: []
        }
    },
    created () {
        this.getHomePage()
        this.getFAQ()
        this.otherSetting()
    },
    methods: {
        getHomePage() {
            axios.get(this.config_api_url+'/home')
            .then(response => {
                this.home_settings = JSON.parse(response.data)
            });
        },
        getFAQ() {
            axios.get(this.config_api_url+'/faq-question')
            .then(response => {
                this.faq_qustions = response.data.data
            });
        },
        otherSetting() {
            axios.get(this.config_api_url+'/other-settings')
            .then(response => {
                this.faq_title = JSON.parse(response.data).faq_title_header
                this.faq_image = JSON.parse(response.data).faq_image_header
                this.faq_title_product = JSON.parse(response.data).title_faq_product
            });
        }
    }
}
</script>

<style scoped>
    .gradient {
        background: linear-gradient(100deg, #247ebd 0%, #34b148 100%);
    }
</style>