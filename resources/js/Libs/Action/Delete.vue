<script>
import { defineComponent } from 'vue'
import { useToast } from "vue-toastification";
export default {
    methods:{
        destroy(route){
             this.$swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'inline-flex items-center mx-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 dark:focus:ring-offset-darker',
                    cancelButton: 'inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition',
                },
                buttonsStyling: false,
            }).then(result => {
                if(result.value){
                    this.$inertia.delete(route, {
                        preserveScroll:true,
                        onSuccess: () => { useToast().success(this.tm("DeletedSuccessfully")); },
                        onError: (res) => { useToast().error(res.error); }
                    })
                }
            })
        }
       
    }
} 
</script>