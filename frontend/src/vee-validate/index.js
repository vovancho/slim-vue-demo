import { required, email, min, max, oneOf } from 'vee-validate/dist/rules'
import {
  extend,
  localize,
  ValidationProvider,
  ValidationObserver
} from 'vee-validate'
import ru from 'vee-validate/dist/locale/ru.json'
import Vue from 'vue'

localize('ru', ru)

extend('required', { ...required })
extend('email', { ...email })
extend('min', { ...min })
extend('max', { ...max })
extend('oneOf', { ...oneOf })

Vue.component('ValidationProvider', ValidationProvider)
Vue.component('ValidationObserver', ValidationObserver)
