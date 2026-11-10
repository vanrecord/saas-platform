<template>
	<div class="w-full pb-2 pr-6">
	    <label v-if="label" class="block font-medium text-sm pb-2 text-gray-700 dark:text-light">
		    {{ label }}
		    <span v-if="required === 'required'" class="text-red-800 pull-right">&nbsp;*</span>
	    </label>
	    <div class="quill-editor" :class="{ 'quill-editor-wrapper ql-container': readOnly }">
	      	<div ref="toolbarRef" class="quill-toolbar"></div>
	      	<div ref="editorRef" />
	      	<span v-if="showError" class="error mt-2 text-xs text-red-400">{{ displayError }}</span>
	    </div>
	</div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'
import '@/quill-custom-lists.js'

export default {
 	name: 'EditorContainerQuill',
	props: {
	    modelValue: {
		    type: String,
		    default: '',
	    },
	    error: {
	      	type: String,
	      	default: '',
	    },
	    label: {
	      	type: String,
	      	default: '',
	    },
	    required: {
	      	type: String,
	      	default: ''
	    },
	    toolbarType: {
	      	type: String,
	      	default: 'essential',
		    validator(value) {
		        return ['essential', 'minimal', 'full'].includes(value)
		    },
	    },
	    customToolbar: {
	      	type: Array,
	      	default: null,
	    },
	    readOnly: {
	      	type: Boolean,
	      	default: false,
	    },
	},
  	emits: ['update:modelValue'],

  	setup(props, { emit }) {
	    const editorRef = ref(null)
	    const toolbarRef = ref(null)
	    let quill = null

	    const displayError = computed(() => props.error)
	    const showError = computed(() => displayError.value !== '')

	    const arrowSymbols = {
			'arrow-right': '\u2192',
			'arrow-left': '\u2190',
			// 'arrow-up': '\u2191',
			// 'arrow-down': '\u2193',
			// 'arrow-up-right': '\u2197',
			// 'arrow-up-left': '\u2196',
			// 'arrow-down-right': '\u2198',
			// 'arrow-down-left': '\u2199',
		}

	    const arrowKeys = Object.keys(arrowSymbols)

	    const toolbarEssential = [
		      [{ header: [1, 2, 3, 4, 5, 6, false] }],
		      ['bold', 'italic', 'underline'],
		      [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
		      ['arrow-right', 'arrow-left'],
		      [{ align: [] }],
		      ['blockquote', 'code-block'],
		      ['link'],
		      [{ color: [] }],
		      ['clean'],
	    ]

	    const toolbarMinimal = [
		      [{ header: [1, 2, false] }],
		      ['bold', 'italic', 'underline'],
		      ['link'],
		      [{ list: 'ordered' }, { list: 'bullet' }],
		      ['blockquote'],
		      ['clean'],
	    ]

	    const toolbarFull = [
		      ['bold', 'italic', 'underline', 'strike'],
		      ['blockquote', 'code-block'],
		      ['link', 'image', 'video', 'formula'],
		      [{ header: 1 }, { header: 2 }],
		      [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
		      ['arrow-right', 'arrow-left'],
		      [{ script: 'sub' }, { script: 'super' }],
		      [{ indent: '-1' }, { indent: '+1' }],
		      [{ direction: 'rtl' }],
		      [{ size: ['small', false, 'large', 'huge'] }],
		      [{ header: [1, 2, 3, 4, 5, 6, false] }],
		      [{ color: [] }, { background: [] }],
		      [{ font: [] }],
		      [{ align: [] }],
		      ['clean'],
	    ]

	    function toolbarOptions() {
		      if (props.customToolbar && Array.isArray(props.customToolbar)) {
		        return props.customToolbar
		      }
		      switch (props.toolbarType) {
		        case 'minimal':
		          return toolbarMinimal
		        case 'full':
		          return toolbarFull
		        case 'essential':
		        default:
		          return toolbarEssential
		      }
	    }

	    function toggleArrowList(type) {
		      const range = quill.getSelection()
		      if (!range) return
		      const [line] = quill.getLine(range.index)
		      const current = line?.formats()?.list
		      const newType = current === type ? false : type
		      quill.formatLine(range.index, range.length, 'list', newType)
		      updateToolbar()
	    }

	    function updateToolbar() {
		      	const range = quill.getSelection()
		      	if (!range) return
		     	const [line] = quill.getLine(range.index)
		      	const current = line?.formats()?.list
			    for (const key of arrowKeys) {
			        const btn = toolbarRef.value.querySelector(`.ql-${key}`)
			        if (btn) btn.classList.toggle('ql-active', current === key)
			    }
	    }

	    function injectArrowStyles() {
	      	let styles = ''
		    for (const [key, symbol] of Object.entries(arrowSymbols)) {
			    styles += `
				  	.ql-editor li[data-list='${key}'] {
					    list-style: none;
					    position: relative;
					    padding-left: 1.6em;
					    min-height: 1.6em;
				 	}
				  	.ql-editor li[data-list='${key}']::before {
					    content: '${symbol}';
					    position: absolute;
					    left: 0;
					    top: 40%;
					    transform: translateY(-50%);
					    color: #333;
					    font-size: 18px;
				  	}
				  	.ql-editor li[data-list='${key}'].ql-align-left { text-align: left; }
				      .ql-editor li[data-list='${key}'].ql-align-center { text-align: center; }
				      .ql-editor li[data-list='${key}'].ql-align-right { text-align: right; }
				      .ql-editor li[data-list='${key}'].ql-align-justify { text-align: justify; }

				      .ql-editor li[data-list='${key}'].ql-align-left::before,
				      .ql-editor li[data-list='${key}'].ql-align-center::before,
				      .ql-editor li[data-list='${key}'].ql-align-right::before,
				      .ql-editor li[data-list='${key}'].ql-align-justify::before {
				        left: auto;
				        top: 40%;
				        transform: translate(-100%, -50%);
				      }
			    `
		  	}
			const styleTag = document.createElement('style')
			styleTag.type = 'text/css'
			styleTag.appendChild(document.createTextNode(styles))
			document.head.appendChild(styleTag)
	    }

	    onMounted(() => {
	    	injectArrowStyles()
	      	const handlers = {}
		    for (const key of arrowKeys) {
		        handlers[key] = () => toggleArrowList(key)
		    }

		    quill = new Quill(editorRef.value, {
		        theme: 'snow',
		        modules: {
		          toolbar: {
		            container: toolbarOptions(),
		            handlers,
		          },
		        },
		        readOnly: props.readOnly,
		    })

		    quill.on('text-change', () => {
		        const html = editorRef.value.querySelector('.ql-editor').innerHTML
		        emit('update:modelValue', html)
		    })

		    if (props.modelValue) {
		        quill.clipboard.dangerouslyPasteHTML(props.modelValue)
		    }

		    quill.on('selection-change', updateToolbar)
		    quill.on('text-change', updateToolbar)
		    updateToolbar()
	    })

	    watch(() => props.modelValue, (newVal) => {
		    if (!quill) return
		    const editorHtml = editorRef.value.querySelector('.ql-editor').innerHTML
		    if (newVal !== editorHtml) {
		        const sel = quill.getSelection()
		        quill.clipboard.dangerouslyPasteHTML(newVal || '')
		        if (sel) quill.setSelection(sel)
		    }
	    })

	    return {
		    editorRef,
		    toolbarRef,
		    displayError,
		    showError,
	    }
  	},
}
</script>

<style scoped>
.quill-editor {
  min-height: 150px;
}

/* Toolbar active icon stroke color */
::v-deep(.ql-toolbar button.ql-active svg) {
  stroke: #06c;
}
/*::v-deep(.ql-stroke){
  fill: none;
  stroke: currentColor !important;;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-width: 2;
}*/

</style>