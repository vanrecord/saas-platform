import './bootstrap';
import '../css/app.css';
import '../../node_modules/leaflet/dist/leaflet.js';
import '../../node_modules/leaflet/dist/leaflet.css';
import '../../node_modules/leaflet-geosearch/dist/geosearch.css';


import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { createI18n } from "vue-i18n";
import {lang} from "@/Lang"
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import 'vue-toast-notification/dist/theme-sugar.css'

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import '@vueform/toggle/themes/default.css';
import 'flowbite';
import 'flatpickr/dist/flatpickr.css';
import  VueHtmlToPaper from './plugins/VueHtmlToPaper'
import JsonExcel from "./plugins/jsonExcel";
import * as FilePond from 'filepond';
import FilePondPluginImageEdit from 'filepond-plugin-image-edit';
import 'filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css';
FilePond.registerPlugin(FilePondPluginImageEdit);
import {
  Head
} from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import _ from 'lodash';
import { Plugin } from '@flavorly/vanilla-components'

const i18n = createI18n({
  legacy: false,
  locale: "en",
  fallbackLocale: "en",
  fallbackRoot: "en",
  messages: lang,
});


const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
       const apps = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(i18n)
            .use(Plugin)
            .use(Toast)
            .use(VueSweetalert2)
            .use(VueHtmlToPaper)
            .use(JsonExcel)
            .component("Head",Head)
            .mixin({ methods: { route } })
            .mixin({methods:{
              can: function (permissions) {          
                var allPermissions = this.$page.props.auth.can;
                var hasPermission = false;
                permissions.forEach(function(item){
                  if(allPermissions['all'])  hasPermission = true;
                  if(allPermissions[item]) hasPermission = true;     
                });
                return hasPermission;
              },
              isSuperAdmin: function () {
                return this.$page.props.auth.isSuperAdmin
              },
              isAdmin: function () {
                return this.$page.props.auth.isAdmin
              }
            },});

            apps.config.globalProperties.$filters = {
              currency(value,code_currency, formate_value = false,decimals) {
                const deci = (typeof decimals === 'undefined') ? 2 : decimals
                let currency = _.find(apps.config.globalProperties.$page.props.currency,function (c) {return c.code ==code_currency;})
                let formatValue = 0
                if(formate_value == true){
                  formatValue = value
                }else{
                  let price= _.round(value,deci)
                  formatValue= this.number_format(price,deci,currency.decimal_place??'.',currency.thousands_sep??',');
                }
                return _.isEmpty(currency) ? formatValue : (_.isEmpty(currency.symbol_left)?formatValue+' '+(currency.symbol_right??''):(currency.symbol_left??'')+' '+formatValue) 
              },
              capitalize: function (value) {
                  if (!value) return '' 
                  value = value.toString()
                  return value.charAt(0).toUpperCase()+ value.slice(1)
              },
              number_format (number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
                var n = !isFinite(+number) ? 0 : +number
                const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
                const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
                const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
                let s = ''
                const toFixedFix = function (n, prec) {
                  if (('' + n).indexOf('e') === -1) {
                    return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
                  } else {
                    const arr = ('' + n).split('e')
                    let sig = ''
                    if (+arr[1] + prec > 0) {
                      sig = '+'
                    }
                    return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
                  }
                }
                // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
                if (s[0].length > 3) {
                  s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
                }
                if ((s[1] || '').length < prec) {
                  s[1] = s[1] || ''
                  // s[1] += new Array(prec - s[1].length + 1).join('0')
                  s[1] += new Array(prec - s[1].length + 1).join('')
                  if(s[1].length == 0) return s.join('')
                }
                return s.join(dec)
              },
              // Put the rest of your filters here
            // Put the rest of your filters here
        }
        apps.mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
