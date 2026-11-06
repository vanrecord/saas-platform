<template>
  <div :class="$attrs.class">
    <label v-if="label" class="block pb-2 font-medium text-sm text-gray-700 dark:text-light" :for="id">{{ label }}
        <span v-if="required=='required'" class="text-danger pull-right">&nbsp;*</span>
    </label>
    <input ref="input" v-bind="{ ...$attrs, class: null }" class="w-full rounded-md focus:ring focus:ring-primary-100 border border-gray-300" :class="{ error: error } +' '+classInput" 
        :id="inputName"
        :type="type" 
        :value="inputValue" 
        :disabled="disabled"
        :placeholder="placeholder"
        @input="onInputChange" 
        @focus="onInputFocus"
    />
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'

export default {
    inheritAttrs: false,
    props: {
        inputs: {
          type: Object
        },
        inputName: {
          type: String
        },

        id: {
          type: String,
          default() {
              return `text-input-${uuid()}`
          },
        },
        type: {
          type: String,
          default: 'text',
        },
        error: String,
        label: String,
        placeholder: String,
        disabled: Boolean,
        required: {
          type: String,
          default: 'text',
        },
        classInput: {
          type: String,
          default: '',
        },
    },
    methods: {
        onInputChange(input) {
          this.$emit("onInputChange", input);
        },
        onInputFocus(input) {
          this.$emit("onInputFocus", input);
        }
    },
    
    computed: {
        inputValue: function () {
          return this.inputs[this.inputName];
        }
    },

    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>