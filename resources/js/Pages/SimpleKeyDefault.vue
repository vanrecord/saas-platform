<template>
  <div :class="keyboardClass"></div>
</template>

<script>
import Keyboard from "simple-keyboard";
import "simple-keyboard/build/css/index.css";

export default {
  // name: "SimpleKeyboard",
  props: {
    keyboardClass: {
      default: "simple-key-default",
      type: String
    },
    input: {
      type: String
    },
    inputname: {
      type: String
    }
  },
  data: () => ({
    keyboard: null
  }),
  mounted() {
    this.keyboard = new Keyboard(this.keyboardClass, {
      onChange: this.onChange,
      onKeyPress: this.onKeyPress,
      inputName: this.inputName, 
      // layoutName: "default", 
      layout: {
        'default': [
          // '` 1 2 3 4 5 6 7 8 9 0 - = {end}',
          'q w e r t y u i o p [ ] {bksp}',
          'a s d f g h j k l ; \' {enter}',
          '{shift} z x c v b n m , . / \\',
          '{numbers} {space} {end}'
        ],
        'shift': [
          // '~ ! @ # $ % ^ & * ( ) _ + {end}',
          'Q W E R T Y U I O P { } {bksp}',
          'A S D F G H J K L : " {enter}',
          '{shift} Z X C V B N M < > ? |',
          '{numbers} {space} {end}'
        ],
        'numbers': ["1 2 3 4", "5 6 7 8", "9 0 {bksp}", "{abc} . {end} {enter}"]
      },
      display: {
        '{shift}': '⇧',
        '{enter}': 'Return',
        '{bksp}': '⌫',
        '{space}': 'Space',
        '{end}': 'Close',
        '{numbers}': '123',
        '{abc}': 'ABC',
      }, 
    });
  },

  methods: {
    onChange(input) {
      this.$emit("onChange", input);
    },
    onKeyPress(button) {
      this.$emit("onKeyPress", button);

      /**
       * If you want to handle the shift and caps lock buttons
       */
      if (button === "{shift}" || button === "{lock}") this.handleShift();
      if (button === "{numbers}" || button === "{abc}") this.handleNumbers();
    },
    handleShift() {
      let currentLayout = this.keyboard.options.layoutName;
      let shiftToggle = currentLayout === "default" ? "shift" : "default";

      this.keyboard.setOptions({
        layoutName: shiftToggle
      });
    },
    handleNumbers() {
      let currentLayout = this.keyboard.options.layoutName;
      let numbersToggle = currentLayout !== "numbers" ? "numbers" : "default";

      this.keyboard.setOptions({
        layoutName: numbersToggle
      });
    }
  },
  watch: {
    inputName(inputName) {
      this.keyboard.setOptions({ inputName });
    },
    
    input(input) {
      this.keyboard.setInput(input);
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  .simple-key-default {
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 0.5em;
    background-color: rgb(206, 221, 225);
    box-shadow: 0px -3px 10px rgba(black, 0.3);
    border-radius: 10px;
  }

  .hg-theme-default .hg-button{
    font-size: 1.5rem;
    height: 3.5rem;
  }

  .hg-button.hg-functionBtn.hg-button-end{
    width: 1px;
    background-color: #7abdc7;
  }
</style>
