<template>
    <div class="sm:flex sm:flex-wrap p-4">
        <div class="w-full">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-xs text-left text-gray-400 dark:text-gray-400">
                    <thead class=" text-xs text-gray-500 uppercase bg-gray-200">
                        <tr>
                            <slot name="header"/>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-black">
                        <slot name="body"/>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>