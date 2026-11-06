<script>
const useModal = {
    computed: {
        modalComponent() {
            return this.$page.props.modal ? () => import(`@/Pages/${this.$page.props.modal}`) : false
        },
    },
}
</script>