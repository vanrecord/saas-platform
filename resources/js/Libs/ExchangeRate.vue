<script>
import { defineComponent } from 'vue'
export default {
    methods:{
        convertAmount(Amount, From=null, To=null){
            let value = 0;
            if (From === To) {
                value = Amount;
            }else {
                value = (Amount * From) / To;
            }
            return value;
        },

        amountChange(amount, change_coefficient, From, To){
            let amount_change = (amount%change_coefficient)
            let amount_convert = this.convertAmount(amount_change,From,To);
            let amount_local = Math.floor(amount/change_coefficient)*change_coefficient;
            let data ={
                amount:amount_local,
                amount_change:amount_convert,
                total:amount_change+amount_local,
            }
            return data;
        },

        roundUpFour(amount){
            return (Math.round(amount*10000)/10000);
        },

        roundUpTwo(amount){
            return (Math.round(amount*100)/100);
        },
    }
} 
</script>