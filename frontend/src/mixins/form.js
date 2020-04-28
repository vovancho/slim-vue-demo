export default {
  methods: {
    prepareForm(form) {
      const newForm = { ...form }
      Object.keys(newForm).forEach((attr) => {
        newForm[attr] = form[attr].value
      })
      return newForm
    },
    assignErrors(form, errors) {
      const newForm = { ...form }
      Object.keys(errors).forEach((attr) => {
        newForm[attr].error = errors[attr] || null
      })
      return newForm
    },
    mergeErrors(localErrors, serverError) {
      return localErrors.concat(serverError || [])
    }
  }
}
