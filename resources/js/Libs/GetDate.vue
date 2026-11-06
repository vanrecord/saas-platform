<script>
export default {
    methods:{
            getDate(today){
                var date = today.getDate();
                var month = today.getMonth()+1;
                var year = (today.getFullYear()).toString().slice(-2);
                if(date<10){
                    date = '0'+date;
                }
                if(month<10){
                    month = '0'+month;
                }
                return date+''+month+''+year;
            },
       
    }
} 
</script>