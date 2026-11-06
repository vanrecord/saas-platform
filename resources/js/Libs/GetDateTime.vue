<script>
export default {
    methods:{
        getDateTime(str){
            let date = new Date(str)
            let day = date.getDate() < 10 ? '0'+date.getDate() : date.getDate()
            let month = (date.getMonth()+ 1) < 10 ? '0'+(date.getMonth()+ 1) : (date.getMonth()+ 1)
            let hours = date.getHours() < 10 ? '0'+date.getHours() : date.getHours()
            let minutes = date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes();
            let strTime = hours + ':' + minutes
            
            return day+'/'+month+'/'+date.getFullYear()+' '+strTime
        },
    }
} 
</script>