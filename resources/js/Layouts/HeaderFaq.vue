<template>
    <div class="">
        <div class="relative">
            <img :src="$page.props.image_url+faq_image" class="block w-full" alt="Image Header"/>
            <h1 class="absolute text-xl font-semibold text-white sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center w-full h-full bg-gray-900 bg-opacity-50">
                {{ faq_title }}</h1>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'image_url',
        'faq_title',
        'faq_image'
    ],
}
</script>
