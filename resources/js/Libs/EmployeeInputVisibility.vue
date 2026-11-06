<script>
export default {
    methods: {
        updateEmployeeInputVisibility(packageOption, package_id, moduleHr) {
            // Find the selected package option by its ID
            const selectedOption = packageOption.find(option => option.id === package_id);

            // Check if the selected option has modules
            if (selectedOption?.modules) {
                // Check if the modules contain any of the specific module names
                return selectedOption.modules.some(module =>
                    Object.keys(moduleHr).includes(module.name)
                );
            } else {
                return false;
            }
        }
    }
}
</script>