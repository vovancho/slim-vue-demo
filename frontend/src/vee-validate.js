import { required, email, min } from "vee-validate/dist/rules";
import { extend, localize } from "vee-validate";
// import en from 'vee-validate/dist/locale/en.json';
import ru from 'vee-validate/dist/locale/ru.json';

localize('ru', ru);

extend("required", {
  ...required
});

extend("email", {
  ...email
});

extend("min", {
  ...min
});
