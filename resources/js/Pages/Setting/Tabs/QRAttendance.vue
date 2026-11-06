<template>
    <div class="w-full p-8">
        <label class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
            Upload background image (max file size 2MB)
        </label>
        <file-pond
            ref="upload_background"
            :allowMultiple="false"
            :allowImageEdit="true"
            :allowReorder="true"
            :credits="''"
            :files="myfileattendance"
            :allowImagePreview="false"
            max-file-size="2MB"
            @activatefile="previewFile"
            @processfile="onProcessFile"
            @addfile="onAddFile"
            v-on:processfile="onload"
            @updatefiles="fileUpload"
        />
    </div> 
    <DialogModal :show="isPreviewFile">
        <template #title>
            <div class="w-full border-b py-1 flex flex-wrap">
                Preview Background QR
            </div>
        </template>
        <template #content>
            <div class="pb-2 pr-2 w-full">
                <img :src="preview_file">
            </div>
        </template>
        <template #footer>
            <div class="w-[20] float-left">
                <button class="mr-2 inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring-red-300 disabled:opacity-25 transition bg-primary cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker" @click="close">Close</button>
            </div>
        </template>
    </DialogModal>
</template>

<script>
import { defineComponent } from 'vue'
import { useI18n } from "vue-i18n"
import { lang } from "@/Lang"
import { useToast } from 'vue-toastification'
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import DialogModal from '@/Components/Modal/DialogModal.vue'

export default defineComponent({
    components: {
        FilePond:vueFilePond(
            FilePondPluginFileValidateType,
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize
        ),
        DialogModal,
    },
    setup() {
        const {
            tm
        } = useI18n({
            inheritLocale: true,
            messages: lang
        })
        const toast = useToast();
        
        return {
            tm,
            toast
        }
    },
    props: [
        'form',
        'myfile',
    ],

    data() {
        return {
            isPreviewFile : false,
            preview_file  : null,
            myfileattendance : null,

        }
    },
    mounted(){
        this.myfileattendance = this.form.push_qr_attendance&&this.form.push_qr_attendance.length>0?this.form.push_qr_attendance:this.myfile;
    },

    methods: {
        previewFile(file) {
            if(file.source.url){
                this.isPreviewFile=true; 
                this.preview_file = file.source.url
            }else{
                this.isPreviewFile=true; 
                let reader = new FileReader
                reader.onload = e => {
                    this.preview_file = e.target.result
                }
                reader.readAsDataURL(file.file)
            }
        },
        close() {
            this.isPreviewFile=false;
        },
        fileUpload(fileItem) {
            if(fileItem[0]?.source?.url){
                this.form.upload_background_qr = fileItem[0]?.file
            }else {
                const file = fileItem.map(fileItem => fileItem.file)
                if(file[0]){
                    this.form.upload_background_qr = file[0]
                    this.form.push_qr_attendance=[];
                    const reader = new FileReader();
                    reader.onload = e => {
                        this.form.push_qr_attendance.push(file[0])
                    }
                    reader.readAsDataURL(file[0]);
                    
                }else{
                    this.form.upload_background_qr = null
                }
            }
        },
    }
})
</script>

