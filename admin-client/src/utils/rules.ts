const rules = {
  required: (message: string = "Поле обязательно") => ({
    required: true,
    message,
  }),
};

export default rules;
