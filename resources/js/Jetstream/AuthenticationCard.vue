<template>
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full md:w-6/12 xl:w-5/12 mt-6 px-8  py-12 bg-white shadow-md overflow-hidden sm:rounded-lg -mt-6 mb-12">
            <slot />
        </div>
    </div>
</template>
