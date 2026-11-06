
<script>
import { defineComponent } from 'vue'
export default {
    methods:{
        checkEnableDiscount(products, enableDiscount){
            if(enableDiscount == true){
                return enableDiscount
            }

            const pro = _.find(products,product => product.product_type == null || product.product_type !== 2)
            const pro_pack = _.find(products,product => product.product_type == 2)
            if(enableDiscount == false && (pro == undefined || pro_pack == undefined)){
                return true
            }
            return false
        },
    }
} 
</script>
